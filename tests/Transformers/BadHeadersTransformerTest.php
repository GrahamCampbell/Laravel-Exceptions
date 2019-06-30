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

use GrahamCampbell\Exceptions\Transformers\BadHeadersTransformer;
use GrahamCampbell\TestBench\AbstractTestCase;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the bad headers transformer test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class BadHeadersTransformerTest extends AbstractTestCase
{
    public function testTransformedWithNoMessage()
    {
        $exception = new ConflictingHeadersException();

        $transformed = (new BadHeadersTransformer())->transform($exception);

        $this->assertInstanceOf(BadRequestHttpException::class, $transformed);
        $this->assertSame('Bad headers provided.', $transformed->getMessage());
        $this->assertSame($exception, $transformed->getPrevious());
    }

    public function testTransformedWithOriginalMessage()
    {
        $exception = new ConflictingHeadersException('Foo!');

        $transformed = (new BadHeadersTransformer())->transform($exception);

        $this->assertInstanceOf(BadRequestHttpException::class, $transformed);
        $this->assertSame('Bad headers provided.', $transformed->getMessage());
        $this->assertSame($exception, $transformed->getPrevious());
    }

    public function testNoChange()
    {
        $exception = new InvalidArgumentException();

        $transformed = (new BadHeadersTransformer())->transform($exception);

        $this->assertSame($exception, $transformed);
    }
}
