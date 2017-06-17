<?php namespace Igaster\LaravelTheme;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

class Themes{

    protected $themesPath;
    protected $activeTheme = null;
    protected $themes = [];
    protected $laravelViewsPath;

    public function __construct(){
        $this->laravelViewsPath = Config::get('view.paths');
        $this->themesPath = Config::get('themes.themes_path', null) ?: Config::get('view.paths')[0];
    }

    /**
     * Return $filename path located in themes folder
     *
     * @param  string $filename
     * @return string
     */
    public function themes_path($filename = null){
        return $filename ? $this->themesPath.'/'.$filename : $this->themesPath;
    }

    /**
     * Return list of registered themes
     * 
     * @return array
     */
    public function list(){
        return $this->themes;
    }

    /**
     * Check if @themeName is registered
     * 
     * @return bool
     */
    public function exists($themeName){
        foreach($this->themes as $theme){
            if($theme->name == $themeName)
                return true;
        }
        return false;
    }

    /**
     * Enable $themeName & set view paths
     * 
     * @return Theme
     */
    public function set($themeName){
        if($this->exists($themeName)){
            $theme = $this->find($themeName);
        } else {
            $theme = new Theme($themeName);
        }

        $this->activeTheme = $theme;

        // Get theme view paths
        $paths = $theme->getViewPaths();

        // fall-back to default paths (set in views.php config file)
        foreach ($this->laravelViewsPath as $path)
            if(!in_array($path, $paths))
                $paths[] = $path;

        Config::set('view.paths', $paths);

        $themeViewFinder = app('view.finder');
        $themeViewFinder->setPaths($paths);

        \Event::fire('igaster.laravel-theme.change', $theme);
        return $theme;
    }

    /**
     * Get current theme
     * 
     * @return Theme
     */
    public function current(){
        return $this->activeTheme ? $this->activeTheme : null;
    }

    /**
     * Get current theme's name
     * 
     * @return string
     */
    public function get(){
        return $this->current() ? $this->current()->name : '';
    }

    /**
     * Find a theme by it's name
     * 
     * @return Theme
     */
    public function find($themeName){
        // Search for registered themes
        foreach($this->themes as $theme){
            if($theme->name == $themeName)
                return $theme;
        }

        throw new Exceptions\themeNotFound($themeName);
    }

    /**
     * Register a new theme
     * 
     * @return Theme
     */
    public function add(Theme $theme){
        if($this->exists($theme->name)){
            throw new Exceptions\themeAlreadyExists($theme);
        }
        $this->themes[] = $theme;
        return $theme;
    }

    public function url($url){
        // If no Theme set, $url
        if (!$this->current())
            return "/".ltrim($url, '/');

        return $this->current()->url($url);
    }

    public function getLaravelViewPaths(){
        return $this->laravelViewsPath;
    }

    /**
     * Scan all folders inside the themes path & config/themes.php 
     * If a "theme.json" file is found then load it and setup theme
     */
    public function scanThemes(){
        $parentThemes = [];
        $themesConfig = config('themes.themes');
        foreach (glob($this->themes_path('*'),GLOB_ONLYDIR) as $themeFolder) {
            if(file_exists($jsonFilename = $themeFolder.'/'.'theme.json')){

                $folders = explode(DIRECTORY_SEPARATOR,$themeFolder);
                $folderName = end($folders);

                // default theme settings
                $defaults = [
                    'name'          => $folderName,
                    // 'views-path'    => null,
                    'asset-path'    => null,
                    'extends'       => null,
                ];

                // If theme.json is not an empty file parse json values
                $json = file_get_contents($jsonFilename);
                if($json !== ""){
                    $data = json_decode($json, true);
                    if($data===null){
                        throw new \Exception("Invalid theme.json file at [$themeFolder]");
                    }
                } else {
                    $data = [];
                }

                // We already know views-path since we have scaned folders.
                $data['views-path'] = $folderName;

                $data = array_merge($defaults,$data);

                // Are theme settings overriden in config/themes.php?
                if(array_key_exists($data['name'], $themesConfig)){
                    $data = array_merge($data, $themesConfig[$data['name']]);
                }

                // Create theme
                $theme = new Theme(
                    $data['name'],
                    $data['asset-path'],
                    $data['views-path']
                );

                // Has a parent theme? Store parent name to resolve later.
                if($data['extends']){
                    $parentThemes[$theme->name] = $data['extends'];
                }

                // Load the rest of the values as theme Settings
                $theme->loadSettings($data);
            }
        }


        // Add themes from config/themes.php
        foreach ($themesConfig as $themeName => $themeConfig) {
            
            // Is it an element with no values?
            if(is_string($themeConfig)){
                $themeName = $themeConfig;
                $themeConfig = [];
            }

            // Create new or Update existing?
            if(!$this->exists($themeName)){
                $theme = new Theme($themeName);
            } else {
                $theme = $this->find($themeName);
            }

            // Load Values from config/themes.php
            if(isset($themeConfig['asset-path'])){
                $theme->assetPath = $themeConfig['asset-path'];
            }

            if(isset($themeConfig['views-path'])){
                $theme->viewsPath = $themeConfig['views-path'];
            }

            if(isset($themeConfig['extends'])){
                $parentThemes[$themeName] = $themeConfig['extends'];
            }

            $theme->loadSettings(array_merge($theme->settings, $themeConfig));
        }

        // All themes are loaded. Now we can assign the parents to the child-themes
        foreach ($parentThemes as $childName => $parentName) {
            $child = $this->find($childName);

            if(\Theme::exists($parentName)){
                $parent = $this->find($parentName);
            } else {
                $parent = new Theme($parentName);
            }
            
            $child->setParent($parent);
        }
    }

    /*--------------------------------------------------------------------------
    | Blade Helper Functions
    |--------------------------------------------------------------------------*/

    /**
     * Return css link for $href
     *
     * @param  string $href
     * @return string
     */
    public function css($href){
        return sprintf('<link media="all" type="text/css" rel="stylesheet" href="%s">',$this->url($href));
    }

    /**
     * Return script link for $href
     *
     * @param  string $href
     * @return string
     */
    public function js($href){
        return sprintf('<script src="%s"></script>',$this->url($href)); 
    }

    /**
     * Return img tag
     *
     * @param  string $src
     * @param  string $alt
     * @param  string $Class
     * @param  array  $attributes
     * @return string
     */
    public function img($src, $alt='', $class='', $attributes=array()){
        return sprintf('<img src="%s" alt="%s" class="%s" %s>',
            $this->url($src),
            $alt,
            $class,
            $this->HtmlAttributes($attributes)
        );
    }
    
    /**
     * Return attributes in html format
     *
     * @param  array $attributes
     * @return string
     */
    private function HtmlAttributes($attributes){
        $formatted = join(' ', array_map(function($key) use ($attributes){
           if(is_bool($attributes[$key])){
              return $attributes[$key]?$key:'';
           }
           return $key.'="'.$attributes[$key].'"';
        }, array_keys($attributes)));
        return $formatted;
    }
 

    /**
     * Act as a proxy to the current theme. Map theme's functions to the Themes class
     */
    public function __call($method, $args) {
        if (($theme=$this->current()) && method_exists($theme, $method)){
            return call_user_func_array(array($theme, $method), $args);
        } else {
            throw new \Exception("Method [$method] not defined in [".self::class."] or [".Theme::class."] classes", 1);
        }
    }   
}