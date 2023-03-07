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

use GrahamCampbell\Analyzer\AnalysisTrait;
use Illuminate\Contracts\Support\Responsable;
use Laravel\Lumen\Application;
use Laravel\Lumen\Routing\UrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

/**
 * This is the analysis test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class AnalysisTest extends TestCase
{
    use AnalysisTrait;

    /**
     * Get the code paths to analyze.
     *
     * @return string[]
     */
    protected static function getPaths(): array
    {
        return [
            realpath(__DIR__.'/../config'),
            realpath(__DIR__.'/../src'),
            realpath(__DIR__),
        ];
    }

    /**
     * Get the classes to ignore not existing.
     *
     * @return string[]
     */
    protected static function getIgnored(): array
    {
        return [
            Application::class,
            Responsable::class,
            SuspiciousOperationException::class,
            UrlGenerator::class,
        ];
    }
}
