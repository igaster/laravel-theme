<?php

return [

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

		/*--------------[Example Structre]------------- 
	
			'BasicTheme',

			'SomeTheme' => [
				'extends'	=> 'BasicTheme',
			],

			'AnOtherTheme',
	
		----------------------------------------------*/
	],

];