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

namespace GrahamCampbell\Exceptions\Information;

use Throwable;

/**
 * This is the array information class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class ArrayInformation implements InformationInterface
{
    /**
     * The information merger.
     *
     * @var \GrahamCampbell\Exceptions\Information\MergerInterface
     */
    private $merger;

    /**
     * The raw data array.
     *
     * @var array[]
     */
    private $data;

    /**
     * Create a new array information instance.
     *
     * @param \GrahamCampbell\Exceptions\Information\MergerInterface $merger
     * @param array[]                                                $data
     *
     * @return void
     */
    public function __construct(MergerInterface $merger, array $data)
    {
        $this->merger = $merger;
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

        return $this->merger->merge($info, $exception);
    }
}
