<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Filters;

use Exception;

/**
 * This is the can display filter class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class CanDisplayFilter
{
    /**
     * Filter and return the displayers.
     *
     * @param \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[] $displayers
     * @param \Exception                                                 $original
     * @param \Exception                                                 $transformed
     * @param int                                                        $code
     *
     * @return \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[]
     */
    public function filter(array $displayers, Exception $original, Exception $transformed, $code)
    {
        foreach ($displayers as $index => $displayer) {
            if (!$displayer->canDisplay($original, $transformed, $code)) {
                unset($displayers[$index]);
            }
        }

        return array_values($displayers);
    }
}
