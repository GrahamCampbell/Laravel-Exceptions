Laravel Exceptions
==================

Laravel Exceptions was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and provides pretty error pages for both development and production for [Laravel 5](http://laravel.com). It utilises the [Whoops](https://github.com/filp/whoops) package for the development error pages. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Exceptions/releases), [license](LICENSE), and [contribution guidelines](CONTRIBUTING.md).

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
"graham-campbell/exceptions": "~4.1"
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Laravel Exceptions is installed, you need to register the service provider. Open up `config/app.php` and add the following to the `providers` key.

* `'GrahamCampbell\Exceptions\ExceptionsServiceProvider'`

You then MUST change your `App\Exceptions\Handler` class to extend `GrahamCampbell\Exceptions\ExceptionHandler` rather than extending `Illuminate\Foundation\Exceptions\Handler`.


## Configuration

Laravel Exceptions supports optional configuration.

To get started, you'll need to publish all vendor assets:

```bash
$ php artisan vendor:publish
```

This will create a `config/exceptions.php` file in your app that you can modify to set your configuration. Also, make sure you check for changes to the original config file in this package between releases.

There are a few config options:

##### Exception Transformers

This option (`'transformers'`) defines each of the exception transformers setup for your application. This allows you to turn your exceptions into other exceptions such as  exceptions for perfect results when passed to the displayers. Note that this list is processed in order and subsequent transformers can still modify the results of previous ones if required.

##### Exception Displayers

This option (`'displayers'`) defines each of the exception displayers setup for your application. These displayers are sorted by priority. Note that when we are in debug mode, we will select the first valid displayer from the list, and when we are not in debug mode, we'll filter out all verbose displayers, then select the first valid displayer from the new list.

##### Displayer Filters

This option (`'filters'`) defines each of the filters for the displayers. This allows you to apply filters to your displayers in order to work out which displayer to use for each exception. This includes things like content type negotiation.

##### Default Displayer

This option (`'default'`) defines the default displayer for your application. This displayer will be used if your filters have filtered out all the displayers, otherwise leaving us unable to displayer the exception.

##### Exception Levels

This option (`'levels'`) defines the log levels for the each exception. If an exception passes an instance of test for each key, then the log level used is the value associated with each key.


## Usage

There is currently no usage documentation for Laravel Exceptions, but we are open to pull requests.


## License

Laravel Exceptions is licensed under [The MIT License (MIT)](LICENSE).
