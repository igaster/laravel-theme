<?php namespace Igaster\LaravelTheme;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Blade;

class themeServiceProvider extends ServiceProvider {

    public function register(){

		/*--------------------------------------------------------------------------
		| Bind in IOC
		|--------------------------------------------------------------------------*/

		$this->app->singleton('igaster.themes', function(){
			return new Themes();
		});

		/*--------------------------------------------------------------------------
		| Replace FileViewFinder
		|--------------------------------------------------------------------------*/

        $this->app->singleton('view.finder', function($app) {
            return new \Igaster\LaravelTheme\themeViewFinder(
                $app['files'],
                $app['config']['view.paths'],
                null
            );
        });

		/*--------------------------------------------------------------------------
		| Register helpers.php functions
		|--------------------------------------------------------------------------*/

        require_once 'Helpers/helpers.php';

		/*--------------------------------------------------------------------------
		| Initialize Themes
		|--------------------------------------------------------------------------*/

		$themes = $this->app->make('igaster.themes');
        $themes->scanThemes();

		/*--------------------------------------------------------------------------
		| Activate default theme
		|--------------------------------------------------------------------------*/
		if (!$themes->current() && \Config::get('themes.default'))
			$themes->set(\Config::get('themes.default'));
    }

	public function boot(){

		/*--------------------------------------------------------------------------
		| Pulish configuration file
		|--------------------------------------------------------------------------*/

		$this->publishes([
			__DIR__.'/Config/themes.php' => config_path('themes.php'),
		], 'laravel-theme');

		/*--------------------------------------------------------------------------
		| Register Console Commands
		|--------------------------------------------------------------------------*/
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Igaster\LaravelTheme\Commands\listThemes::class,
                \Igaster\LaravelTheme\Commands\createTheme::class,
                \Igaster\LaravelTheme\Commands\removeTheme::class,
                \Igaster\LaravelTheme\Commands\createPackage::class,
                \Igaster\LaravelTheme\Commands\installPackage::class,
                \Igaster\LaravelTheme\Commands\refreshCache::class,
            ]);
        }

		/*--------------------------------------------------------------------------
		| Register custom Blade Directives
		|--------------------------------------------------------------------------*/

		$this->registerBladeDirectives();
	}

	protected function registerBladeDirectives(){
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
			return preg_replace_callback('/\@js\s*\(\s*([^),]*)(?:,\s*([^),]*))?(?:,\s*([^),]*))?\)/', function($match){

				$p1 = trim($match[1], " \t\n\r\0\x0B\"'");
				$p2 = trim(empty($match[2]) ? $p1 : $match[2], " \t\n\r\0\x0B\"'");
				$p3 = trim(empty($match[3]) ? '' : $match[3], " \t\n\r\0\x0B\"'");

				if(empty($p3))
					return "<?php Asset::script('$p2', \Theme::url('$p1'));?>";
				else
					return "<?php Asset::script('$p2', \Theme::url('$p1'), '$p3');?>";

			},$value);
		});

		\Blade::extend(function ($value)
		{
			return preg_replace_callback('/\@jsIn\s*\(\s*([^),]*)(?:,\s*([^),]*))?(?:,\s*([^),]*))?(?:,\s*([^),]*))?\)/',
				function ($match) {

					$p1 = trim($match[1], " \t\n\r\0\x0B\"'");
					$p2 = trim($match[2], " \t\n\r\0\x0B\"'");
					$p3 = trim(empty($match[3]) ? $p2 : $match[3], " \t\n\r\0\x0B\"'");
					$p4 = trim(empty($match[4]) ? '' : $match[4], " \t\n\r\0\x0B\"'");

					if (empty($p4)) {
						return "<?php Asset::container('$p1')->script('$p3', \\Theme::url('$p2'));?>";
					} else {
						return "<?php Asset::container('$p1')->script('$p3', \\Theme::url('$p2'), '$p4');?>";
					}

				}, $value);
		});


		Blade::extend(function($value)
		{
			return preg_replace_callback('/\@css\s*\(\s*([^),]*)(?:,\s*([^),]*))?(?:,\s*([^),]*))?\)/', function($match){

				$p1 = trim($match[1], " \t\n\r\0\x0B\"'");
				$p2 = trim(empty($match[2]) ? $p1 : $match[2], " \t\n\r\0\x0B\"'");
				$p3 = trim(empty($match[3]) ? '' : $match[3], " \t\n\r\0\x0B\"'");

				if(empty($p3))
					return "<?php Asset::style('$p2', \Theme::url('$p1'));?>";
				else
					return "<?php Asset::style('$p2', \Theme::url('$p1'), '$p3');?>";

			},$value);
		});
	}

}
