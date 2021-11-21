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

namespace GrahamCampbell\Exceptions\Displayer;

/**
 * This is the json displayer class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class JsonDisplayer extends AbstractJsonDisplayer
{
    /**
     * Get the supported content type.
     *
     * @return string
     */
    public function contentType()
    {
        return 'application/json';
    }
}
