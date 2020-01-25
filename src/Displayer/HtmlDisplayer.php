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

namespace GrahamCampbell\Exceptions\Displayer;

use GrahamCampbell\Exceptions\Information\InformationInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This is the html displayer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
final class HtmlDisplayer implements DisplayerInterface
{
    /**
     * The exception information instance.
     *
     * @var \GrahamCampbell\Exceptions\Information\InformationInterface
     */
    private $info;

    /**
     * The asset generator function.
     *
     * @var callable
     */
    private $assets;

    /**
     * The html template path.
     *
     * @var string
     */
    private $path;

    /**
     * Create a new html displayer instance.
     *
     * @param \GrahamCampbell\Exceptions\Information\InformationInterface $info
     * @param callable                                                    $assets
     * @param string                                                      $path
     *
     * @return void
     */
    public function __construct(InformationInterface $info, callable $assets, string $path)
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
    public function display(Throwable $exception, string $id, int $code, array $headers)
    {
        $info = $this->info->generate($exception, $id, $code);

        return new Response($this->render($info), $code, array_merge($headers, ['Content-Type' => $this->contentType()]));
    }

    /**
     * Render the page with given info.
     *
     * @param array $info
     *
     * @return string
     */
    private function render(array $info)
    {
        $content = file_get_contents($this->path);

        $generator = $this->assets;
        $info['home_url'] = $generator('/');
        $info['favicon_url'] = $generator('favicon.ico');

        foreach ($info as $key => $val) {
            $content = str_replace("{{ $$key }}", $val, $content);
        }

        return $content;
    }

    /**
     * Get the supported content type.
     *
     * @return string
     */
    public function contentType()
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
    public function canDisplay(Throwable $original, Throwable $transformed, int $code)
    {
        return true;
    }

    /**
     * Do we provide verbose information about the exception?
     *
     * @return bool
     */
    public function isVerbose()
    {
        return false;
    }
}
