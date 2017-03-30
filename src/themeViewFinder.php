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


    /*
     * Intersect findNamespacedView() to add Theme/vendor/....
     * Laravel >= 5.4
     *
     * @param  string  $name
     * @return string
     */
    protected function findNamespacedView($name)
    {
        list($namespace, $view) = $this->parseNamespaceSegments($name);
        $this->addThemeInNamespacedViews($namespace, $view);
        return $this->findInPaths($view, $this->hints[$namespace]);
    }

    /**
     * Intersect findNamespacedView() to add Theme/vendor/....
     * Laravel <= 5.3 - Replaced by findNamespacedView() in 5.4
     *
     * @param  string  $name
     * @return string
     */
    protected function findNamedPathView($name)
    {
        list($namespace, $view) = $this->getNamespaceSegments($name);
        $this->addThemeInNamespacedViews($namespace, $view);
        return $this->findInPaths($view, $this->hints[$namespace]);
    }

    /**
     * Inject theme paths into namespaced views
     *
     * @param  string  $name
     * @return string
     */
    protected function addThemeInNamespacedViews($namespace, $view)
    {
        $rootVendors = $this->themeEngine->config('vendor-as-root', []);

        if(in_array($namespace, $rootVendors)){
            $vendorPath = $this->paths[0];
        } else {
            $vendorPath = $this->paths[0] . '/vendor/' . $namespace;
        }

        $hints = $this->hints[$namespace];

        if (!Arr::has($hints, $vendorPath) && $this->files->isDirectory($vendorPath)) {
            $this->hints[$namespace] = Arr::prepend($hints, $vendorPath);
        }
    }


    /**
     * Intersect replaceNamespace() to add path for custom error pages from themes...
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function replaceNamespace($namespace, $hints)
    {
        $this->hints[$namespace] = (array) $hints;
        if($namespace == 'errors')
            $this->setupErrorViews();
    }

    public function setupErrorViews() {

        $errorPaths = array_map(function($path){
            return "$path/errors";
        },$this->paths);

        $this->prependNamespace('errors',$errorPaths);
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
