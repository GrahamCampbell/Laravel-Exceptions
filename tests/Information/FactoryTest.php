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

use GrahamCampbell\Exceptions\Information\ArrayInformation;
use GrahamCampbell\Exceptions\Information\InformationFactory;
use GrahamCampbell\Exceptions\Information\InformationMerger;
use GrahamCampbell\Exceptions\Information\NullInformation;
use GrahamCampbell\TestBench\AbstractTestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the information factory test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class FactoryTest extends AbstractTestCase
{
    public function testFallback()
    {
        $info = (new NullInformation(new InformationMerger()))->generate(new BadRequestHttpException(), 'foo', 400);

        $expected = ['id' => 'foo', 'code' => 500, 'name' => 'Internal Server Error', 'detail' => 'An error has occurred and this resource cannot be displayed.'];

        $this->assertSame($expected, $info);
    }

    public function testErrorsJson()
    {
        $path = __DIR__.'/../../resources/lang/en/errors.json';

        $this->assertFileExists($path);

        $decoded = json_decode(file_get_contents($path), true);

        $this->assertIsArray($decoded);
        $this->assertCount(40, $decoded);
        $this->assertSame('I\'m a teapot', $decoded[418]['name']);
        $this->assertSame('The resource that is being accessed is locked.', $decoded[423]['message']);
    }

    public function testFactoryNoPath()
    {
        $i = (new InformationFactory(new InformationMerger()))->create();

        $this->assertInstanceOf(NullInformation::class, $i);
    }

    public function testFactoryWithPath()
    {
        $i = (new InformationFactory(new InformationMerger()))->create(__DIR__.'/../../resources/lang/en/errors.json');

        $this->assertInstanceOf(ArrayInformation::class, $i);
    }

    public function testFactoryBadPath()
    {
        $i = (new InformationFactory(new InformationMerger()))->create(__DIR__.'/../../resources/lang/en/errors.jso');

        $this->assertInstanceOf(NullInformation::class, $i);
    }

    public function testFactoryBadContent()
    {
        $i = (new InformationFactory(new InformationMerger()))->create(__DIR__.'/stubs/garbage.json');

        $this->assertInstanceOf(NullInformation::class, $i);
    }
}
