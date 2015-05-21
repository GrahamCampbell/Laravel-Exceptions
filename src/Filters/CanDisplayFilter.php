<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Filters;

use Exception;

/**
 * This is the can display filter class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class CanDisplayFilter
{
    /**
     * Filter and return the displayers.
     *
     * @param \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[] $displayers
     * @param \Exception                                                 $exception
     *
     * @return \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[]
     */
    public function filter(array $displayers, Exception $exception)
    {
        foreach ($displayers as $index => $displayer) {
            if (!$displayer->canDisplay($exception)) {
                unset($displayers[$index]);
            }
        }
    }
}
