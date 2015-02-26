<?php namespace igaster\laravelTheme;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Blade;

class themeServiceProvider extends ServiceProvider {


    public function register(){

		/*--------------------------------------------------------------------------
		| Bind in IOC
		|--------------------------------------------------------------------------*/

		$this->app->bindShared('igaster.themes', function(){
			return new Themes();
		});

		/*--------------------------------------------------------------------------
		| Initialize Themes
		|--------------------------------------------------------------------------*/

		$Themes = $this->app->make('igaster.themes');

		/*--------------------------------------------------------------------------
		|   Load Themes from theme.php configuration file
		|--------------------------------------------------------------------------*/

		if (Config::has('themes')){
			foreach (Config::get('themes.themes') as $themeName => $options) {
				$assetPath = null;
				$viewsPath = null;
				$extends = null;

				if(is_array($options)){
					if(array_key_exists('asset-path', $options)) $assetPath = $options['asset-path'];
					if(array_key_exists('views-path', $options)) $viewsPath = $options['views-path'];
					if(array_key_exists('extends', $options)) $extends = $options['extends'];
				} else {
					$themeName = $options;
				}
				$Themes->add(new Theme($themeName, $assetPath, $viewsPath), $extends);
			}

			if (!$Themes->activeTheme)
				$Themes->set(Config::get('themes.active'));
		}
    }

	public function boot(){

		/*--------------------------------------------------------------------------
		| Pulish configuration file
		|--------------------------------------------------------------------------*/

		$this->publishes([
			__DIR__.'/config.php' => config_path('themes.php'),
		]);

		/*--------------------------------------------------------------------------
		| Extend Blade to support Orcherstra\Asset (Asset Managment)
		|
		| Syntax:
		|
		|   @css (filename, alias, depends-on-alias)
		|   @js  (filename, alias, depends-on-alias)
		|--------------------------------------------------------------------------*/

		Blade::extend(function($value)
		{
		    return preg_replace_callback('/\@js\s*\(\s*([\w\-\._:\\/]*)\s*(?:,\s*([\w\-\._:\\/]*)\s*,?\s*(.*))?\)/', function($match){

				$p1 = \Theme::url($match[1]);
				$p2 = empty($match[2]) ? $match[1] : $match[2];
				$p3 = empty($match[3]) ? '' : $match[3];

				if(empty($p2))
					return "<?php Asset::script('$p2', '$p1');?>";
				elseif(empty($p3))
					return "<?php Asset::script('$p2', '$p1');?>";
				else
					return "<?php Asset::script('$p2', '$p1', '$p3');?>";
			},$value);
		});


		Blade::extend(function($value)
		{
			return preg_replace_callback('/\@css\s*\(\s*([\w\-\._:\\/]*)\s*(?:,\s*([\w\-\._:\\/]*)\s*,?\s*(.*))?\)/', function($match){

				$p1 = \Theme::url($match[1]);
				$p2 = empty($match[2]) ? $match[1] : $match[2];
				$p3 = empty($match[3]) ? '' : $match[3];

				if(empty($p2))
					return "<?php Asset::style('$p2', '$p1');?>";
				elseif(empty($p3))
					return "<?php Asset::style('$p2', '$p1');?>";
				else
					return "<?php Asset::style('$p2', '$p1', '$p3');?>";
			},$value);
		});
	}


}
