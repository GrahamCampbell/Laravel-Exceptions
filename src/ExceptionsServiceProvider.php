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

use GrahamCampbell\Exceptions\Displayer\HtmlDisplayer;
use GrahamCampbell\Exceptions\Filter\VerboseFilter;
use GrahamCampbell\Exceptions\Identifier\HashingIdentifier;
use GrahamCampbell\Exceptions\Identifier\IdentifierInterface;
use GrahamCampbell\Exceptions\Information\FactoryInterface;
use GrahamCampbell\Exceptions\Information\InformationFactory;
use GrahamCampbell\Exceptions\Information\InformationInterface;
use GrahamCampbell\Exceptions\Information\InformationMerger;
use GrahamCampbell\Exceptions\Information\MergerInterface;
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
        $this->app->singleton(IdentifierInterface::class, function () {
            return new HashingIdentifier();
        });

        $this->app->singleton(MergerInterface::class, function () {
            return new InformationMerger();
        });

        $this->app->singleton(FactoryInterface::class, function (Container $app) {
            $merger = $app->make(MergerInterface::class);

            return new InformationFactory($merger);
        });

        $this->app->singleton(InformationInterface::class, function (Container $app) {
            $factory = $app->make(FactoryInterface::class);
            $path = __DIR__.'/../resources/errors.json';

            return $factory->create(realpath($path));
        });

        $this->app->bind(HtmlDisplayer::class, function (Container $app) {
            $info = $app->make(InformationInterface::class);
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
