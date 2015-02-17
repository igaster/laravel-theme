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

Themes::add('Theme1');
Themes::add('Theme2', ['extends' => 'Theme1']);

Themes::set('Theme2');

// echo Themes::url('something.txt');

Assets::add(10, 10);
Assets::add(11, 11, 10);
Assets::add(20, 20, 11);
Assets::add(21, 21, 20);

dd(
	Assets::add('xx', 'xx', [11,21])

	->dependencies()
);

Route::get('/', function()
{
return '';
    return View::make('index');
});


