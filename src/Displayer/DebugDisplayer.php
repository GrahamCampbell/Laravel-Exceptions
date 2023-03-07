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

namespace GrahamCampbell\Exceptions\Displayer;

use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as Whoops;
use Whoops\RunInterface;

/**
 * This is the debug displayer class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class DebugDisplayer implements DisplayerInterface
{
    /**
     * Get the error response associated with the given exception.
     *
     * @param \Throwable $exception
     * @param string     $id
     * @param int        $code
     * @param string[]   $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function display(Throwable $exception, string $id, int $code, array $headers): Response
    {
        $content = self::makeWhoops()->handleException($exception);

        return new Response(
            $content,
            $code,
            array_merge($headers, ['Content-Type' => 'text/html']),
        );
    }

    /**
     * Get the whoops instance.
     *
     * @return \Whoops\RunInterface
     */
    private static function makeWhoops(): RunInterface
    {
        $whoops = new Whoops();
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $whoops->pushHandler(new PrettyPageHandler());

        return $whoops;
    }

    /**
     * Get the supported content type.
     *
     * @return string
     */
    public function contentType(): string
    {
        return 'text/html';
    }

    /**
     * Can we display the exception?
     *
     * @param \Throwable $original
     * @param \Throwable $transformed
     * @param int        $code
     *
     * @return bool
     */
    public function canDisplay(Throwable $original, Throwable $transformed, int $code): bool
    {
        return class_exists(Whoops::class);
    }

    /**
     * Do we provide verbose information about the exception?
     *
     * @return bool
     */
    public function isVerbose(): bool
    {
        return true;
    }
}
