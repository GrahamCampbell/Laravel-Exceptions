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
use Illuminate\Http\Request;

/**
 * This is the displayer interface.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
interface DisplayerInterface
{
    /**
     * Get the error response associated with the given exception.
     *
     * @param \Exception $exception
     * @param int        $code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function display(Exception $exception, $code);

    /**
     * Get the supported content type.
     *
     * @return string
     */
    public function contentType();

    /**
     * Can we display the exception in the given context?
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return bool
     */
    public function canDisplay(Request $request, Exception $exception);

    /**
     * Do we provide verbose information about the exception?
     *
     * @return bool
     */
    public function isVerbose();
}
