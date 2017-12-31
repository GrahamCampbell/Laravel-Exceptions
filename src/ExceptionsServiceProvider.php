<?php

declare(strict_types=1);

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
        $source = realpath($raw = __DIR__.'/../config/exceptions.php') ?: $raw;

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

        $this->app->singleton(ExceptionInfoInterface::class, function () {
            $path = __DIR__.'/../resources/errors.json';

            return new ExceptionInfo(realpath($path));
        });

        $this->app->bind(HtmlDisplayer::class, function (Container $app) {
            $info = $app->make(ExceptionInfoInterface::class);
            $generator = $app->make($this->app instanceof LumenApplication ? LumenGenerator::class : LaravelGenerator::class);
            $assets = function ($path) use ($generator) {
                return $generator->asset($path);
            };
            $path = __DIR__.'/../resources/error.html';

            return new HtmlDisplayer($info, $assets, realpath($path));
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
