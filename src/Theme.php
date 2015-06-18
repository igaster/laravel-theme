<?php namespace igaster\laravelTheme;

class Theme extends Tree\Item {
    public $name;
    public $assetPath;
    public $viewsPath;

    public function __construct($name, $assetPath = null, $viewsPath = null){
        $this->name = $name;
        $this->assetPath = $assetPath === null ? $name : $assetPath;
        $this->viewsPath = $viewsPath === null ? $name : $viewsPath;
    }

    public function getParent(){
        if (!empty($this->parents))
            return $this->parents[0];
        else
            return null;
    }

    public function url($url){
        if(preg_match('/^((http(s?):)?\/\/)/i',$url))
            return $url;

        $fullUrl = (empty($this->assetPath) ? '' : '/').$this->assetPath.'/'.ltrim($url, '/');

        if (file_exists($fullPath = base_path('public').$fullUrl))
            return $fullUrl;

        if ($this->getParent())
            return $this->getParent()->url($url);
        
        //Asset not found
        $action = \Config::get('themes.asset_not_found','LOG_ERROR');

        if ($action == 'THROW_EXCEPTION')
            throw new themeException("Asset not found [$url]");
        elseif($action == 'LOG_ERROR')
            \Log::warning("Asset not found [$url] in Theme [".\Theme::get()."]");
    }

    // get theme's configuration
    public function config($key){
        if (array_key_exists($key, $conf = \Config::get("themes.themes")[$this->name]))
            return $conf[$key];

        if ($this->getParent())
            return $this->getParent()->config($key);

        return null;
    }


}


// use Illuminate\Support\Facades\Config;

// class Theme {
// 	public $themeName;
//     public $assetPath;
//     public $viewsPath;
//     public $parentTheme;

//     public static $defaultViewsPath = '';

//     /**
//      *
//      * @param string $themeName
//      * @param array $options : ['assetPath' => xx, 'viewsPath' => xx, 'extends' => xx]
//      *
//      */
//     public function __construct($themeName, $options = []){

//         if (empty(static::$defaultViewsPath))
//             static::$defaultViewsPath = Config::get('view.paths')[0];

//         $defaults = [
//             'asset-path'  => $themeName,
//             'views-path'  => $themeName,
//             'extends'     => '',
//         ];

//         $options = array_merge($defaults, $options);

//         $this->themeName    = $themeName;
//         $this->assetPath    = $options['asset-path'];
//         $this->viewsPath    = $options['views-path'];
//         $this->parentTheme  = Themes::get($options['extends']);
//     }

//     public function url($url){
//         if(preg_match('/^((http(s?):)?\/\/)/i',$url))
//             return $url;

// 		$fullUrl = (empty($this->assetPath) ? '' : '/').$this->assetPath.'/'.ltrim($url, '/');

//         if (!file_exists($fullPath = base_path('public').$fullUrl))
//             if (empty($this->parentTheme))
//                 throw new \Exception("$fullPath - not found");
//             else
//                 return $this->parentTheme->url($url);

//         return $fullUrl;
// 	}

//     public function activate(){
//         Config::set('view.paths', $this->pathsList_Views());
//         Themes::set($this->themeName);
// 	}

// 	public function pathsList_Views(){
// 		if (!empty($this->parentTheme))
// 			$paths = $this->parentTheme->pathsList_Views();
// 		else
// 			$paths = [];

//         array_unshift($paths,static::$defaultViewsPath.'/'.$this->viewsPath);
// 		return $paths;
// 	}

// }