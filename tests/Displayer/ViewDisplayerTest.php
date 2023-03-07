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

namespace GrahamCampbell\Tests\Exceptions\Displayer;

use Exception;
use GrahamCampbell\Exceptions\Displayer\ViewDisplayer;
use GrahamCampbell\Exceptions\Information\InformationFactory;
use GrahamCampbell\Exceptions\Information\InformationMerger;
use GrahamCampbell\Tests\Exceptions\AbstractTestCase;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * This is the view displayer test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class ViewDisplayerTest extends AbstractTestCase
{
    public function testError(): void
    {
        $view = Mockery::mock(View::class);
        $view->shouldReceive('with')->once()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn("Gutted.\n");

        $factory = Mockery::mock(Factory::class);
        $factory->shouldReceive('make')
            ->once()
            ->with('errors.502', ['id' => 'foo', 'code' => 502, 'name' => 'Bad Gateway', 'detail' => 'Oh noes!'])
            ->andReturn($view);

        $displayer = new ViewDisplayer((new InformationFactory(new InformationMerger()))->create(__DIR__.'/../../resources/lang/en/errors.json'), $factory);
        $response = $displayer->display(new HttpException(502, 'Oh noes!'), 'foo', 502, []);

        self::assertSame("Gutted.\n", $response->getContent());
        self::assertSame(502, $response->getStatusCode());
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testPropertiesTrue(): void
    {
        $displayer = new ViewDisplayer((new InformationFactory(new InformationMerger()))
            ->create(__DIR__.'/../../resources/lang/en/errors.json'), $factory = Mockery::mock(Factory::class));

        $factory->shouldReceive('exists')->once()->with('errors.500')->andReturn(true);

        self::assertFalse($displayer->isVerbose());
        self::assertTrue($displayer->canDisplay(new Exception(), new HttpException(500), 500));
        self::assertSame('text/html', $displayer->contentType());
    }

    public function testPropertiesFalse(): void
    {
        $displayer = new ViewDisplayer((new InformationFactory(new InformationMerger()))
            ->create(__DIR__.'/../../resources/lang/en/errors.json'), $factory = Mockery::mock(Factory::class));

        $factory->shouldReceive('exists')->once()->with('errors.500')->andReturn(false);

        self::assertFalse($displayer->isVerbose());
        self::assertFalse($displayer->canDisplay(new Exception(), new HttpException(500), 500));
        self::assertSame('text/html', $displayer->contentType());
    }
}
