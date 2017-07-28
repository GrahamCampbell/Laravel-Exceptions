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

use GrahamCampbell\Exceptions\Transformers\CsrfTransformer;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Session\TokenMismatchException;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the csrf transformer test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class CsrfTransformerTest extends AbstractTestCase
{
    public function testTransformedWithNewMessage()
    {
        $exception = new TokenMismatchException();

        $transformed = (new CsrfTransformer())->transform($exception);

        $this->assertInstanceOf(BadRequestHttpException::class, $transformed);
        $this->assertSame('CSRF token validation failed.', $transformed->getMessage());
        $this->assertSame($exception, $transformed->getPrevious());
    }

    public function testTransformedWithOldMessage()
    {
        $exception = new TokenMismatchException('Foo!');

        $transformed = (new CsrfTransformer())->transform($exception);

        $this->assertInstanceOf(BadRequestHttpException::class, $transformed);
        $this->assertSame('Foo!', $transformed->getMessage());
        $this->assertSame($exception, $transformed->getPrevious());
    }

    public function testNoChange()
    {
        $exception = new InvalidArgumentException();

        $transformed = (new CsrfTransformer())->transform($exception);

        $this->assertSame($exception, $transformed);
    }
}
