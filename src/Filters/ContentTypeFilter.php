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
use GrahamCampbell\Exceptions\Displayers\DisplayerInterface;

/**
 * This is the content type filter class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class ContentTypeFilter
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
            foreach ($this->getContentTypes($displayer) as $type) {
                if (in_array($type, $acceptable)) {
                    continue 2;
                }
            }

            $split = explode('/', $displayer->contentType());

            foreach ($acceptable as $type) {
                if (preg_match('/'.$split[0].'\/.+\+'.$split[1].'/', $type)) {
                    continue 2;
                }
            }

            unset($displayers[$index]);
        }

        return $displayers;
    }

    /**
     * Get the content types to match.
     *
     * @param \GrahamCampbell\Exceptions\Displayers\DisplayerInterface $displayer
     *
     * @return string[]
     */
    protected function getContentTypes(DisplayerInterface $displayer)
    {
        $type = $displayer->contentType();

        return ['*/*', $type, strtok($type, '/').'/*'];
    }
}
