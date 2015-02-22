<?php

// install 

git clone https://github.com/igaster/laravel-theme.git laravel-theme

// -- composer.json:

...
	"autoload": {
		"classmap": [
			"database"
		],
		"psr-4": {
			"App\\": "app/",
			"igaster\\laravelTheme\\": "laravel-theme/igaster/laravelTheme/src/"
		}
	},
...

// -- app.php:

	//Providers:

		'igaster\laravelTheme\themeServiceProvider',

	//Facades:

		'Themes'     => 'igaster\laravelTheme\Themes',
		'Assets'     => 'igaster\laravelTheme\Assets'

// -- routes:
use igaster\laravelTheme\Theme;
use igaster\laravelTheme\Themes;

use igaster\laravelTheme\Assets\Assets;


Themes::boot();

Themes::add( new Theme('t1') );
Themes::add( new Theme('t2') );
Themes::add( new Theme('t21'), 't2');
Themes::set('t21');

Assets::script('1.txt', 1);
Assets::script('2.txt', 2, 1);
Assets::script('3.txt', 3, 2);

echo (Assets::find(2)->write());
echo (Assets::find(3)->write());