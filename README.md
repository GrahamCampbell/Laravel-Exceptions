Laravel Exceptions
==================

Laravel Exceptions was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and provides a powerful error response system for both development and production for [Laravel](http://laravel.com). It optionally utilises the [Whoops](https://github.com/filp/whoops) package for the development error pages. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Exceptions/releases), [security policy](https://github.com/GrahamCampbell/Laravel-Exceptions/security/policy), [license](LICENSE), [code of conduct](.github/CODE_OF_CONDUCT.md), and [contribution guidelines](.github/CONTRIBUTING.md).

![Banner](https://user-images.githubusercontent.com/2829600/71477346-60993680-27e1-11ea-881f-01ac5caaa169.png)

<p align="center">
<a href="https://github.com/GrahamCampbell/Laravel-Exceptions/actions?query=workflow%3ATests"><img src="https://img.shields.io/github/workflow/status/GrahamCampbell/Laravel-Exceptions/Tests?label=Tests&style=flat-square" alt="Build Status"></img></a>
<a href="https://github.styleci.io/repos/26882182"><img src="https://github.styleci.io/repos/26882182/shield" alt="StyleCI Status"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen?style=flat-square" alt="Software License"></img></a>
<a href="https://packagist.org/packages/graham-campbell/exceptions"><img src="https://img.shields.io/packagist/dt/graham-campbell/exceptions?style=flat-square" alt="Packagist Downloads"></img></a>
<a href="https://github.com/GrahamCampbell/Laravel-Exceptions/releases"><img src="https://img.shields.io/github/release/GrahamCampbell/Laravel-Exceptions?style=flat-square" alt="Latest Version"></img></a>
</p>


## Installation

Laravel Exceptions requires [PHP](https://php.net) 7.2-8.0. This particular version supports Laravel 6.

| Exceptions | L5.1               | L5.2               | L5.3               | L5.4               | L5.5               | L5.6               | L5.7               | L5.8               | L6                 |
|------------|--------------------|--------------------|--------------------|--------------------|--------------------|--------------------|--------------------|--------------------|--------------------|
| 9.4        | :white_check_mark: | :white_check_mark: | :white_check_mark: | :white_check_mark: | :x:                | :x:                | :x:                | :x:                | :x:                |
| 10.1       | :x:                | :x:                | :white_check_mark: | :white_check_mark: | :white_check_mark: | :x:                | :x:                | :x:                | :x:                |
| 11.3       | :x:                | :x:                | :x:                | :x:                | :white_check_mark: | :white_check_mark: | :white_check_mark: | :white_check_mark: | :x:                |
| 12.1       | :x:                | :x:                | :x:                | :x:                | :white_check_mark: | :white_check_mark: | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| 13.1       | :x:                | :x:                | :x:                | :x:                | :x:                | :x:                | :x:                | :x:                | :white_check_mark: |

To get the latest version, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require "graham-campbell/exceptions:^13.1"
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


## For Enterprise

Available as part of the Tidelift Subscription

The maintainers of `graham-campbell/exceptions` and thousands of other packages are working with Tidelift to deliver commercial support and maintenance for the open source dependencies you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact dependencies you use. [Learn more.](https://tidelift.com/subscription/pkg/packagist-graham-campbell-exceptions?utm_source=packagist-graham-campbell-exceptions&utm_medium=referral&utm_campaign=enterprise&utm_term=repo)
