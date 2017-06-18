<?php namespace Igaster\LaravelTheme\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;

class removeTheme extends baseCommand
{
    protected $signature = 'theme:remove {themeName?} {--force}';
    protected $description = 'Removes a theme';

    public function handle() {
        // Get theme name
        $themeName = $this->argument('themeName');
        if ($themeName == ""){
            $themes = array_map(function($theme){
                return $theme->name;
            }, \Theme::list());
            $themeName = $this->choice('Select a theme to create a distributable package:', $themes); 
        }

        // Remove without confirmation?
        $force = $this->option('force');

        // Check that theme exists
        if(!\Theme::exists($themeName)){
            $this->error("Error: Theme $themeName doesn't exist");
            return;
        }

        // Get the theme
        $theme = \Theme::find($themeName);

        // Diaplay Warning
        if(!$force){
            $viewsPath = themes_path($theme->viewsPath);
            $assetPath = public_path($theme->assetPath);

            $this->info("Warning: These folders will be deleted:");
            $this->info("- views: $viewsPath");
            $this->info("- asset: $assetPath");

            if(!$this->confirm("Continue?"))
                return;
        }

        // Delete folders
        $theme->uninstall();
        $this->info("Theme $themeName was removed");

    }



}
