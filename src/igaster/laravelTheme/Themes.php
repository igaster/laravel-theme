<?php namespace igaster\Theme;

// Themes Catalog / Static

class Themes {

    public static $ThemeCatalog = [];
    public static $activeTheme = null;

    public static function add($themeName, $options= []){
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
        self::get($themeName)->activate();
    }

    // --- for use in Blade files ---
    public static function path($url){
        return static::$activeTheme->path($url);
	}

	public static function css($href){
		return '<link media="all" type="text/css" rel="stylesheet" href="'.static::path($href).'">'."\n";
	}

	public static function js($href){
		return '<script src="'.static::path($href).'"></script>'."\n";
	}

	public static function img($src, $alt='', $Class=''){
		return '<img src="'.static::path($src).'" alt="'.$alt.'" class="'.$Class.'">'."\n";
	}

}