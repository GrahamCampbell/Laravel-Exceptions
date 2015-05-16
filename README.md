Laravel Exceptions
==================

Laravel Exceptions was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and provides pretty error pages for both development and production for [Laravel 5](http://laravel.com). It utilises the [Whoops](https://github.com/filp/whoops) package for the development error pages. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Exceptions/releases), [license](LICENSE), [api docs](http://docs.grahamjcampbell.co.uk), and [contribution guidelines](CONTRIBUTING.md).

![Laravel Exceptions](https://cloud.githubusercontent.com/assets/2829600/5115020/8da9e70a-7035-11e4-9d28-080b4ba55ed9.PNG)

<p align="center">
<a href="https://travis-ci.org/GrahamCampbell/Laravel-Exceptions"><img src="https://img.shields.io/travis/GrahamCampbell/Laravel-Exceptions/master.svg?style=flat-square" alt="Build Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Exceptions/code-structure"><img src="https://img.shields.io/scrutinizer/coverage/g/GrahamCampbell/Laravel-Exceptions.svg?style=flat-square" alt="Coverage Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Exceptions"><img src="https://img.shields.io/scrutinizer/g/GrahamCampbell/Laravel-Exceptions.svg?style=flat-square" alt="Quality Score"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/GrahamCampbell/Laravel-Exceptions/releases"><img src="https://img.shields.io/github/release/GrahamCampbell/Laravel-Exceptions.svg?style=flat-square" alt="Latest Version"></img></a>
</p>


## Installation

[PHP](https://php.net) 5.5+ or [HHVM](http://hhvm.com) 3.6+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Exceptions, simply add the following line to the require block of your `composer.json` file:

```
"graham-campbell/exceptions": "~2.0"
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Laravel Exceptions is installed, you need to register the service provider. Open up `config/app.php` and add the following to the `providers` key.

* `'GrahamCampbell\Exceptions\ExceptionsServiceProvider'`

You then MUST change your `App\Exceptions\Handler` class to extend `GrahamCampbell\Exceptions\ExceptionHandler` rather than extending `Illuminate\Foundation\Exceptions\Handler`.


## Configuration

Laravel Exceptions requires no configuration. Just follow the simple install instructions and go!


## Usage

There is currently no real documentation for this package, but feel free to check out the [API Documentation](http://docs.grahamjcampbell.co.uk) for Laravel Exceptions.


## License

Laravel Exceptions is licensed under [The MIT License (MIT)](LICENSE).
