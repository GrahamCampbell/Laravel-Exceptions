<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Displayers;

use Exception;
use GrahamCampbell\Exceptions\Traits\InfoTrait;

/**
 * This is the array displayer class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class ArrayDisplayer implements DisplayerInterface
{
    use InfoTrait;

    /**
     * Get the content associated with the given exception.
     *
     * @param \Exception $exception
     * @param int        $code
     *
     * @return array
     */
    public function display(Exception $exception, $code)
    {
        $info = $this->info($code, $exception->getMessage());

        return ['success' => false, 'code' => $info['code'], 'msg' => $info['extra']];
    }
}
