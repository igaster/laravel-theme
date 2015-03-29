<?php namespace igaster\laravelTheme;

use Illuminate\View\FileViewFinder;

class themeViewFinder extends FileViewFinder {

	public function setPaths($paths){
		$this->paths = $paths;
	}

	public function test(){
		dd($this->paths);
	}

}