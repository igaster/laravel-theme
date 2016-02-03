<?php namespace igaster\laravelTheme;

use Illuminate\View\FileViewFinder;
use Illuminate\Support\Arr;

class themeViewFinder extends FileViewFinder {

    /**
     * Add support for vendor namded path overrides.
     *
     * @param  string  $name
     * @return string
     */
    protected function findNamedPathView($name)
    {
        list($namespace, $view) = $this->getNamespaceSegments($name);

        $vendorPath = $this->paths[0] . '/vendor/' . $namespace;
        $hints = $this->hints[$namespace];

        if (!Arr::has($hints, $vendorPath)) {
            $this->hints[$namespace] = Arr::prepend($hints, $vendorPath);
        }

        return $this->findInPaths($view, $this->hints[$namespace]);
    }

    /**
     * Set the array of active view paths.
     *
     * @param  array  $paths
     */
	public function setPaths($paths){
		$this->paths = $paths;
	}

}