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

namespace GrahamCampbell\Tests\Exceptions\Transformers;

use GrahamCampbell\Exceptions\Transformers\AuthTransformer;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Auth\Access\AuthorizationException;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * This is the auth transformer test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class AuthTransformerTest extends AbstractTestCase
{
    public function testTransformedWithNoMessage()
    {
        if (!class_exists(AuthorizationException::class)) {
            $this->markTestSkipped('Laravel version too old.');
        }

        $exception = new AuthorizationException();

        $transformed = (new AuthTransformer())->transform($exception);

        $this->assertInstanceOf(AccessDeniedHttpException::class, $transformed);
        $this->assertEmpty($transformed->getMessage());
        $this->assertSame($exception, $transformed->getPrevious());
    }

    public function testTransformedWithOriginalMessage()
    {
        if (!class_exists(AuthorizationException::class)) {
            $this->markTestSkipped('Laravel version too old.');
        }

        $exception = new AuthorizationException('Foo!');

        $transformed = (new AuthTransformer())->transform($exception);

        $this->assertInstanceOf(AccessDeniedHttpException::class, $transformed);
        $this->assertSame('Foo!', $transformed->getMessage());
        $this->assertSame($exception, $transformed->getPrevious());
    }

    public function testNoChange()
    {
        $exception = new InvalidArgumentException();

        $transformed = (new AuthTransformer())->transform($exception);

        $this->assertSame($exception, $transformed);
    }
}
