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

use GrahamCampbell\Exceptions\Transformer\BadHeadersTransformer;
use GrahamCampbell\TestBench\AbstractTestCase;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the bad headers transformer test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class BadHeadersTransformerTest extends AbstractTestCase
{
    public function testTransformedWithNoMessage(): void
    {
        $exception = new ConflictingHeadersException();

        $transformed = (new BadHeadersTransformer())->transform($exception);

        self::assertInstanceOf(BadRequestHttpException::class, $transformed);
        self::assertSame('Bad headers provided.', $transformed->getMessage());
        self::assertSame($exception, $transformed->getPrevious());
    }

    public function testTransformedWithOriginalMessage(): void
    {
        $exception = new ConflictingHeadersException('Foo!');

        $transformed = (new BadHeadersTransformer())->transform($exception);

        self::assertInstanceOf(BadRequestHttpException::class, $transformed);
        self::assertSame('Bad headers provided.', $transformed->getMessage());
        self::assertSame($exception, $transformed->getPrevious());
    }

    public function testNoChange(): void
    {
        $exception = new InvalidArgumentException();

        $transformed = (new BadHeadersTransformer())->transform($exception);

        self::assertSame($exception, $transformed);
    }
}
