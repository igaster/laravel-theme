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

	public function url($url){
		return $this->activeTheme->url($url);
	}
}
