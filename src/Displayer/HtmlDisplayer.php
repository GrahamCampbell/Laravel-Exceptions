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

use Closure;
use GrahamCampbell\Exceptions\Information\InformationInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This is the html displayer class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class HtmlDisplayer implements DisplayerInterface
{
    /**
     * The exception information instance.
     *
     * @var \GrahamCampbell\Exceptions\Information\InformationInterface
     */
    private InformationInterface $info;

    /**
     * The asset generator function.
     *
     * @var Closure(string): string
     */
    private Closure $assets;

    /**
     * The html template path.
     *
     * @var string
     */
    private string $path;

    /**
     * Create a new html displayer instance.
     *
     * @param \GrahamCampbell\Exceptions\Information\InformationInterface $info
     * @param Closure(string): string                                     $assets
     * @param string                                                      $path
     *
     * @return void
     */
    public function __construct(InformationInterface $info, Closure $assets, string $path)
    {
        $this->info = $info;
        $this->assets = $assets;
        $this->path = $path;
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
            $this->render($info),
            $code,
            array_merge($headers, ['Content-Type' => $this->contentType()]),
        );
    }

    /**
     * Render the page with given info.
     *
     * @param array $info
     *
     * @return string
     */
    private function render(array $info): string
    {
        $content = file_get_contents($this->path);

        $info['home_url'] = ($this->assets)('/');
        $info['favicon_url'] = ($this->assets)('favicon.ico');

        foreach ($info as $key => $val) {
            $content = str_replace("{{ $$key }}", (string) $val, (string) $content);
        }

        return $content;
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
        return true;
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
