<?php namespace igaster\Theme;

use Illuminate\Support\ServiceProvider;
// use App\libraries\Themes\Themes;
// use App\libraries\Domains;

class themeServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		// foreach (Config::get('themes.themes') as $themeName => $options)
		// 	Themes::add($themeName, $options);

		// Themes::set(Config::get('themes.active'));  // Default skiped!
    }

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

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
