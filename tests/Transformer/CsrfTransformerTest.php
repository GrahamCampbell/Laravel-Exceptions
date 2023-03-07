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

use GrahamCampbell\Exceptions\Transformer\CsrfTransformer;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Session\TokenMismatchException;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the csrf transformer test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class CsrfTransformerTest extends AbstractTestCase
{
    public function testTransformedWithNewMessage(): void
    {
        $exception = new TokenMismatchException();

        $transformed = (new CsrfTransformer())->transform($exception);

        self::assertInstanceOf(BadRequestHttpException::class, $transformed);
        self::assertSame('CSRF token validation failed.', $transformed->getMessage());
        self::assertSame($exception, $transformed->getPrevious());
    }

    public function testTransformedWithOldMessage(): void
    {
        $exception = new TokenMismatchException('Foo!');

        $transformed = (new CsrfTransformer())->transform($exception);

        self::assertInstanceOf(BadRequestHttpException::class, $transformed);
        self::assertSame('Foo!', $transformed->getMessage());
        self::assertSame($exception, $transformed->getPrevious());
    }

    public function testNoChange(): void
    {
        $exception = new InvalidArgumentException();

        $transformed = (new CsrfTransformer())->transform($exception);

        self::assertSame($exception, $transformed);
    }
}
