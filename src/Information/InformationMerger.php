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
use ValueError;

/**
 * This is the information merger class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
final class InformationMerger implements MergerInterface
{
    /**
     * @param string
     */
    private $defaultSummary;

    /**
     * Create a new information merger instance.
     *
     * @param string|null $defaultSummary
     *
     * @return void
     */
    public function __construct(string $defaultSummary = null)
    {
        if ($this->defaultSummary !== null && (strlen($defaultSummary) > 35 || strlen($defaultSummary) < 5)) {
            throw new ValueError(
                sprintf(
                    '%s::__construct(): Argument #1 ($defaultSummary) must be either null or a string of length between 5 and 35',
                    self::class,
                )
            );
        }

        $this->defaultSummary = $defaultSummary ?? 'Houston, We Have A Problem.';
    }

    /**
     * Merge the exception information.
     *
     * @param array      $info
     * @param \Throwable $exception
     *
     * @return array
     */
    public function merge(array $info, Throwable $exception)
    {
        if ($exception instanceof HttpExceptionInterface) {
            $msg = (string) $exception->getMessage();
            $info['detail'] = (strlen($msg) > 4) ? $msg : $info['message'];
            $info['summary'] = (strlen($msg) < 36 && strlen($msg) > 4) ? $msg : $this->defaultSummary;
        } else {
            $info['detail'] = $info['message'];
            $info['summary'] = $this->defaultSummary;
        }

        unset($info['message']);

        return $info;
    }
}
