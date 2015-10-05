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
use Illuminate\Contracts\Config\Repository as Config;

/**
 * This is the verbose filter class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class VerboseFilter
{
    /**
     * The config instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Create a new verbose filter instance.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     *
     * @return void
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
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
        if ($this->config->get('app.debug', false) !== true) {
            foreach ($displayers as $index => $displayer) {
                if ($displayer->isVerbose()) {
                    unset($displayers[$index]);
                }
            }
        }

        return array_values($displayers);
    }
}
