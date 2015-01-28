<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions;

use Illuminate\Support\ServiceProvider;

/**
 * This is the exceptions service provider class.
 *
 * @author Graham Campbell <graham@mineuk.com>
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
        $this->loadViewsFrom('exceptions', realpath(__DIR__.'/../views'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
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
