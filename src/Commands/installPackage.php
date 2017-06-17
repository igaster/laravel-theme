<?php namespace Igaster\LaravelTheme\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;

class installPackage extends baseCommand
{
    protected $signature = 'theme:install {package?}';
    protected $description = 'Install a theme package';

    public function handle() {
        $package = $this->argument('package');

        if(!$package){
            $filenames = glob($this->packages_path('*.theme.tar.gz'));
            $packages = array_map(function($filename){
                return basename($filename, '.theme.tar.gz');
            }, $filenames);
            $package = $this->choice('Select a theme to install:', $packages);        
        }
        $package = $this->packages_path($package.'.theme.tar.gz');

        // Create Temp Folder
        $this->createTempFolder();

        // Untar to temp folder
        exec("tar xzf $package -C {$this->tempPath}");

        // Read theme.json
        $json = file_get_contents("{$this->tempPath}/views/theme.json");
        $data = json_decode($json, true);

        // Check if theme is already installed
        $themeName = $data['name'];
        if($this->theme_installed($themeName)){
            $this->error('Error: Theme '.$themeName.' already exist. You must remove it first with "artisan theme:remove '.$themeName.'"');
            $this->clearTempFolder();
            return;
        }

        // Target Paths
        $viewsPath = themes_path($data['views-path']);
        $assetPath = public_path($data['asset-path']);

        // If Views+Asset paths don't exist, move theme from temp to target paths
        if (file_exists($viewsPath)){
            $this->info("Warning: Views path [$viewsPath] already exists. Will not be installed.");
        } else {
            exec("mv {$this->tempPath}/views $viewsPath");
            $this->info("Theme views installed to path [$viewsPath]");
        }
        
        if (file_exists($assetPath)){
            $this->error("Error: Asset path [$assetPath] already exists. Will not be installed.");
        } else {
            exec("mv {$this->tempPath}/asset $assetPath");
            $this->info("Theme assets installed to path [$assetPath]");
        }

        // Del Temp Folder
        $this->clearTempFolder();
    }




}
