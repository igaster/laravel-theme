<?php namespace igaster\laravelTheme;

use Illuminate\Support\Facades\Config;

class Themes{

	public static $activeTheme = null;

	public static $root = null;

    private static $defaultViewsPath = '';

	// Setup Themes, must be called once from a Service Provider
    public static function boot(){
		static::$defaultViewsPath = Config::get('view.paths')[0];
		self::$root = new Theme('root','','');
    }



    private static $callback = null;
    public static function selectTheme($callback){
    	self::$callback = $callback;
    }

    public static function select(){
    	if(self::$callback)
    		static::set(self::$callback->__invoke());
    }

	// Add a new theme to the Tree
	public static function add(Theme $theme, $parentName = ''){

		if ($parentName)
			$parentTheme = self::find($parentName);
		else
			$parentTheme = self::$root;

		$theme->addParent($parentTheme);
		return $theme;
	}

	// find a Theme (by name)
    public static function find($name){
        return self::$root->searchChild(function($item) use($name){
            if ($item->name == $name)
                return $item;
            else
                return false;
        });
    }

	// Set active theme (by name)
	public static function set($themeName){
		$theme = self::find($themeName);
		Themes::$activeTheme = $theme;

		$paths = [];
		do {
			$paths[] = static::$defaultViewsPath.'/'.$theme->viewsPath;
		} while ($theme = $theme->getParent());

		Config::set('view.paths', $paths);
	}

	public static function url($url){
		return self::$activeTheme->url($url);
	}
}

//     // public static function add(Theme $theme){
//     //     return static::$ThemeCatalog[] = $theme;
//     // }

//     public static function add($themeName, $options = []){
//         return static::$ThemeCatalog[] = new Theme($themeName, $options);
//     }

//     public static function get($themeName){
//     	foreach (static::$ThemeCatalog as $theme) {
//     		if ($theme->themeName == $themeName)
//     			return $theme;
//     	}
//     	return null;
//     }

    // // Set active theme
    // public static function set($themeName){
    //     $theme = self::get($themeName);
    //     Config::set('view.paths', $theme->pathsList_Views());
    //     Themes::$activeTheme = $theme;        
    // }

//     // --- for use in Blade files ---

//     public static function url($url){
//         return static::$activeTheme->url($url);
// 	}

// 	public static function css($href){
// 		return '<link media="all" type="text/css" rel="stylesheet" href="'.static::url($href).'">'."\n";
// 	}

// 	public static function js($href){
// 		return '<script src="'.static::url($href).'"></script>'."\n";
// 	}

// 	public static function img($src, $alt='', $Class=''){
// 		return '<img src="'.static::url($src).'" alt="'.$alt.'" class="'.$Class.'">'."\n";
// 	}

// }