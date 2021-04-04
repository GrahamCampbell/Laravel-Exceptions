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
 * @author Graham Campbell <graham@alt-three.com>
 */
class ViewDisplayerTest extends AbstractTestCase
{
    public function testError()
    {
        $view = Mockery::mock(View::class);
        $view->shouldReceive('with')->once()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn("Gutted.\n");

        $factory = Mockery::mock(Factory::class);
        $factory->shouldReceive('make')->once()->with('errors.502', ['id' => 'foo', 'code' => 502, 'name' => 'Bad Gateway', 'detail' => 'Oh noes!', 'summary' => 'Oh noes!'])->andReturn($view);

        $displayer = new ViewDisplayer((new InformationFactory(new InformationMerger()))->create(__DIR__.'/../../resources/lang/en/errors.json'), $factory);
        $response = $displayer->display(new HttpException(502, 'Oh noes!'), 'foo', 502, []);

        $this->assertSame("Gutted.\n", $response->getContent());
        $this->assertSame(502, $response->getStatusCode());
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testPropertiesTrue()
    {
        $displayer = new ViewDisplayer((new InformationFactory(new InformationMerger()))->create(__DIR__.'/../../resources/lang/en/errors.json'), $factory = Mockery::mock(Factory::class));

        $factory->shouldReceive('exists')->once()->with('errors.500')->andReturn(true);

        $this->assertFalse($displayer->isVerbose());
        $this->assertTrue($displayer->canDisplay(new Exception(), new HttpException(500), 500));
        $this->assertSame('text/html', $displayer->contentType());
    }

    public function testPropertiesFalse()
    {
        $displayer = new ViewDisplayer((new InformationFactory(new InformationMerger()))->create(__DIR__.'/../../resources/lang/en/errors.json'), $factory = Mockery::mock(Factory::class));

        $factory->shouldReceive('exists')->once()->with('errors.500')->andReturn(false);

        $this->assertFalse($displayer->isVerbose());
        $this->assertFalse($displayer->canDisplay(new Exception(), new HttpException(500), 500));
        $this->assertSame('text/html', $displayer->contentType());
    }
}
