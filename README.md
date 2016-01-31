## Description
[![Laravel](https://img.shields.io/badge/Laravel-5.x-orange.svg?style=flat-square)](http://laravel.com)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)
[![Downloads](https://img.shields.io/packagist/dt/igaster/laravel-theme.svg?style=flat-square)](https://packagist.org/packages/igaster/laravel-theme)

This is a package for the Laravel 5 Framework that adds basic support for managing themes. It allows you to seperate your views & your assets files in seperate folders, and supports for theme extending! Awesome :)

Features:

* Views & Asset seperation in theme folders
* Theme inheritence: Extend any theme and create Theme hierarcies (WordPress style!)
* Intergrates [Orchestra/Asset](http://orchestraplatform.com/docs/3.0/components/asset) to provide Asset dependencies managment
* Your App & Views remain theme-agnostic. Include new themes with (almost) no modifications

## How it works

Very simple, you create a folder for each Theme in 'resources/views' and keep all your views seperated. The same goes for assets: create a folder for each theme in your 'public' directory. Set your active theme and you are done. The rest of your application remains theme-agnostic©, which means that when you `View::make('index')` you will access the `index.blade.php` from your selected theme's folder. Same goes for your assets.

## Version Compatibility

Laravel    | laravel-theme
:----------|:----------
 5.0.x     | 1.0.x
 5.1.x     | 1.0.x
 5.2.x     | 1.1.x

## Installation

Edit your project's `composer.json` file to require:

    "require": {
        "igaster/laravel-theme": "~1.1.0"
    }

and install with `composer update`

Add the service provider in `app/config/app.php`, `Providers` array:

    igaster\laravelTheme\themeServiceProvider::class,

also edit the `Facades` array and add:

    'Theme' => igaster\laravelTheme\Facades\Theme::class,

Almost Done. You can optionally publish a configuration file to your application with

    php artisan vendor:publish --provider="igaster\laravelTheme\themeServiceProvider"

That's it. You are now ready to start theming your applications!

## Defining themes

Simple define your themes in the `themes` array in `config/themes.php`. The format for every theme is very simple:

```php
// Select a name for your theme
'theme-name' => [

    // Theme to extend
    // Defaults to null (=none)
    'extends'	 	=> 'theme-to-extend',

    // The path where the view are stored
    // Defaults to 'theme-name' 
    // It is relative to /resources/views (or whatever is defined in config/view.php)
    'views-path' 	=> 'path-to-views',

    // The path where the assets are stored
    // Defaults to 'theme-name' 
    // It is relative to laravels public folder (/public)
    'asset-path' 	=> 'path-to-assets',   // defaults to: theme-name

    // you can add your own custom keys and retrieve them with Theme::config('key'). e.g.:
    'key'           => 'value', 
],
```
all settings are optional and can be ommited. Check the example in the configuration file... If you are OK with the defaults then you don't even have to touch the configuration file. If a theme has not been registered then the default values will be used!

## Extending themes

You can set a theme to extend an other. When you are requesting a view/asset that doesn't exist in your active theme, then it will be resolved from it's parent theme. You can easily create variations of your theme by simply overiding your views/themes that are different. 

All themes fall back to the default laravel folders if a resource is not found on the theme folders. So for example you can leave your common libraries (jquery/bootstrap ...) in your `public` folder and use them from all themes. No need to dublicate common assets for each theme!

## Working with Themes

The default theme can be configured in the `theme.php` configuration file. Working with themes is very straightforward. Use:

```php
Theme::set('theme-name');        // switch to 'theme-name'
Theme::get();                    // retrieve current theme's name
Theme::config('key');            // read current theme's configuration value for 'key'
Theme::configSet('key','value'); // assign a key-value pair to current theme's configuration
```

You are free to create your own implementation to set a Theme via a ServiceProvider, or a Middleware, or even define the Theme in your Controllers. 

## 'setTheme' middleware (Laravel 5.2+)

A [helper middleware](https://github.com/igaster/laravel-theme/blob/master/src/Middleware/setTheme.php) is included out of the box if you want to define a Theme per route. To use it:

First register it in `app\Http\Kernel.php`:

```php
protected $routeMiddleware = [
    // ...
    'setTheme' => \igaster\laravelTheme\Middleware\setTheme::class,
];
```

Now you can apply the middleware to a route or route-group. Eg:

```php
Route::group(['prefix' => 'admin', 'middleware'=>'setTheme:ADMIN_THEME'], function() {
    // ... Add your routes here 
    // The ADMIN_THEME will be applied.
});
```
For more advanced example check demo application: [Set Theme in Session](https://github.com/igaster/laravel-theme-demo) 

## Building your views

Whenever you need the url of a local file (image/css/js etc) you can retrieve its path with:

```php
Theme::url('path-to-file')
```

The path is relative to Theme Folder (NOT to public!). For example, if you have placed an image in `public/theme-name/img/logo.png` your Blade code would be:

    <img src="{{Theme::url('img/logo.png')}}">

When you are refering to a local file it will be looked-up in the current theme hierarcy, and the correct path will be returned. If the file is not found on the current theme or its parents then you can define in the configuration file the action that will be carried out: `THROW_EXCEPTION` | `LOG_ERROR` as warning (Default) | `IGNORE` completly.

Some usefull helpers you can use:

```php
Theme::js('file-name')
Theme::css('file-name')
Theme::img('src', 'alt', 'class-name')
```    

## Paremeters in filenames

You can include any configuration key of the current theme inside any path string using *{curly brackets}*. For examle:

```php
Theme::url('main-{version}.css')
```

if there is a `"version"` key defined in the theme's configuration it will be evaluated and then the filename will be looked-up in the theme hierarcy. (e.g: many comercial themes ship with multiple versions of the main.css for different color-schemes, or use [language-dependent assets](https://github.com/igaster/laravel-theme/issues/17))

## Assets Managment (Optional)

This package provides intergration with [Orchestra/Asset](http://orchestraplatform.com/docs/3.0/components/asset) component. All the features are explained in the official documentation. If you don't need the extra functinality you can skip this section. Orchestra/Asset is NOT installed along with this package - you have to install it manualy.

To install Orchestra\Asset you must add it in your composer.json (see the [Official Documentation](https://github.com/orchestral/asset)):

    "orchestra/asset": "~3.0",
    "orchestra/support": "~3.0",

and run `composer update`. Then add the Service Providers in your Providers array (in `app/config/app.php`):

    Orchestra\Asset\AssetServiceProvider::class,
    Collective\Html\HtmlServiceProvider::class,

Add the Asset facade in your `aliases` array:

    'Asset' => Orchestra\Support\Facades\Asset::class,

Now you can leverage all the power of Orchestra\Asset package. However the syntax can become quite cumbersome when you are using Themes + Orchestra/Asset, so some Blade-specific sugar has been added to ease your work. Here how to build your views:

In any blade file you can require a script or a css:

    @css('filename')
    @js('filename')

Please note that you are just defining your css/js files but not actually dumping them in html. Usually you only need write your css/js decleration in one place on the Head/Footer of you page. So open your master layout and place:

    {!! Asset::styles() !!}
    {!! Asset::scripts() !!}

exactly where you want write your declerations.

## Assets dependencies

This is an [Orchestra/Asset](http://orchestraplatform.com/docs/3.0/components/asset) feature explained well in the official documentation. Long story short:

    @css ('filename', 'alias', 'depends-on')
    @js  ('filename', 'alias', 'depends-on')

and your assets dependencies will be auto resolved. Your assets will be exported in the correct order. The biggest benefit of this approach is that you don't have to move all your declerations in your master layout file. Each sub-view can define it's requirements and they will auto-resolved in the correct order with no doublications. Awesome! A short example:

    @js  ('jquery.js',    'jquery')
    @js  ('bootstrap.js', 'bootsrap', jquery)

## FAQ:

##### Is this package compatible with AWS?
Yes with one exception: If you are building Theme hierarcies, asset's will not be looked up on the parent theme. Performing file searching on a remote repository is not the best practice. Should be addressed in a future version... However Blade templates auto-discovery works fine since they are local files.

##### What about external assets (eg CDN)?
Link directly to your external assets. Every url that starts with http(s) will not be proccesed by default.

##### How do I change the public path?
Rebind Laravel's 'path.public'. [(More info)](https://laracasts.com/discuss/channels/general-discussion/where-do-you-set-public-directory-laravel-5)

##### I'm editing a view but I dont see the changes
Laravel is compiling your views every-time you make an edit. A compiled view will not recompile unless you make any edit to your view. Keep this in mind while you are developing themes...