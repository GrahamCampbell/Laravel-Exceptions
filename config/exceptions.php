<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Exception Displayer
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the exception displayers below you wish to
    | use as your default displayer for all exceptions.
    |
    */

    'default' => 'plain',

    /*
    |--------------------------------------------------------------------------
    | Exception Displayers
    |--------------------------------------------------------------------------
    |
    | Here are each of the exception displayers setup for your application.
    | Default displayers has been included, but you may add as many displayers
    | as you would like.
    |
    */

    'displayers' => [
        'array' => 'GrahamCampbell\Exceptions\Displayers\ArrayDisplayer',
        'debug' => 'GrahamCampbell\Exceptions\Displayers\DebugDisplayer',
        'plain' => 'GrahamCampbell\Exceptions\Displayers\PlainDisplayer',
    ],

];
