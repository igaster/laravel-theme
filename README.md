## Description

This is a package for the Laravel 5 Framework that adds basic support for managing themes. It allows you to seperate your views & your assets files in seperate folders, and supports for theme extending! Awesome :)

Features:

* Views & Asset seperation in theme folders
* Theme inheritence: Extend any theme and create Theme hierarcies (WordPress style!)
* Intergrates [Orchestra/Asset](http://orchestraplatform.com/docs/3.0/components/asset) to provide Asset dependencies managment

## How it works

Very simple, you create a folder for each Theme in 'resources/views' and keep all your views seperated. The same goes for assets: create a folder for each theme in your 'public' directory. Set your active theme and you are done. The rest of your application remains theme-agnostic©, which means that when you `View::make('index')` you will access the `index.blade.php` from your selected theme's folder. Same goes for your assets.

## Installation

Edit your project's `composer.json` file to require:

    "require": {
        "igaster/laravel-theme": "~1.0"
    }

and install with `composer update`

Add the service provider in `app/config/app.php`, `Providers` array:

    'igaster\laravelTheme\themeServiceProvider',

also edit the `Facades` array and add:

    'Theme'  => 'igaster\laravelTheme\Facades\Theme',

Almost Done. You only need to publish configuration file to your application with

    artisan vendor:publish

That's it. You are now ready to start theming your applications!

## Defining themes

Simple define your themes in the `themes` array in `config\theme.php`. The format every theme is very simple:

```php
// Select a name for your theme
'theme-name' => [

    // Theme to extend
    // Defaults to null (=none)
    'extends'	 	=> 'theme-to-extend',

    // The path where the view are stored
    // Defaults to 'theme-name' 
    // It is relative to /resources/views (or what ever is defined in )
    'views-path' 	=> 'path-to-views',

    // The path where the assets are stored
    // Defaults to 'theme-name' 
    // It is relative to /public
    'asset-path' 	=> 'path-to-assets',   // defaults to: theme-name
],
```

all settings are optional and can be ommited. Check the example in the configuration file...

## Extending themes

You can set a theme to extend an other. When you are requesting a view/asset that doesn't exist in your active theme, then it will be resolved from it's parent theme. You can easily create variations of your theme by simply overiding your views/themes that are different. 

All themes fall back to the default laravel folders if a resource is not found on the theme folders. So for example you can leave your common libraries (jquery/bootstrap ...) in your `public` folder and use them from all themes. No need to dublicate common assets for each theme!


## Switching Themes

The default theme can be configured in the `theme.php` configuration file. If you need to switch to another theme:

    Theme::set('theme-name');

Unfortunately you can not hot-switch theme anywhere in your application. You must place the login in a Service provider in the `register` method.
For example this is a Service Provider that will select a different theme for the `/admin/xxx` urls:

```php
class themeSelectServiceProvider extends ServiceProvider {

    public function register()
    {
        if (\Request::segment(1)=='admin')
            \Theme::set('adminTheme');
    }

}
```
## Building your views

Whenever you need to link to a local file (image/css/js etc) you can retreive its path with:

    Theme::url('path-to-file')

The path is relative to Theme Folder (NOT to pubic!). For example, if you have placed an image in `public\theme-name\img\logo.png` your Blade code would be:

    <img src="{{Theme::url('img\logo.png')}}">

When you are refering to a local file it will be looked-up in the current theme hierarcy, and the correct path will be returned. If the file is not found on the current theme or its parents then an exception will be thrown.

## Assets Managment (by Orchestra/ASset)

This package provides intergration with [Orchestra/Asset](http://orchestraplatform.com/docs/3.0/components/asset) component. All the features are explained in the official documentation. Although Orchestra/Asset is installed along with this package, it's use is optional.

To use the Orchestra\Asset you need to add in your Providers array:

    'Orchestra\Asset\AssetServiceProvider',
    'Orchestra\Html\HtmlServiceProvider',

and add the Asset facade in your `Facades` array in `app/config/app.php`

    'Asset' => 'Orchestra\Support\Facades\Asset',

Now you can leverage all the power of Orchestra\Asset package. However the syntax can become painful when you are using Themes + Orchestra/Asset, so some Blade-specific sugar has been added to ease your work. So here how to build your views:

In any blade file when you need to refer to a script or css: (dont use single/double quotes)

    @css(filename)
    @js(filename)

Please note that you are just defining your css/js files but not actually dumping them in html. Usually you only need write your css/js decleration in one place on the head/bottom of you file. So open your master layout and place:

    {!! Asset::styles() !!}
    {!! Asset::scripts() !!}

exactly where you want write your declerations (usualy on Head and Footer of the page respectively).

## Assets dependencies

Well this is an [Orchestra/Asset](http://orchestraplatform.com/docs/3.0/components/asset) feature explained well in the official documentation. Long story short:

    @css (filename, alias, depends-on)
    @js  (filename, alias, depends-on)

and your assets dependencies will be auto resolved. Your assets will be exported in the correct order. The biggest benefit of this approach is that you don't have to move all your declerations in your master layout file. Each sub-view can define it's requirements and they will auto-resolved in the correct order with no dublications. Awesome! A short example:

    @js  (jquery.js,    jq)
    @js  (bootstrap.js, bs, jq)

## Important Note:

Laravel is compiling your views every-time you make an edit. A compiled view will not recompile if you switch to another theme unless you make any edit to your view. Keep this in mind while you are developing themes...