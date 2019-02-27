<?php namespace Igaster\LaravelTheme\Commands;

use Igaster\LaravelTheme\Facades\Theme;
use Igaster\LaravelTheme\themeManifest;

class installPackage extends baseCommand
{
    protected $signature = 'theme:install {package?}';
    protected $description = 'Install a theme package';

    public function handle()
    {
        $package = $this->argument('package');

        if (!$package) {

            $filenames = $this->files->glob($this->packages_path('*.theme.tar.gz'));

            $packages = array_map(function ($filename) {

                return basename($filename, '.theme.tar.gz');

            }, $filenames);

            if(empty($packages)){

                $this->info("No theme packages found to install at ".$this->packages_path());

            }

            $package = $this->choice('Select a theme to install:', $packages);
        }

        $package = $this->packages_path($package . '.theme.tar.gz');

        // Create Temp Folder
        $this->createTempFolder();

        // Untar to temp folder
        exec("tar xzf $package -C {$this->tempPath}");

        // Read theme.json
        $themeJson = new themeManifest();

        $themeJson->loadFromFile("{$this->tempPath}/views/theme.json");

        // Check if theme is already installed
        $themeName = $themeJson->get('name');
        if ($this->theme_installed($themeName)) {

            $this->error('Error: Theme ' . $themeName . ' already exist. You must remove it first with "artisan theme:remove ' . $themeName . '"');

            $this->clearTempFolder();

            return;
        }

        // Target Paths
        $viewsPath = themes_path($themeJson->get('views-path'));
        $assetPath = public_path($themeJson->get('asset-path'));

        // If Views+Asset paths don't exist, move theme from temp to target paths
        if (file_exists($viewsPath)) {
            $this->info("Warning: Views path [$viewsPath] already exists. Will not be installed.");
        } else {
            exec("mv {$this->tempPath}/views $viewsPath");

            // Remove 'theme-views' from theme.json
            $themeJson->remove('views-path');

            $themeJson->saveToFile("$viewsPath/theme.json");

            $this->info("Theme views installed to path [$viewsPath]");
        }

        if (file_exists($assetPath)) {
            $this->error("Error: Asset path [$assetPath] already exists. Will not be installed.");
        } else {
            exec("mv {$this->tempPath}/asset $assetPath");
            $this->info("Theme assets installed to path [$assetPath]");
        }

        // Rebuild Themes Cache
        Theme::rebuildCache();

        // Del Temp Folder
        $this->clearTempFolder();
    }

}
