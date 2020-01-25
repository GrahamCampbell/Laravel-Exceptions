<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Information;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * This is the array information class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
final class ArrayInformation implements InformationInterface
{
    /**
     * The raw data array.
     *
     * @var array[]
     */
    private $data;

    /**
     * Create a new array information instance.
     *
     * @param array[] $data
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the exception information.
     *
     * @param \Throwable $exception
     * @param string     $id
     * @param int        $code
     *
     * @return array
     */
    public function generate(Throwable $exception, string $id, int $code)
    {
        if (isset($this->data[$code])) {
            $info = array_merge(['id' => $id, 'code' => $code], $this->data[$code]);
        } else {
            $info = array_merge(['id' => $id, 'code' => 500], $this->data[500]);
        }

        return InformationMerger::merge($info, $exception);
    }
}
