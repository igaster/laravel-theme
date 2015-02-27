<?php namespace igaster\laravelTheme;

use Illuminate\Support\Facades\Config;

class Themes{

	public  $activeTheme = null;

	public  $root = null;

    private  $defaultViewsPath = '';

    public function __construct(){
		$this->defaultViewsPath = Config::get('view.paths')[0];
		
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

	// Set active theme (by name)
	public function set($themeName){
		$theme = $this->find($themeName);
		$this->activeTheme = $theme;

		$paths = [];
		do {
			$paths[] = $this->defaultViewsPath.'/'.$theme->viewsPath;
		} while ($theme = $theme->getParent());

		Config::set('view.paths', $paths);
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
		return $this->activeTheme->url($url);
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
