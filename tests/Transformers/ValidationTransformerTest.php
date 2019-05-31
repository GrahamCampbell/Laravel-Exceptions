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

use GrahamCampbell\Exceptions\Transformers\ValidationTransformer;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the validation transformer test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ValidationTransformerTest extends AbstractTestCase
{
    public function testTransformedWithSingleMessage()
    {
        $exception = new ValidationException($this->getMockedValidator(['Foo']));

        $transformed = (new ValidationTransformer())->transform($exception);

        $this->assertInstanceOf(NotFoundHttpException::class, $transformed);
        $this->assertSame('Foo', $transformed->getMessage());
        $this->assertSame($exception, $transformed->getPrevious());
    }

    public function testTransformedWithTwoMessages()
    {
        $exception = new ValidationException($this->getMockedValidator(['Bar', 'Foo']));

        $transformed = (new ValidationTransformer())->transform($exception);

        $this->assertInstanceOf(NotFoundHttpException::class, $transformed);
        $this->assertSame('Bar', $transformed->getMessage());
        $this->assertSame($exception, $transformed->getPrevious());
    }

    public function testNoChange()
    {
        $exception = new InvalidArgumentException();

        $transformed = (new ValidationTransformer())->transform($exception);

        $this->assertSame($exception, $transformed);
    }

    protected function getMockedValidator(array $messages)
    {
        $validator = Mockery::mock(Validator::class);

        $validator->shouldReceive('errors')->once()->andReturn(new MessageBag($messages));

        return $validator;
    }
}
