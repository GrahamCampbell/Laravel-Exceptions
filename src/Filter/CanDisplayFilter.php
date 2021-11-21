<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Filter;

use Illuminate\Http\Request;
use Throwable;

/**
 * This is the can display filter class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class CanDisplayFilter implements FilterInterface
{
    /**
     * Filter and return the displayers.
     *
     * @param \GrahamCampbell\Exceptions\Displayer\DisplayerInterface[] $displayers
     * @param \Illuminate\Http\Request                                  $request
     * @param \Throwable                                                $original
     * @param \Throwable                                                $transformed
     * @param int                                                       $code
     *
     * @return \GrahamCampbell\Exceptions\Displayer\DisplayerInterface[]
     */
    public function filter(array $displayers, Request $request, Throwable $original, Throwable $transformed, int $code)
    {
        foreach ($displayers as $index => $displayer) {
            if (!$displayer->canDisplay($original, $transformed, $code)) {
                unset($displayers[$index]);
            }
        }

        return array_values($displayers);
    }
}
