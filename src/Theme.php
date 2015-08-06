<?php namespace igaster\laravelTheme;

class Theme extends Tree\Item {
    public $name;
    public $assetPath;
    public $viewsPath;

    public function __construct($themeName, $assetPath = null, $viewsPath = null){
        $this->name = $themeName;
        $this->assetPath = $assetPath === null ? $themeName : $assetPath;
        $this->viewsPath = $viewsPath === null ? $themeName : $viewsPath;
    }

    public function getParent(){
        if (!empty($this->parents))
            return $this->parents[0];
        else
            return null;
    }


    /**
     * Attach theme paths to a local Url. The Url must be a resource located on the asset path
     * of the current theme or it's parents. 
     *
     * @param  string $url
     * @return string
     */
    public function url($url){
        // return external URLs unmodified
        if(preg_match('/^((http(s?):)?\/\/)/i',$url))
            return $url;

        // Is it on AWS? Dont lookup parent themes...
        if(preg_match('/^((http(s?):)?\/\/)/i',$this->assetPath))
            return $this->assetPath.'/'.ltrim($url, '/');

        // Lookup asset in current's theme asset path
        $fullUrl = (empty($this->assetPath) ? '' : '/').$this->assetPath.'/'.ltrim($url, '/');

        if (file_exists($fullPath = public_path($fullUrl)))
            return $fullUrl;

        // If not found then lookup in parent's theme asset path
        if ($this->getParent())
            return $this->getParent()->url($url);
        
        // Asset not found at all. Error handling
        $action = \Config::get('themes.asset_not_found','LOG_ERROR');

        if ($action == 'THROW_EXCEPTION')
            throw new themeException("Asset not found [$url]");
        elseif($action == 'LOG_ERROR')
            \Log::warning("Asset not found [$url] in Theme [".\Theme::get()."]");
    }

    /**
     * Return the configuration value of $key for the current theme. Configuration values
     * are stored per theme in themes.php config file. 
     *
     * @param  string $key
     * @return mixed
     */
    public function config($key){
        //root theme not have configs
        if(array_key_exists($this->name, $confs = \Config::get("themes.themes")))
        {
            if (array_key_exists($key, $conf = $confs[$this->name]))
                return $conf[$key];
        }
        if ($this->getParent())
            return $this->getParent()->config($key);

        return null;
    }


}
