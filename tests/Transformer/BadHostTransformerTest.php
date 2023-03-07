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

namespace GrahamCampbell\Tests\Exceptions\Transformer;

use GrahamCampbell\Exceptions\Transformer\BadHostTransformer;
use GrahamCampbell\TestBench\AbstractTestCase;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the bad host transformer test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class BadHostTransformerTest extends AbstractTestCase
{
    public function testTransformedWithNoMessage(): void
    {
        $exception = new SuspiciousOperationException();

        $transformed = (new BadHostTransformer())->transform($exception);

        self::assertInstanceOf(NotFoundHttpException::class, $transformed);
        self::assertSame('Bad hostname provided.', $transformed->getMessage());
        self::assertSame($exception, $transformed->getPrevious());
    }

    public function testTransformedWithOriginalMessage(): void
    {
        $exception = new SuspiciousOperationException('Foo!');

        $transformed = (new BadHostTransformer())->transform($exception);

        self::assertInstanceOf(NotFoundHttpException::class, $transformed);
        self::assertSame('Bad hostname provided.', $transformed->getMessage());
        self::assertSame($exception, $transformed->getPrevious());
    }

    public function testNoChange(): void
    {
        $exception = new InvalidArgumentException();

        $transformed = (new BadHostTransformer())->transform($exception);

        self::assertSame($exception, $transformed);
    }
}
