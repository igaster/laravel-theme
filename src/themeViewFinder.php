<?php namespace igaster\laravelTheme;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\FileViewFinder;
use Illuminate\Support\Arr;

class themeViewFinder extends FileViewFinder {

    /** @var  Themes */
    protected $themeEngine;

    public function __construct(Filesystem $files, array $paths, array $extensions = null, $themeEngine)
    {
        $this->themeEngine = $themeEngine;
        parent::__construct($files, $paths, $extensions);
    }


    /**
     * Add support for vendor named path overrides.
     *
     * @param  string  $name
     * @return string
     */
    protected function findNamedPathView($name)
    {
        list($namespace, $view) = $this->getNamespaceSegments($name);

        $namespaceOverrides = $this->themeEngine->config('namespace-overrides', []);
        if (in_array($namespace, array_keys($namespaceOverrides))) {
            if ($namespaceOverrides[$namespace] === '') {
                $vendorPath = $this->paths[0];
            } else {
                $vendorPath = $this->paths[0] . DIRECTORY_SEPARATOR . $namespaceOverrides[$namespace];
            }
        }

        if (!isset($vendorPath) || !$this->files->isDirectory($vendorPath)) {
            $vendorPath = $this->paths[0] . '/vendor/' . $namespace;
        }

        $hints = $this->hints[$namespace];

        if (!in_array($vendorPath, $hints) && $this->files->isDirectory($vendorPath)) {
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
