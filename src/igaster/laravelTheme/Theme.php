<?php namespace igaster\laravelTheme;

use Illuminate\Support\Facades\Config;

class Theme {
	public $themeName;
    public $assetPath;
    public $viewsPath;
    public $parentTheme;

    public static $defaultViewsPath = '';

    /**
     *
     * @param string $themeName
     * @param array $options : ['assetPath' => xx, 'viewsPath' => xx, 'parentTheme' => xx]
     *
     */
    public function __construct($themeName, $options = []){

        if (empty(static::$defaultViewsPath))
            static::$defaultViewsPath = Config::get('view.paths')[0];

        $defaults = [
            'asset-path'  => $themeName,
            'views-path'  => $themeName,
            'extends'     => '',
        ];

        $options = array_merge($defaults, $options);

        $this->themeName    = $themeName;
        $this->assetPath    = $options['asset-path'];
        $this->viewsPath    = $options['views-path'];
        $this->parentTheme  = Themes::get($options['extends']);
    }

    public function url($url){
        if(preg_match('/^((http(s?):)?\/\/)/i',$url))
            return $url;

		$url = (empty($this->assetPath) ? '' : '/').$this->assetPath.'/'.ltrim($url, '/');

        if (!file_exists($fullPath = base_path('/public').$url))
            if (empty($this->parentTheme))
                throw new \Exception("$fullPath - not found");
            else
                return $this->parentTheme->path($url);

        return $url;
	}

    public function activate(){
        Config::set('view.paths', $this->pathsList_Views());
        Themes::set($this->themeName);
	}

	public function pathsList_Views(){
		if (!empty($this->parentTheme))
			$paths = $this->parentTheme->pathsList_Views();
		else
			$paths = [];

        array_unshift($paths,static::$defaultViewsPath.'/'.$this->viewsPath);
		return $paths;
	}

}