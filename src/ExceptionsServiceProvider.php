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
use Illuminate\Contracts\Foundation\Application;
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
        $this->setupConfig($this->app);
    }

    /**
     * Setup the config.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function setupConfig(Application $app)
    {
        $source = realpath(__DIR__.'/../config/exceptions.php');

        if ($app instanceof LaravelApplication && $app->runningInConsole()) {
            $this->publishes([$source => config_path('exceptions.php')]);
        } elseif ($app instanceof LumenApplication) {
            $app->configure('exceptions');
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

        $this->app->bind(HtmlDisplayer::class, function ($app) {
            return new HtmlDisplayer($app->make(ExceptionInfo::class), __DIR__.'/../resources/error.html');
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
