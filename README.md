## Description
[![Laravel](https://img.shields.io/badge/Laravel-5.x-orange.svg?style=flat-square)](http://laravel.com)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)
[![Downloads](https://img.shields.io/packagist/dt/igaster/laravel-theme.svg?style=flat-square)](https://packagist.org/packages/igaster/laravel-theme)

This is a Laravel package that adds basic support for managing themes. It allows you to build your views & your assets in seperate folders, and supports for theme extending! Awesome :)

Features:

* Views & Asset separation in theme folders
* Theme inheritance: Extend any theme and create Theme hierarchies (WordPress style!)
* Integrates [Orchestra/Asset](http://orchestraplatform.com/docs/3.0/components/asset) to provide Asset dependencies managment
* Your App & Views remain theme-agnostic. Include new themes with (almost) no modifications
* Themes are distributable! Create a single-file theme Package and install it on any Laravel application.
* Ships with console commands to manage themes

## Documentation

Check the [Documentation](https://github.com/igaster/laravel-theme/wiki/1.-Installation)

If you are upgrading from v1.x please read the [migration guide](https://github.com/igaster/laravel-theme/wiki/Migrating-from-v1.x)

## Compability

v2.x requires Laravel 5.4+

- For Laravel 5.0 & 5.1, please use the [v1.0.x branch](https://github.com/igaster/laravel-theme/tree/v1.0)
- For Laravel 5.2 & 5.3, please use the [v1.1.x branch](https://github.com/igaster/laravel-theme/tree/v1.1)

## Tests

This package is fully tested. Check the [testing repository](https://github.com/igaster/laravel-theme-tests)
