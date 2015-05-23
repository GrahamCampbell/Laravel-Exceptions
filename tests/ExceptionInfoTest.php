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

/**
 * This is the exception info test class.
 *
 * @author Graham Campbell <graham@cachethq.io>
 */
class ExceptionInfoTest extends AbstractTestCase
{
    public function testExistingError()
    {
        $info = $this->app->make('GrahamCampbell\Exceptions\ExceptionInfo')->generate(400, 'Made a mess.');

        $expected = ['code' => 400, 'name' => 'Bad Request', 'message' => 'The request cannot be fulfilled due to bad syntax.', 'extra' => 'Made a mess.'];

        $this->assertSame($expected, $info);
    }

    public function testLongError()
    {
        $info = $this->app->make('GrahamCampbell\Exceptions\ExceptionInfo')->generate(422, 'Made a mess a really really big mess this time. Everything has broken, and unicorns are crying.');

        $expected = ['code' => 422, 'name' => 'Unprocessable Entity', 'message' => 'The request was well-formed but was unable to be followed due to semantic errors.', 'extra' => 'Houston, We Have A Problem.'];

        $this->assertSame($expected, $info);
    }

    public function testBadError()
    {
        $info = $this->app->make('GrahamCampbell\Exceptions\ExceptionInfo')->generate(666, 'Ooops.');

        $expected = ['code' => 500, 'name' => 'Internal Server Error', 'message' => 'An error has occurred and this resource cannot be displayed.', 'extra' => 'Ooops.'];

        $this->assertSame($expected, $info);
    }
}
