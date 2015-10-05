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
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new content type filter instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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
            if (!$this->request->accepts($displayer->contentType())) {
                unset($displayers[$index]);
            }
        }

        return array_values($displayers);
    }
}
