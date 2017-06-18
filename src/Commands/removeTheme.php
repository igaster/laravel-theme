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

        // Calculate absolute paths
        $viewsPath = themes_path($theme->viewsPath);
        $assetPath = public_path($theme->assetPath);

        // Check that paths exist
        $delViews = $this->files->exists($viewsPath);
        $delAsset = $this->files->exists($assetPath);

        // Check that no other theme uses to the same paths (ie a child theme)
        foreach (\Theme::list() as $t) {
            if ($t !== $theme && $t->viewsPath == $theme->viewsPath)
                $delViews = false;

            if ($t !== $theme && $t->assetPath == $theme->assetPath)
                $delAsset = false;
        }

        // Diaplay Warning
        if(!$force){
            $this->info("Warning: These folders will be deleted:");
            $this->info($delViews ? "- views: $viewsPath" : "- views path [$viewsPath] not found or used by another theme. Will not delete!");
            $this->info($delAsset ? "- asset: $assetPath" : "- asset path [$assetPath] not found or used by another theme. Will not delete!");
        }

        // Delete folders
        if($force || $this->confirm("Continue?")){
            $this->files->deleteDirectory($viewsPath);
            $this->files->deleteDirectory($assetPath);

            // Rebuild Themes Cache
            \Theme::rebuildCache();
        
            $this->info("Theme $themeName was removed");
        }

    }



}
