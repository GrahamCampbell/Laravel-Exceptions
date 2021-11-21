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

use GrahamCampbell\Exceptions\Transformer\ModelTransformer;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the model transformer test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class ModelTransformerTest extends AbstractTestCase
{
    public function testTransformedWithNoMessage()
    {
        $exception = new ModelNotFoundException();

        $transformed = (new ModelTransformer())->transform($exception);

        $this->assertInstanceOf(NotFoundHttpException::class, $transformed);
        $this->assertEmpty($transformed->getMessage());
        $this->assertSame($exception, $transformed->getPrevious());
    }

    public function testTransformedWithOriginalMessage()
    {
        $exception = new ModelNotFoundException('Foo!');

        $transformed = (new ModelTransformer())->transform($exception);

        $this->assertInstanceOf(NotFoundHttpException::class, $transformed);
        $this->assertSame('Foo!', $transformed->getMessage());
        $this->assertSame($exception, $transformed->getPrevious());
    }

    public function testNoChange()
    {
        $exception = new InvalidArgumentException();

        $transformed = (new ModelTransformer())->transform($exception);

        $this->assertSame($exception, $transformed);
    }
}
