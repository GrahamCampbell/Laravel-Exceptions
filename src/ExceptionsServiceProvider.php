<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions;

use GrahamCampbell\Exceptions\Displayers\HtmlDisplayer;
use GrahamCampbell\Exceptions\Filters\VerboseFilter;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\UrlGenerator as LaravelGenerator;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Laravel\Lumen\Routing\UrlGenerator as LumenGenerator;

/**
 * This is the exceptions service provider class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ExceptionsServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $file['source'] = realpath(__DIR__.'/../config/exceptions.php');
        $file['json'] = realpath(__DIR__.'/../resources/errors.json');
        $file['html'] = realpath(__DIR__.'/../resources/error.html');


        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$file['source'] => config_path('exceptions.php')]);
            $this->publishes([$file['json'] => resource_path('views/errors/errors.json')]);
            $this->publishes([$file['html'] => resource_path('views/errors/errors.html')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('exceptions');
        }

        $this->mergeConfigFrom($file['source'], 'exceptions');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ExceptionIdentifier::class, function () {
            return new ExceptionIdentifier();
        });

        $this->app->singleton(ExceptionInfo::class, function () {
            $path = config('exceptions.views.json');
            return new ExceptionInfo($path);
        });

        $this->app->bind(HtmlDisplayer::class, function (Container $app) {
            $info = $app->make(ExceptionInfo::class);
            $generator = $app->make($this->app instanceof LumenApplication ? LumenGenerator::class : LaravelGenerator::class);
            $assets = function ($path) use ($generator) {
                return $generator->asset($path);
            };
            $path = config('exceptions.views.html');

            return new HtmlDisplayer($info, $assets, $path);
        });

        $this->app->bind(VerboseFilter::class, function (Container $app) {
            $debug = $app->config->get('app.debug', false);

            return new VerboseFilter($debug);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            //
        ];
    }
}
