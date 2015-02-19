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

use igaster\laravelTheme\Asset;
use igaster\laravelTheme\Assets;


Assets::add('x1', 'x1');
Assets::add('x2', 'x2');
Assets::add('x3', 'x3');

Assets::add('x21', 'x21', 'x2');


dd(Assets::find('x21'));


Themes::boot();

Themes::add(new Theme('t1'));
Themes::add(new Theme('t2'));
Themes::add(new Theme('t3'));

$t21 = Themes::add(new Theme('t21'), 't2');
Themes::set('t21');
//dd($t21);
dd(Themes::url('xxx.txt'));

