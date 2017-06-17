<?php namespace Igaster\LaravelTheme\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;

class baseCommand extends Command
{

    public function __construct() {
        parent::__construct();
        $this->tempPath = $this->packages_path('tmp');
    }

    protected function createTempFolder(){
        $this->clearTempFolder();
        mkdir($this->tempPath);
    }

    protected function clearTempFolder(){
        if (file_exists($this->tempPath)){
            exec("rm -r $this->tempPath");
        }
    }

    protected function packages_path($path=''){
        return storage_path("themes/$path");
    }

    protected function theme_installed($themeName){
        if(!\Theme::exists($themeName)){
            return false;
        }

        $viewsPath = \Theme::find($themeName)->viewsPath;
        return file_exists(themes_path("$viewsPath/theme.json"));
    }
}
