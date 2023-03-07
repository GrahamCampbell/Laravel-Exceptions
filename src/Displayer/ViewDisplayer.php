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

use GrahamCampbell\Exceptions\Information\InformationInterface;
use Illuminate\Contracts\View\Factory;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This is the view displayer class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class ViewDisplayer implements DisplayerInterface
{
    /**
     * The exception information instance.
     *
     * @var \GrahamCampbell\Exceptions\Information\InformationInterface
     */
    private InformationInterface $info;

    /**
     * The view factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    private Factory $factory;

    /**
     * Create a new view displayer instance.
     *
     * @param \GrahamCampbell\Exceptions\Information\InformationInterface $info
     * @param \Illuminate\Contracts\View\Factory                          $factory
     *
     * @return void
     */
    public function __construct(InformationInterface $info, Factory $factory)
    {
        $this->info = $info;
        $this->factory = $factory;
    }

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
        $info = $this->info->generate($exception, $id, $code);

        return new Response(
            $this->render($exception, $info, $code),
            $code,
            array_merge($headers, ['Content-Type' => $this->contentType()]),
        );
    }

    /**
     * Render the page with given info and exception.
     *
     * @param \Throwable $exception
     * @param array      $info
     * @param int        $code
     *
     * @return string
     */
    private function render(Throwable $exception, array $info, int $code): string
    {
        $view = $this->factory->make("errors.{$code}", $info);

        return $view->with('exception', $exception)->render();
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
        return $this->factory->exists("errors.{$code}");
    }

    /**
     * Do we provide verbose information about the exception?
     *
     * @return bool
     */
    public function isVerbose(): bool
    {
        return false;
    }
}
