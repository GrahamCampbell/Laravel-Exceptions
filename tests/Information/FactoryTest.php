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
    public function testFallback(): void
    {
        $info = (new NullInformation(new InformationMerger()))->generate(new BadRequestHttpException(), 'foo', 400);

        $expected = ['id' => 'foo', 'code' => 500, 'name' => 'Internal Server Error', 'detail' => 'An error has occurred and this resource cannot be displayed.'];

        self::assertSame($expected, $info);
    }

    public function testErrorsJson(): void
    {
        $path = __DIR__.'/../../resources/lang/en/errors.json';

        self::assertFileExists($path);

        $decoded = json_decode(file_get_contents($path), true);

        self::assertIsArray($decoded);
        self::assertCount(40, $decoded);
        self::assertSame('I\'m a teapot', $decoded[418]['name']);
        self::assertSame('The resource that is being accessed is locked.', $decoded[423]['message']);
    }

    public function testFactoryNoPath(): void
    {
        $i = (new InformationFactory(new InformationMerger()))->create();

        self::assertInstanceOf(NullInformation::class, $i);
    }

    public function testFactoryWithPath(): void
    {
        $i = (new InformationFactory(new InformationMerger()))->create(__DIR__.'/../../resources/lang/en/errors.json');

        self::assertInstanceOf(ArrayInformation::class, $i);
    }

    public function testFactoryBadPath(): void
    {
        $i = (new InformationFactory(new InformationMerger()))->create(__DIR__.'/../../resources/lang/en/errors.jso');

        self::assertInstanceOf(NullInformation::class, $i);
    }

    public function testFactoryBadContent(): void
    {
        $i = (new InformationFactory(new InformationMerger()))->create(__DIR__.'/stubs/garbage.json');

        self::assertInstanceOf(NullInformation::class, $i);
    }
}
