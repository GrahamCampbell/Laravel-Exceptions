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

namespace GrahamCampbell\Tests\Exceptions;

use GrahamCampbell\Exceptions\Displayers\DebugDisplayer;
use GrahamCampbell\Exceptions\Displayers\HtmlDisplayer;
use GrahamCampbell\Exceptions\Displayers\JsonApiDisplayer;
use GrahamCampbell\Exceptions\Displayers\JsonDisplayer;
use GrahamCampbell\Exceptions\ExceptionHandler;
use GrahamCampbell\Exceptions\Filters\CanDisplayFilter;
use GrahamCampbell\Exceptions\Filters\ContentTypeFilter;
use GrahamCampbell\Exceptions\Filters\VerboseFilter;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use Illuminate\Support\Str;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testExceptionHandlerIsInjectable()
    {
        $this->assertIsInjectable(ExceptionHandler::class);
    }

    public function testJsonApiDisplayerIsInjectable()
    {
        $this->assertIsInjectable(JsonApiDisplayer::class);
    }

    public function testJsonDisplayerIsInjectable()
    {
        $this->assertIsInjectable(JsonDisplayer::class);
    }

    public function testDebugDisplayerIsInjectable()
    {
        $this->assertIsInjectable(DebugDisplayer::class);
    }

    public function testHtmlDisplayerIsInjectable()
    {
        $this->assertIsInjectable(HtmlDisplayer::class);
    }

    public function testCanDisplayFilterIsInjectable()
    {
        $this->assertIsInjectable(CanDisplayFilter::class);
    }

    public function testContentTypeFilterIsInjectable()
    {
        $this->assertIsInjectable(ContentTypeFilter::class);
    }

    public function testVerboseFilterIsInjectable()
    {
        $this->assertIsInjectable(VerboseFilter::class);
    }

    public function testDisplayerConfig()
    {
        $displayers = $this->app->config->get('exceptions.displayers');

        $this->assertCount(5, $displayers);

        foreach ($displayers as $displayer) {
            $this->assertTrue(Str::startsWith($displayer, 'GrahamCampbell\Exceptions\Displayers'));
        }
    }

    public function testFilterConfig()
    {
        $filters = $this->app->config->get('exceptions.filters');

        $this->assertCount(3, $filters);

        foreach ($filters as $filter) {
            $this->assertTrue(Str::startsWith($filter, 'GrahamCampbell\Exceptions\Filters'));
        }
    }
}
