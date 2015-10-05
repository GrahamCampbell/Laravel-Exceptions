<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Displayers;

use Exception;

/**
 * This is the displayer interface.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
interface DisplayerInterface
{
    /**
     * Get the error response associated with the given exception.
     *
     * @param \Exception $exception
     * @param string     $id
     * @param int        $code
     * @param string[]   $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function display(Exception $exception, $id, $code, array $headers);

    /**
     * Get the supported content type.
     *
     * @return string
     */
    public function contentType();

    /**
     * Can we display the exception?
     *
     * @param \Exception $original
     * @param \Exception $transformed
     * @param int        $code
     *
     * @return bool
     */
    public function canDisplay(Exception $original, Exception $transformed, $code);

    /**
     * Do we provide verbose information about the exception?
     *
     * @return bool
     */
    public function isVerbose();
}
