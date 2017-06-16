<?php namespace Igaster\LaravelTheme;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\FileViewFinder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class themeViewFinder extends FileViewFinder {

    public function __construct(Filesystem $files, array $paths, array $extensions = null)
    {
        $this->themeEngine = \App::make('igaster.themes');
        parent::__construct($files, $paths, $extensions);
    }

    /*
     * Override findNamespacedView() to add "Theme/vendor/..." paths
     *
     * @param  string  $name
     * @return string
     */
    protected function findNamespacedView($name)
    {
        // Extract the $view and the $namespace parts
        list($namespace, $view) = $this->parseNamespaceSegments($name);

        $paths = $this->addThemeNamespacePaths($namespace);

        // Find and return the view
        return $this->findInPaths($view, $paths);
    }

    public function addThemeNamespacePaths($namespace){
        // This rule will remap all paths starting with $key to $value.
        // For exapmle paths starting with 'resources/views/vendor' (relative to base_path())
        // will be maped to path 'THEMENAME/vendor' (relative to current theme views-path)
        $pathsMap = [
            // 'resources/views/vendor/mail' => 'mail',
            'resources/views/vendor' => 'vendor',
        ];

        // Does $namespace exists?
        if(!isset($this->hints[$namespace])){
            return [];
        }

        // Get the paths registered to the $namespace
        $paths = $this->hints[$namespace];

        // Search $paths array and remap paths that start with a key of $pathsMap array.
        // replace with the value of $pathsMap array
        $themeSubPaths = [];
        foreach ($paths as $path) {
            $pathRelativeToApp = substr($path, strlen(base_path())+1);
            // Ignore paths in composer installed packages (paths inside vendor folder)
            if(strpos($pathRelativeToApp,'vendor')!==0){
                // Remap paths definded int $pathsMap array
                foreach ($pathsMap as $key => $value) {
                    if(strpos($pathRelativeToApp, $key)===0){
                        $pathRelativeToApp = str_replace($key, $value, $pathRelativeToApp);
                        break;
                    }
                }
                $themeSubPaths[] = $pathRelativeToApp;
            }
        }

        // Prepend current theme's view path to the remaped paths
        $newPaths = [];
        $searchPaths = array_diff($this->paths, \Theme::getLaravelViewPaths());
        foreach ($searchPaths as $path1) {
            foreach ($themeSubPaths as $path2) {
                $newPaths[] = $path1.'/'.$path2;
            }
        }

        // Add new paths in the beggin of the search paths array
        foreach (array_reverse($newPaths) as $path) {
            if (!in_array($path, $paths)) {
                $paths = Arr::prepend($paths, $path);
            }
        }

        return $paths;
    }

    /**
     * Override replaceNamespace() to add path for custom error pages "Theme/errors/..."
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function replaceNamespace($namespace, $hints)
    {
        $this->hints[$namespace] = (array) $hints;

        // Overide Error Pages
        if($namespace == 'errors' || $namespace == 'mails'){

            $searchPaths = array_diff($this->paths, \Theme::getLaravelViewPaths());

            $addPaths = array_map(function($path) use ($namespace){
                return "$path/$namespace";
            }, $searchPaths);

            $this->prependNamespace($namespace, $addPaths);
        }
    }

    /**
     * Set the array of paths wherew the views are being searched.
     *
     * @param  array  $paths
     */
    public function setPaths($paths){
        $this->paths = $paths;
    }

    /**
     * Get the array of paths wherew the views are being searched.
     */
    public function getPaths(){
        return $this->paths;
    }

}