<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Switch this package on/off. Usefull for testing...
	|--------------------------------------------------------------------------
	*/

	'enabled' => true,

	/*
	|--------------------------------------------------------------------------
	| Set behavior if an asset is not found in a Theme hierarcy.
	| Available options: THROW_EXCEPTION | LOG_ERROR | IGNORE
	|--------------------------------------------------------------------------
	*/

	'asset_not_found' => 'THROW_EXCEPTION',

	/*
	|--------------------------------------------------------------------------
	| Set the Active Theme. Can be set at runtime with:
	|  Themes::set('theme-name');
	|--------------------------------------------------------------------------
	*/

	'active' => 'default',

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
	| 	],
	|
	|--------------------------------------------------------------------------
	*/

	'themes' => [

		'default' => [
			'extends'	 	=> null,
			'views-path' 	=> '',
			'asset-path' 	=> '',
		],

		// Add your themes here...

		/*--------------[ Example Structre ]-------------

			// Recomended (all defaults) : Assets -> \public\BasicTheme , Views -> \resources\views\BasicTheme

			'BasicTheme',


			// This theme shares the views with BasicTheme but defines its own assets in \public\SomeTheme

			'SomeTheme' => [
				'views-path'	=> 'BasicTheme',
			],


			// This theme extends BasicTheme and ovverides SOME views\assets in its folders

			'AnotherTheme' => [
				'extends'	=> 'BasicTheme',
			],

		------------------------------------------------*/
	],

];
