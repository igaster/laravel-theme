<?php namespace Igaster\LaravelTheme\Commands;

use Illuminate\Console\Command;

class createTheme extends Command
{
    protected $signature = 'theme:create {themeName?}';
    protected $description = 'Create a new theme';

    public function info($text,$newline = true){
        $this->output->write("<info>$text</info>", $newline);        
    }

    public function handle() {
        $themeName = $this->argument('themeName');

        if(!$themeName){
            $themeName = $this->ask('Give theme name:');
        }

        $viewsPath = $this->anticipate("Where will views be located [Default='$themeName']?",[$themeName]);
        $assetPath = $this->anticipate("Where will assets be located [Default='$themeName']?",[$themeName]);

        $viewsPathFull = themes_path($viewsPath);
        $assetPathFull = public_path($assetPath);

        $parentTheme = "";
        if ($this->confirm('Extends an other theme?')){
            $themes = array_map(function($theme){
                return $theme->name;
            }, \Theme::list());
            $parentTheme = $this->choice('Which one', $themes);        
        }

        $this->info("Summary:");
        $this->info("- Theme name: ".$themeName);
        $this->info("- Views Path: ".$viewsPathFull);
        $this->info("- Asset Path: ".$assetPathFull);
        $this->info("- Extends Theme: ".($parentTheme ?: "No"));

        if ($this->confirm('Create Theme?', true)){

            // Create theme.json
            $templateFile = (dirname(__FILE__)."/template/theme.json");
            $template = file_get_contents($templateFile);

            $replace = [
                '[THEME_NAME]'      =>  $themeName,
                '[VIEWS_PATH]'      =>  $viewsPath,
                '[ASSET_PATH]'      =>  $assetPath,
                '[THEME_PARENT]'    =>  $parentTheme,
            ];
            $template = str_replace(array_keys($replace), array_values($replace), $template);

            // Create Paths
            system("mkdir $viewsPathFull");
            system("mkdir $assetPathFull");
            file_put_contents(themes_path("$viewsPath/theme.json"),$template);

        }
    }




}
