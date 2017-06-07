<?php namespace igaster\laravelTheme;

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
		| Is package enabled?
		|--------------------------------------------------------------------------*/

		if (!Config::get('themes.enabled', true))
			return;

		/*--------------------------------------------------------------------------
		| Replace FileViewFinder
		|--------------------------------------------------------------------------*/

        $this->app->singleton('view.finder', function($app) {
            return new \igaster\laravelTheme\themeViewFinder(
                $app['files'],
                $app['config']['view.paths'],
                null
            );
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
