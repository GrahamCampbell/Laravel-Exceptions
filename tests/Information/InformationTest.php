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

namespace GrahamCampbell\Tests\Exceptions\Information;

use Exception;
use GrahamCampbell\Exceptions\Information\InformationInterface;
use GrahamCampbell\Tests\Exceptions\AbstractTestCase;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * This is the exception information test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class InformationTest extends AbstractTestCase
{
    public function testExistingError(): void
    {
        $info = $this->app->make(InformationInterface::class)
            ->generate(new BadRequestHttpException('Made a mess.'), 'foo', 400);

        $expected = ['id' => 'foo', 'code' => 400, 'name' => 'Bad Request', 'detail' => 'Made a mess.'];

        self::assertSame($expected, $info);
    }

    public function testShortError(): void
    {
        $info = $this->app->make(InformationInterface::class)
            ->generate(new PreconditionFailedHttpException(':('), 'bar', 412);

        $expected = ['id' => 'bar', 'code' => 412, 'name' => 'Precondition Failed', 'detail' => 'The server does not meet one of the preconditions that the requester put on the request.'];

        self::assertSame($expected, $info);
    }

    public function testLongError(): void
    {
        $info = $this->app->make(InformationInterface::class)
            ->generate(new UnprocessableEntityHttpException('Made a mess a really really big mess this time. Everything has broken, and unicorns are crying.'), 'baz', 422);

        $expected = ['id' => 'baz', 'code' => 422, 'name' => 'Unprocessable Entity', 'detail' => 'Made a mess a really really big mess this time. Everything has broken, and unicorns are crying.'];

        self::assertSame($expected, $info);
    }

    public function testBadError(): void
    {
        $info = $this->app->make(InformationInterface::class)
            ->generate(new Exception('Ooops.'), 'test', 666);

        $expected = ['id' => 'test', 'code' => 500, 'name' => 'Internal Server Error', 'detail' => 'An error has occurred and this resource cannot be displayed.'];

        self::assertSame($expected, $info);
    }

    public function testHiddenError(): void
    {
        $info = $this->app->make(InformationInterface::class)
            ->generate(new InvalidArgumentException('Made another mess.'), 'hi', 503);

        $expected = ['id' => 'hi', 'code' => 503, 'name' => 'Service Unavailable', 'detail' => 'The server is currently unavailable. It may be overloaded or down for maintenance.'];

        self::assertSame($expected, $info);
    }
}
