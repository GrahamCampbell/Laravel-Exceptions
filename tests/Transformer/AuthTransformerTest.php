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

use GrahamCampbell\Exceptions\Transformer\AuthTransformer;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Auth\Access\AuthorizationException;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * This is the auth transformer test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class AuthTransformerTest extends AbstractTestCase
{
    public function testTransformedWithOriginalMessage(): void
    {
        $exception = new AuthorizationException('Foo!');

        $transformed = (new AuthTransformer())->transform($exception);

        self::assertInstanceOf(AccessDeniedHttpException::class, $transformed);
        self::assertSame('Foo!', $transformed->getMessage());
        self::assertSame($exception, $transformed->getPrevious());
    }

    public function testNoChange(): void
    {
        $exception = new InvalidArgumentException();

        $transformed = (new AuthTransformer())->transform($exception);

        self::assertSame($exception, $transformed);
    }
}
