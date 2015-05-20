<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Displayers;

use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

/**
 * This is the displayer interface.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
interface DisplayerInterface
{
    /**
     * Get the content associated with the given exception.
     *
     * @param \Exception $exception
     * @param int        $code
     *
     * @return string|array
     */
    public function display(Exception $exception, $code);

    /**
     * Can the exception be displayed?
     *
     * @param \Exception                              $exception
     * @param \Illuminate\Http\Request                $request
     * @param \Illuminate\Contracts\Config\Repository $config
     *
     * @return bool
     */
    public function canDisplay(Exception $exception, Request $request, Repository $config);
}
