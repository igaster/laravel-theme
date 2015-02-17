<?php namespace igaster\laravelTheme;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class themeServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;



    public function boot()
    {

		/*--------------------------------------------------------------------------
		|   @js(filename, alias, depends-on-alias)
		|
		|	Example:
		|
		|   @js(jquery.js, jquery)
		|   @js(bootstrap.js, bootstrap, jquery)
		|--------------------------------------------------------------------------*/

		Blade::extend(function($value)
		{
			return preg_replace_callback('/\@js\s*\(\s*([\w\-\._:\\/]*)\s*(?:,\s*([\w\-\._:\\/]*)\s*,?\s*(.*))?\)/', function($match){

				$match[1] = Themes::url($match[1]);

				if(empty($match[2]))
					return "<?php Orchestra\Support\Facades\Asset::script('{$match[1]}', '{$match[1]}');?>";
				elseif(empty($match[3]))
					return "<?php Orchestra\Support\Facades\Asset::script('{$match[2]}', '{$match[1]}');?>";
				else
					return "<?php Orchestra\Support\Facades\Asset::script('{$match[2]}', '{$match[1]}', '{$match[3]}');?>"; // ToDo : Support for array (match[3]);
				
				},$value);
		});

		/*--------------------------------------------------------------------------
		|   @css (filename, alias, depends-on-alias)
		|--------------------------------------------------------------------------*/

		Blade::extend(function($value)
		{
			return preg_replace_callback('/\@css\s*\(\s*([\w\-\._:\\/]*)\s*(?:,\s*([\w\-\._:\\/]*)\s*,?\s*(.*))?\)/', function($match){

				$match[1] = Themes::url($match[1]);

				if(empty($match[2]))
					return "<?php Orchestra\Support\Facades\Asset::style('{$match[1]}', '{$match[1]}');?>";
				elseif(empty($match[3]))
					return "<?php Orchestra\Support\Facades\Asset::style('{$match[2]}', '{$match[1]}');?>";
				else
					return "<?php Orchestra\Support\Facades\Asset::style('{$match[2]}', '{$match[1]}', '{$match[3]}');?>";

			},$value);
		});

		/*--------------------------------------------------------------------------
		|   Load Themes from theme.php configuration file
		|--------------------------------------------------------------------------*/

		// foreach (Config::get('themes.themes') as $themeName => $options)
		// 	Themes::add($themeName, $options);

		// Themes::set(Config::get('themes.active'));
    }


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [];
	}

}
