Laravel Exceptions
==================

Laravel Exceptions was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and provides a powerful error response system for both development and production for [Laravel 5](http://laravel.com). It optionally utilises the [Whoops](https://github.com/filp/whoops) package for the development error pages. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Exceptions/releases), [license](LICENSE), and [contribution guidelines](CONTRIBUTING.md).

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

Either [PHP](https://php.net) 5.5+ or [HHVM](http://hhvm.com) 3.6+ are required.

To get the latest version of Laravel Exceptions, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require graham-campbell/exceptions
```

Instead, you may of course manually update your require block and run `composer update` if you so choose:

```json
{
    "require": {
        "graham-campbell/exceptions": "^10.0"
    }
}
```

If you want to have the debug error pages available, you're going to need to require [Whoops](https://github.com/filp/whoops):

```bash
$ composer require filp/whoops --dev
```

We support both Whoops `^1.1` or `^2.0`, so feel free to use either.

#### Register Service Provider
Once Laravel Exceptions is installed, you need to register the service provider.

* Modify `config/app.php` and add the following to the `providers` key.

```php
/*
 * Application Service Providers...
 */

...
App\Providers\EventServiceProvider::class,
App\Providers\RouteServiceProvider::class,
...
GrahamCampbell\Exceptions\ExceptionsServiceProvider::class,
```

or

```php
'GrahamCampbell\Exceptions\ExceptionsServiceProvider'
```

#### Laravel Customization
Based on the version of Laravel (or Lumen) you will need to change `App\Exceptions\Hanlder` to extend the appropriate Exceptions handler


##### Laravel 5.3 or greater
* Modify `App\Exceptions\Handler` class to extend `GrahamCampbell\Exceptions\NewExceptionHandler`
```php
use Illuminate\Auth\AuthenticationException;
//use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use GrahamCampbell\Exceptions\NewExceptionHandler as ExceptionHandler;

class Handler extends ExceptionHandler
{
  ...
```  

##### Laravel 5.1 / 5.2
Modify `App\Exceptions\Handler` class to extend `GrahamCampbell\Exceptions\ExceptionHandler`

##### Lumen
Modify `App\Exceptions\Handler` class to extend `GrahamCampbell\Exceptions\LumenExceptionHandler`.

####  Adjust Render Method
* Modify `App\Exceptions\Hander` class `render` method.  Add the following above `parent::render..`

```php
if ($exception instanceof \Illuminate\Auth\AuthenticationException){
    return $this->unauthenticated($request, $exception);
}
```

Your `render` method should look similar to the following
```php
public function render($request, Exception $exception)
{
    if ($exception instanceof \Illuminate\Auth\AuthenticationException){
        return $this->unauthenticated($request, $exception);
    }
    return parent::render($request, $exception);
}
```

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

If you discover a security vulnerability within this package, please send an e-mail to Graham Campbell at graham@alt-three.com. All security vulnerabilities will be promptly addressed.


## License

Laravel Exceptions is licensed under [The MIT License (MIT)](LICENSE).
