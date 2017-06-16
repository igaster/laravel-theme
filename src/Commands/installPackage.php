<?php namespace Igaster\LaravelTheme\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;

class installPackage extends abstractCommand
{
    protected $signature = 'theme:package:install {package?}';
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

        // Create Temp Folder. Clear it if exists.
        $this->createTempFolder();

        // Untar to temp folder
        exec("tar xzf $package -C {$this->tempPath}");

        // Read theme.json
        $json = file_get_contents("{$this->tempPath}/views/theme.json");
        $data = json_decode($json, true);

        // Check if theme is already installed
        $themeName = $data['name'];
        if(\Theme::exists($themeName)){
            $this->info('Warning: Theme '.$themeName.' already exist. You must remove it first with "artisan theme:remove '.$themeName.'"');
            $this->clearTempFolder();
            return;
        }

        // Target Paths
        $viewsPath = themes_path($data['views-path']);
        $assetPath = public_path($data['asset-path']);

        exec("mv {$this->tempPath}/views $viewsPath");
        exec("mv {$this->tempPath}/asset $assetPath");

        // Del Temp Folder
        exec("rm -r {$this->tempPath}");
    }




}
