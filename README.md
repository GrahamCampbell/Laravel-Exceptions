Laravel Exceptions
==================

Laravel Exceptions was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and provides a powerful error response system for both development and production for [Laravel 5](http://laravel.com). It optionally utilises the [Whoops](https://github.com/filp/whoops) package for the development error pages. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Exceptions/releases), [security policy](https://github.com/GrahamCampbell/Laravel-Exceptions/security/policy), [license](LICENSE), [code of conduct](.github/CODE_OF_CONDUCT.md), and [contribution guidelines](.github/CONTRIBUTING.md).

![Laravel Exceptions](https://cloud.githubusercontent.com/assets/2829600/5115020/8da9e70a-7035-11e4-9d28-080b4ba55ed9.PNG)

<p align="center">
<a href="https://styleci.io/repos/26882182"><img src="https://styleci.io/repos/26882182/shield" alt="StyleCI Status"></img></a>
<a href="https://travis-ci.org/GrahamCampbell/Laravel-Exceptions"><img src="https://img.shields.io/travis/GrahamCampbell/Laravel-Exceptions/master.svg?style=flat-square" alt="Build Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Exceptions/code-structure"><img src="https://img.shields.io/scrutinizer/coverage/g/GrahamCampbell/Laravel-Exceptions.svg?style=flat-square" alt="Coverage Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Exceptions"><img src="https://img.shields.io/scrutinizer/g/GrahamCampbell/Laravel-Exceptions.svg?style=flat-square" alt="Quality Score"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/GrahamCampbell/Laravel-Exceptions/releases"><img src="https://img.shields.io/github/release/GrahamCampbell/Laravel-Exceptions.svg?style=flat-square" alt="Latest Version"></img></a>
</p>


## Installation

Laravel Exceptions requires [PHP](https://php.net) 7.1-7.4. This particular version supports Laravel 5.5-5.8 and 6 only.

To get the latest version, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require graham-campbell/exceptions
```

Once installed, if you are not using automatic package discovery, then you need to register the `GrahamCampbell\Exceptions\ExceptionsServiceProvider` service provider in your `config/app.php`.

You then MUST change your `App\Exceptions\Handler` to extend `GrahamCampbell\Exceptions\ExceptionHandler`.


## Whoops Support

If you want to have the debug error pages available, you're going to need to require [Whoops](https://github.com/filp/whoops):

```bash
$ composer require filp/whoops --dev
```

Our debug displayer will automatically detect the presence of Whoops. Feel free to go and have a read of our source code to give you a better understanding of how this works. Do note that we support only Whoops `^2.4`.


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


## Security

If you discover a security vulnerability within this package, please send an email to Graham Campbell at graham@alt-three.com. All security vulnerabilities will be promptly addressed. You may view our full security policy [here](https://github.com/GrahamCampbell/Laravel-Exceptions/security/policy).


## License

Laravel Exceptions is licensed under [The MIT License (MIT)](LICENSE).


---

<div align="center">
	<b>
		<a href="https://tidelift.com/subscription/pkg/packagist-graham-campbell-exceptions?utm_source=packagist-graham-campbell-exceptions&utm_medium=referral&utm_campaign=readme">Get professional support for Laravel Exceptions with a Tidelift subscription</a>
	</b>
	<br>
	<sub>
		Tidelift helps make open source sustainable for maintainers while giving companies<br>assurances about security, maintenance, and licensing for their dependencies.
	</sub>
</div>
