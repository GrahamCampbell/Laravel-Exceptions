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

use Throwable;

/**
 * This is the null information class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
final class NullInformation implements InformationInterface
{
    /**
     * The information merger.
     *
     * @var \GrahamCampbell\Exceptions\Information\MergerInterface
     */
    private $merger;

    /**
     * Create a new null information instance.
     *
     * @param \GrahamCampbell\Exceptions\Information\MergerInterface $merger
     *
     * @return void
     */
    public function __construct(MergerInterface $merger)
    {
        $this->merger = $merger;
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
        $info = [
            'id'      => $id,
            'code'    => 500,
            'name'    => 'Internal Server Error',
            'message' => 'An error has occurred and this resource cannot be displayed.',
        ];

        return $this->merger->merge($info, $exception);
    }
}
