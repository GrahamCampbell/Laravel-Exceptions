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

namespace GrahamCampbell\Tests\Exceptions;

use GrahamCampbell\Exceptions\Displayer\DebugDisplayer;
use GrahamCampbell\Exceptions\Displayer\HtmlDisplayer;
use GrahamCampbell\Exceptions\Displayer\JsonApiDisplayer;
use GrahamCampbell\Exceptions\Displayer\JsonDisplayer;
use GrahamCampbell\Exceptions\ExceptionHandler;
use GrahamCampbell\Exceptions\Filter\CanDisplayFilter;
use GrahamCampbell\Exceptions\Filter\ContentTypeFilter;
use GrahamCampbell\Exceptions\Filter\VerboseFilter;
use GrahamCampbell\Exceptions\Identifier\IdentifierInterface;
use GrahamCampbell\Exceptions\Information\FactoryInterface;
use GrahamCampbell\Exceptions\Information\InformationInterface;
use GrahamCampbell\Exceptions\Information\MergerInterface;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use Illuminate\Support\Str;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testExceptionHandlerIsInjectable(): void
    {
        $this->assertIsInjectable(ExceptionHandler::class);
    }

    public function testExceptionIdentifierIsInjectable(): void
    {
        $this->assertIsInjectable(IdentifierInterface::class);
    }

    public function testExceptionInformationMergerIsInjectable(): void
    {
        $this->assertIsInjectable(MergerInterface::class);
    }

    public function testExceptionInformationFactoryIsInjectable(): void
    {
        $this->assertIsInjectable(FactoryInterface::class);
    }

    public function testExceptionInformationIsInjectable(): void
    {
        $this->assertIsInjectable(InformationInterface::class);
    }

    public function testJsonApiDisplayerIsInjectable(): void
    {
        $this->assertIsInjectable(JsonApiDisplayer::class);
    }

    public function testJsonDisplayerIsInjectable(): void
    {
        $this->assertIsInjectable(JsonDisplayer::class);
    }

    public function testDebugDisplayerIsInjectable(): void
    {
        $this->assertIsInjectable(DebugDisplayer::class);
    }

    public function testHtmlDisplayerIsInjectable(): void
    {
        $this->assertIsInjectable(HtmlDisplayer::class);
    }

    public function testCanDisplayFilterIsInjectable(): void
    {
        $this->assertIsInjectable(CanDisplayFilter::class);
    }

    public function testContentTypeFilterIsInjectable(): void
    {
        $this->assertIsInjectable(ContentTypeFilter::class);
    }

    public function testVerboseFilterIsInjectable(): void
    {
        $this->assertIsInjectable(VerboseFilter::class);
    }

    public function testDisplayerConfig(): void
    {
        $displayers = $this->app->config->get('exceptions.displayers');

        self::assertCount(5, $displayers);

        foreach ($displayers as $displayer) {
            self::assertTrue(Str::startsWith($displayer, 'GrahamCampbell\Exceptions\Displayer'));
        }
    }

    public function testFilterConfig(): void
    {
        $filters = $this->app->config->get('exceptions.filters');

        self::assertCount(3, $filters);

        foreach ($filters as $filter) {
            self::assertTrue(Str::startsWith($filter, 'GrahamCampbell\Exceptions\Filter'));
        }
    }
}
