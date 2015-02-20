<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Active Theme
	|--------------------------------------------------------------------------
	*/

	'active' => 'Theme.TravelAgency',

	/*
	|--------------------------------------------------------------------------
	| Themes List
	|--------------------------------------------------------------------------
	*/

	'themes' => [

		'Default' => [
			'extends'	 	=> null,
			'views-path' 	=> '',
			'asset-path' 	=> '',
		],

		'Theme.Shared' 			=> [],
		'Theme.Main' 			=> ['extends' => 'Theme.Shared'],
		'Theme.Main.Guide' 		=> ['extends' => 'Theme.Main'],
		'Theme.TravelAgency' 	=> ['extends' => 'Theme.Shared'],
		'Theme.Admin' 			=> ['extends' => 'Theme.Shared'],
	],

];