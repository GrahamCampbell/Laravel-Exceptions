<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Displayers;

/**
 * This is the json api displayer class.
 *
 * @author Graham Campbell <graham@cachethq.io>
 */
class JsonApiDisplayer extends JsonDisplayer implements DisplayerInterface
{
    /**
     * Get the supported content type.
     *
     * @return string
     */
    public function contentType()
    {
        return 'application/vnd.api+json';
    }
}
