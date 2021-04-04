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
use RuntimeException;
use Throwable;
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

        $this->app->bind(InformationInterface::class, function (Container $app) {
            $factory = $app->make(FactoryInterface::class);
            $path = self::getLocalizedResourcePath($app, 'errors.json');

            return $factory->create($path);
        });

        $this->app->bind(HtmlDisplayer::class, function (Container $app) {
            $info = $app->make(InformationInterface::class);
            $generator = $app->make($this->app instanceof LumenApplication ? LumenGenerator::class : LaravelGenerator::class);
            $assets = function ($path) use ($generator) {
                return $generator->asset($path);
            };
            $path = self::getLocalizedResourcePath($app, 'error.html');

            return new HtmlDisplayer($info, $assets, $path);
        });

        $this->app->bind(VerboseFilter::class, function (Container $app) {
            $debug = $app->config->get('app.debug', false);

            return new VerboseFilter($debug);
        });
    }

    /**
     * Get the localized resource path.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     * @param string                                    $file
     *
     * @return string
     */
    private static function getLocalizedResourcePath(Container $app, string $file)
    {
        try {
            $locale = $app->make('translator')->getLocale();

            if ($locale && is_dir(__DIR__.'/../resources/lang/'.$locale)) {
                return realpath(__DIR__.'/../resources/lang/'.$locale.'/'.$file);
            } else {
                throw new RuntimeException('Invalid locale.');
            }
        } catch (Throwable $e) {
            return realpath(__DIR__.'/../resources/lang/en/'.$file);
        }
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
