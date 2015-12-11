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
use Illuminate\Http\Request;

/**
 * This is the content type filter class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ContentTypeFilter
{
    /**
     * Filter and return the displayers.
     *
     * @param \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[] $displayers
     * @param \Illuminate\Http\Request                                   $request
     * @param \Exception                                                 $original
     * @param \Exception                                                 $transformed
     * @param int                                                        $code
     *
     * @return \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[]
     */
    public function filter(array $displayers, Request $request, Exception $original, Exception $transformed, $code)
    {
        foreach ($displayers as $index => $displayer) {
            if (!$request->accepts($displayer->contentType())) {
                unset($displayers[$index]);
            }
        }

        return array_values($displayers);
    }
}
