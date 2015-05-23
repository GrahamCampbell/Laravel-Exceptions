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

use GrahamCampbell\TestBench\Traits\ServiceProviderTestCaseTrait;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <graham@cachethq.io>
 */
class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTestCaseTrait;

    public function testExceptionHandlerIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Exceptions\ExceptionHandler');
    }

    public function testJsonDisplayerIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Exceptions\Displayers\JsonDisplayer');
    }

    public function testDebugDisplayerIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Exceptions\Displayers\DebugDisplayer');
    }

    public function testHtmlDisplayerIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Exceptions\Displayers\HtmlDisplayer');
    }

    public function testCanDisplayFilterIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Exceptions\Filters\CanDisplayFilter');
    }

    public function testContentTypeFilterIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Exceptions\Filters\ContentTypeFilter');
    }

    public function testVerboseFilterIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Exceptions\Filters\VerboseFilter');
    }

    public function testDisplayerConfig()
    {
        $displayers = $this->app->config->get('exceptions.displayers');

        $this->assertInternalType('array', $displayers);
        $this->assertCount(3, $displayers);

        foreach ($displayers as $displayer) {
            $this->assertTrue(starts_with($displayer, 'GrahamCampbell\\Exceptions\\Displayers\\'));
        }
    }

    public function testFilterConfig()
    {
        $filters = $this->app->config->get('exceptions.filters');

        $this->assertInternalType('array', $filters);
        $this->assertCount(3, $filters);

        foreach ($filters as $filter) {
            $this->assertTrue(starts_with($filter, 'GrahamCampbell\\Exceptions\\Filters\\'));
        }
    }
}
