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

	// Add a new theme to the Tree
	public function add(Theme $theme, $parentName = ''){

		if ($parentName)
			$parentTheme = $this->find($parentName);
		else
			$parentTheme = $this->root;

		$theme->addParent($parentTheme);
		return $theme;
	}

	// find a Theme (by name)
    public function find($name){
        return $this->root->searchChild(function($item) use($name){
            if ($item->name == $name)
                return $item;
            else
                return false;
        });
    }

    public function exists($themeName){
    	return ($this->find($themeName)!==false);
    }

	// Set active theme (by name)
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

	// get active theme (name)
	public function get(){
		return $this->activeTheme ? $this->activeTheme->name : '';
	}

	// get current theme's configuration
	public function config($key){
		return $this->activeTheme->config($key);
	}

	public function url($url){
		if (Config::get('themes.enabled', true))
			return $this->activeTheme->url($url);
		else
			return $url;
	}

	//	Helper Functions (for Blade files)

	public function css($href){
		return '<link media="all" type="text/css" rel="stylesheet" href="'.$this->url($href).'">'."\n";
	}

	public function js($href){
		return '<script src="'.$this->url($href).'"></script>'."\n";
	}

	public function img($src, $alt='', $Class=''){
		return '<img src="'.$this->url($src).'" alt="'.$alt.'" class="'.$Class.'">'."\n";
	}	
}
