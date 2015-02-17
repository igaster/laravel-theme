<?php namespace igaster\laravelTheme;

use Illuminate\Support\Facades\Config;

class Themes {

    public static $ThemeCatalog = [];
    public static $activeTheme = null;

    // public static function add(Theme $theme){
    //     return static::$ThemeCatalog[] = $theme;
    // }

    public static function add($themeName, $options = []){
        return static::$ThemeCatalog[] = new Theme($themeName, $options);
    }

    public static function get($themeName){
    	foreach (static::$ThemeCatalog as $theme) {
    		if ($theme->themeName == $themeName)
    			return $theme;
    	}
    	return null;
    }

    // Set active theme
    public static function set($themeName){
        $theme = self::get($themeName);
        Config::set('view.paths', $theme->pathsList_Views());
        Themes::$activeTheme = $theme;        
    }

    // --- for use in Blade files ---

    public static function url($url){
        return static::$activeTheme->url($url);
	}

	public static function css($href){
		return '<link media="all" type="text/css" rel="stylesheet" href="'.static::url($href).'">'."\n";
	}

	public static function js($href){
		return '<script src="'.static::url($href).'"></script>'."\n";
	}

	public static function img($src, $alt='', $Class=''){
		return '<img src="'.static::url($src).'" alt="'.$alt.'" class="'.$Class.'">'."\n";
	}

}