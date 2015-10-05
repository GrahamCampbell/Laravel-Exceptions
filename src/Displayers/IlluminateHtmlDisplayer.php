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
use GrahamCampbell\Exceptions\ExceptionInfo;
use Symfony\Component\Debug\ExceptionHandler as SymfonyDisplayer;
use Symfony\Component\HttpFoundation\Response;

class IlluminateHtmlDisplayer extends HtmlDisplayer implements DisplayerInterface
{
    /**
     * Create a new html displayer instance.
     *
     * @param \GrahamCampbell\Exceptions\ExceptionInfo $info
     *
     * @return void
     */
    public function __construct(ExceptionInfo $info)
    {
        $this->info = $info;
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
        if (view()->exists("errors.{$code}")) {
            return new Response(view()->make("errors.{$code}"), $code, array_merge($headers, [
                'Content-Type' => $this->contentType(),
            ]));

        } else {
            return (new SymfonyDisplayer(config('app.debug')))->createResponse($exception);
        }
    }
}
