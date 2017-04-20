<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Displayers;

use Exception;
use GrahamCampbell\Exceptions\ExceptionInfoInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the html displayer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class HtmlDisplayer implements DisplayerInterface
{
    /**
     * The exception info instance.
     *
     * @var \GrahamCampbell\Exceptions\ExceptionInfoInterface
     */
    protected $info;

    /**
     * The asset generator function.
     *
     * @var callable
     */
    protected $assets;

    /**
     * The html template path.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new html displayer instance.
     *
     * @param \GrahamCampbell\Exceptions\ExceptionInfoInterface $info
     * @param callable                                          $assets
     * @param string                                            $path
     *
     * @return void
     */
    public function __construct(ExceptionInfoInterface $info, callable $assets, $path)
    {
        $this->info = $info;
        $this->assets = $assets;
        $this->path = $path;
    }

    /**
     * Get the error response associated with the given exception.
     *
     * @param \Exception $exception
     * @param string     $id
     * @param int        $code
     * @param string[]   $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function display(Exception $exception, $id, $code, array $headers)
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
    protected function render(array $info)
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
     * @param \Exception $original
     * @param \Exception $transformed
     * @param int        $code
     *
     * @return bool
     */
    public function canDisplay(Exception $original, Exception $transformed, $code)
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
