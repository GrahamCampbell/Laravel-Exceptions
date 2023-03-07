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
 * This is the verbose filter class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class VerboseFilter implements FilterInterface
{
    /**
     * Is debug mode enabled?
     *
     * @var bool
     */
    private bool $debug;

    /**
     * Create a new verbose filter instance.
     *
     * @param bool $debug
     *
     * @return void
     */
    public function __construct(bool $debug)
    {
        $this->debug = $debug;
    }

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
    public function filter(
        array $displayers,
        Request $request,
        Throwable $original,
        Throwable $transformed,
        int $code
    ): array {
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
