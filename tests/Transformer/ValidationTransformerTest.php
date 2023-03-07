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

use Composer\InstalledVersions;
use GrahamCampbell\Exceptions\Transformer\ValidationTransformer;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use InvalidArgumentException;
use Mockery;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * This is the validation transformer test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class ValidationTransformerTest extends AbstractTestCase
{
    public function testTransformedWithSingleMessage(): void
    {
        $exception = new ValidationException($this->getMockedValidator(['Foo']));

        $transformed = (new ValidationTransformer())->transform($exception);

        self::assertInstanceOf(UnprocessableEntityHttpException::class, $transformed);
        self::assertSame('Foo', $transformed->getMessage());
        self::assertSame($exception, $transformed->getPrevious());
    }

    public function testTransformedWithTwoMessages(): void
    {
        $exception = new ValidationException($this->getMockedValidator(['Bar', 'Foo']));

        $transformed = (new ValidationTransformer())->transform($exception);

        self::assertInstanceOf(UnprocessableEntityHttpException::class, $transformed);
        self::assertSame('Bar', $transformed->getMessage());
        self::assertSame($exception, $transformed->getPrevious());
    }

    public function testNoChange(): void
    {
        $exception = new InvalidArgumentException();

        $transformed = (new ValidationTransformer())->transform($exception);

        self::assertSame($exception, $transformed);
    }

    private function getMockedValidator(array $messages): Validator
    {
        $validator = Mockery::mock(Validator::class);

        if (version_compare(InstalledVersions::getVersion('laravel/framework'), '9.27') >= 0) {
            $translator = Mockery::mock(Translator::class);
            $translator->shouldReceive('get')->andReturn('');
            $validator->shouldReceive('getTranslator')->andReturn($translator);
        }

        $validator->shouldReceive('errors')->andReturn(new MessageBag($messages));

        return $validator;
    }
}
