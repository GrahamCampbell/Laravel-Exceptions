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
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

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
        $source = realpath(__DIR__.'/../config/exceptions.php');

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('exceptions.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('exceptions');
        }

        $this->mergeConfigFrom($source, 'exceptions');
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
            return new ExceptionInfo(__DIR__.'/../resources/errors.json');
        });

        $this->app->bind(HtmlDisplayer::class, function (Container $app) {
            return new HtmlDisplayer($app->make(ExceptionInfo::class), __DIR__.'/../resources/error.html');
        });

        $this->app->bind(VerboseFilter::class, function (Container $app) {
            return new VerboseFilter($app->config->get('app.debug', false));
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
