<?php

return [


    /*
    |--------------------------------------------------------------------------
    | File path where theme Views will be located.
    | Can be outside default views path EG: resources/themes
    | Leave it null if you place your themes in the default views folder 
    | (as defined in config\views.php)
    |--------------------------------------------------------------------------
    */

    'themes_path' => null, // eg: base_path('resources/themes')

	/*
	|--------------------------------------------------------------------------
	| Set behavior if an asset is not found in a Theme hierarcy.
	| Available options: THROW_EXCEPTION | LOG_ERROR | IGNORE
	|--------------------------------------------------------------------------
	*/

	'asset_not_found' => 'LOG_ERROR',

	/*
	|--------------------------------------------------------------------------
	| Set the Active Theme. Can be set at runtime with:
	|  Themes::set('theme-name');
	|--------------------------------------------------------------------------
	*/

	'active' => null,

	/*
	|--------------------------------------------------------------------------
	| Define available themes. Format:
	|
	| 	'theme-name' => [
	| 		'extends'	 	=> 'theme-to-extend',  // optional
	| 		'views-path' 	=> 'path-to-views',    // defaults to: resources/views/theme-name
	| 		'asset-path' 	=> 'path-to-assets',   // defaults to: public/theme-name
	|
	|		// you can add your own custom keys and retrieve them with Theme::config('key');
	| 		'key' 			=> 'value', 
	| 	],
	|
	|--------------------------------------------------------------------------
	*/

	'themes' => [

		// Add your themes here...

		/*
		|---------------------------[ Example Structure ]--------------------------
		|
		|	// Full theme Syntax:
		|
		|	'example1' => [
		|		'extends'	 	=> null, 	// doesn't extend any theme
		|		'views-path' 	=> example, // = resources/views/example_theme
		|		'asset-path' 	=> example, // = public/example_theme
		|	],
		|	
		|	// Use all Defaults:
		|	
		|	'example2',	// Assets =\public\example2, Views =\resources\views\example2
		|				// Note that if you use all default values, you can ommit decledration completely.
		|				// i.e. defaults will be used when you call Theme::set('undefined-theme')
		|	
		|	
		|	// This theme shares the views with example2 but defines its own assets in \public\example3
		|	
		|	'example3' => [
		|		'views-path'	=> 'example',
		|	],
		|	
		|	// This theme extends example1 and may ovveride SOME views\assets in its own paths
		|	
		|	'example4' => [
		|		'extends'	=> 'example1',
		|	],
		|	
		|--------------------------------------------------------------------------
		*/
	],

];
