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
 * This is the verbose filter class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class VerboseFilter
{
    /**
     * Is debug mode enabled?
     *
     * @var bool
     */
    protected $debug;

    /**
     * Create a new verbose filter instance.
     *
     * @param bool $debug
     *
     * @return void
     */
    public function __construct($debug)
    {
        $this->debug = $debug;
    }

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
        if ($this->debug !== true) {
            foreach ($displayers as $index => $displayer) {
                if ($displayer->isVerbose()) {
                    unset($displayers[$index]);
                }
            }
        }

        return array_values($displayers);
    }
}
