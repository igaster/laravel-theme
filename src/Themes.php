<?php namespace igaster\laravelTheme;

use Illuminate\Support\Facades\Config;

class Themes{

	public  $activeTheme = null;

	public  $root = null;

    private  $defaultViewsPath;

    public function __construct(){
		$this->defaultViewsPath = Config::get('view.paths');
		
		$this->root = new Theme('root','','');
    }

    /**
     * Add a new theme to the hierarcy. Optionaly define a parent theme.
     *
     * @param   \igaster\laravelTheme\Theme $theme
     * @param   string $parentName
     * @return  \igaster\laravelTheme\Theme
     */
	public function add(Theme $theme, $parentName = ''){

		if ($parentName)
			$parentTheme = $this->find($parentName);
		else
			$parentTheme = $this->root;

		$theme->addParent($parentTheme);
		return $theme;
	}

    /**
     * Find a Theme (by name)
     *
     * @param   string $themeName
     * @return  \igaster\laravelTheme\Theme
     */
    public function find($themeName){
        return $this->root->searchChild(function($item) use($themeName){
            if ($item->name == $themeName)
                return $item;
            else
                return false;
        });
    }

    /**
     * Check if $themeName is a valid Theme
     *
     * @param   string $themeName
     * @return  bool
     */
    public function exists($themeName){
    	return ($this->find($themeName)!==false);
    }

    /**
     * Set $themeName is the active Theme
     *
     * @param   string $themeName
     * @return  void
     */
	public function set($themeName){
		if (!Config::get('themes.enabled', true))
			return;
		
		if (!$theme = $this->find($themeName))
			$theme = $this->add(new Theme($themeName));

		$this->activeTheme = $theme;

		// Build Paths array. 
		// All paths are relative first entry in 'paths' array (set in views.php config file)
		$paths = [];
		do {
			$path = $this->defaultViewsPath[0];
			$path .= empty($theme->viewsPath) ? '' : '/'.$theme->viewsPath;
			if(!in_array($path, $paths))
				$paths[] = $path;
		} while ($theme = $theme->getParent());

		// fall-back to default paths (set in views.php config file)
		foreach ($this->defaultViewsPath as $path)
			if(!in_array($path, $paths))
				$paths[] = $path;

		Config::set('view.paths', $paths);
	
		$themeViewFinder = app('view.finder');
		$themeViewFinder->setPaths($paths);
	}

    /**
     * Get  active's Theme Name
     *
     * @return  string
     */
	public function get(){
		return $this->activeTheme ? $this->activeTheme->name : '';
	}

    /**
     * Return current theme's configuration value for $key
     *
     * @param   string $key
     * @return  mixed
     */
	public function config($key, $defaultValue = null){
		return $this->activeTheme->config($key, $defaultValue);
	}

    /**
     * Set current theme's configuration value for $key
     *
     * @param   string $key
     * @return  mixed
     */
    public function configSet($key, $value){
        return $this->activeTheme->configSet($key, $value);
    }


    /**
     * Attach current theme's paths to a local Url. The Url must be a resource located on the asset path
     * of the current theme or it's parents. 
     *
     * @param  string $url
     * @return string
     */
	public function url($url){
		if (Config::get('themes.enabled', true)){

            // Check for valid {xxx} keys and replace them with the Theme's configuration value (in themes.php)
            preg_match_all('/\{(.*?)\}/', $url, $matches);
            foreach ($matches[1] as $param)
                if(($value=$this->config($param)) !== null)
                    $url = str_replace('{'.$param.'}', $value, $url);

			return $this->activeTheme->url($url);
        }
		else
			return $url;
	}

	//---------------- Helper Functions (for Blade files) -------------------------

    /**
     * Return css link for $href
     *
     * @param  string $href
     * @return string
     */
	public function css($href){
		return '<link media="all" type="text/css" rel="stylesheet" href="'.$this->url($href).'">';
	}

    /**
     * Return script link for $href
     *
     * @param  string $href
     * @return string
     */
	public function js($href){
		return '<script src="'.$this->url($href).'"></script>';
	}

    /**
     * Return img tag
     *
     * @param  string $src
     * @param  string $alt
     * @param  string $Class
     * @return string
     */
	public function img($src, $alt='', $Class=''){
		return '<img src="'.$this->url($src).'" alt="'.$alt.'" class="'.$Class.'">';
	}
}
