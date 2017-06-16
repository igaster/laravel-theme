<?php namespace Igaster\LaravelTheme\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;

class removeTheme extends Command
{
    protected $signature = 'theme:remove {themeName?} {--force}';
    protected $description = 'Removes a theme';

    public function handle() {
        $themeName = $this->argument('themeName');
        $force = $this->option('force');

        if ($themeName == ""){
            $themes = array_map(function($theme){
                return $theme->name;
            }, \Theme::list());
            $themeName = $this->choice('Select a theme to create a distributable package:', $themes); 
        }

        if(!\Theme::exists($themeName)){
            $this->error("Error: Theme $themeName doesn't exist");
            return;
        }

        $theme = \Theme::find($themeName);

        $viewsPath = themes_path($theme->viewsPath);
        $assetPath = public_path($theme->assetPath);

        if(!$force){
            $this->info("Warning: These folders will be deleted:");
            $this->info("- views: $viewsPath");
            $this->info("- asset: $assetPath");
        }
        if($force || $this->confirm("Continue?")){
            exec("rm -r $viewsPath");
            exec("rm -r $assetPath");
            $this->info("Theme $themeName was removed");
        }

    }



}
