<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Exceptions;

use Exception;
use GrahamCampbell\Exceptions\ExceptionInfo;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * This is the exception info test class.
 *
 * @author Graham Campbell <graham@cachethq.io>
 */
class ExceptionInfoTest extends AbstractTestCase
{
    public function testExistingError()
    {
        $info = $this->app->make(ExceptionInfo::class)->generate(new BadRequestHttpException('Made a mess.'), 400);

        $expected = ['code' => 400, 'name' => 'Bad Request', 'detail' => 'Made a mess.', 'summary' => 'Made a mess.'];

        $this->assertSame($expected, $info);
    }

    public function testShortError()
    {
        $info = $this->app->make(ExceptionInfo::class)->generate(new PreconditionFailedHttpException(':('), 412);

        $expected = ['code' => 412, 'name' => 'Precondition Failed', 'detail' => 'The server does not meet one of the preconditions that the requester put on the request.', 'summary' => 'Houston, We Have A Problem.'];

        $this->assertSame($expected, $info);
    }

    public function testLongError()
    {
        $info = $this->app->make(ExceptionInfo::class)->generate(new UnprocessableEntityHttpException('Made a mess a really really big mess this time. Everything has broken, and unicorns are crying.'), 422);

        $expected = ['code' => 422, 'name' => 'Unprocessable Entity', 'detail' => 'Made a mess a really really big mess this time. Everything has broken, and unicorns are crying.', 'summary' => 'Houston, We Have A Problem.'];

        $this->assertSame($expected, $info);
    }

    public function testBadError()
    {
        $info = $this->app->make(ExceptionInfo::class)->generate(new Exception('Ooops.'), 666);

        $expected = ['code' => 500, 'name' => 'Internal Server Error', 'detail' => 'An error has occurred and this resource cannot be displayed.', 'summary' => 'Houston, We Have A Problem.'];

        $this->assertSame($expected, $info);
    }

    public function testHiddenError()
    {
        $info = $this->app->make(ExceptionInfo::class)->generate(new InvalidArgumentException('Made another mess.'), 503);

        $expected = ['code' => 503, 'name' => 'Service Unavailable', 'detail' => 'The server is currently unavailable. It may be overloaded or down for maintenance.', 'summary' => 'Houston, We Have A Problem.'];

        $this->assertSame($expected, $info);
    }
}
