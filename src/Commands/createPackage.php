<?php namespace Igaster\LaravelTheme\Commands;

use Illuminate\Console\Command;
use Igaster\LaravelTheme\Facades\Theme;
use Illuminate\Support\Facades\File;  

class createPackage extends baseCommand
{
    protected $signature = 'theme:package {themeName?}';
    protected $description = 'Create a theme package';

    public function handle()
    {
        // Check if tar exists
        exec("tar --version", $output, $status);

        if($status !== 0 ) {

            $this->info('Error: tar executable could not be found. Please install tar utility before you can continue');

            return;

        }

        $themeName = $this->argument('themeName');

        if ($themeName == "") {

            $themes = array_map(function ($theme) {

                return $theme->name;

            }, Theme::all());

            if(empty($themes)){

                $this->info("No themes found");

                return;

            }

            $themeName = $this->choice('Select a theme to create a distributable package:', $themes);
        }

        $theme = Theme::find($themeName);

        $viewsPath = themes_path($theme->viewsPath);

        $assetPath = public_path($theme->assetPath);

        // Packages storage path
        $packagesPath = $this->packages_path();

        if (!$this->files->exists($packagesPath)){
            mkdir($packagesPath);
        }

        // Sanitize target filename
        $packageFileName = $theme->name;

        $packageFileName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $packageFileName);

        $packageFileName = mb_ereg_replace("([\.]{2,})", '', $packageFileName);

        $packageFileName = $this->packages_path("{$packageFileName}.theme.tar.gz");

        // Create Temp Folder
        $this->createTempFolder();

        // Copy Views+Assets to Temp Folder
        File::copyDirectory($viewsPath, "{$this->tempPath}/views");

        File::copyDirectory($assetPath, "{$this->tempPath}/asset");

        // Add viewsPath into theme.json file
        $themeJson = new \Igaster\LaravelTheme\themeManifest();

        $themeJson->loadFromFile("{$this->tempPath}/views/theme.json");

        $themeJson->set('views-path', $theme->viewsPath);

        $themeJson->saveToFile("{$this->tempPath}/views/theme.json");

        // Tar Temp Folder contents
        system("cd {$this->tempPath} && tar -cvzf $packageFileName .");

        // Del Temp Folder
        $this->clearTempFolder();

        $this->info("Package created at [$packageFileName]");
    }


}
