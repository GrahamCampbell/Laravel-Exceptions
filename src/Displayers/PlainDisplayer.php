<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Displayers;

use Exception;
use GrahamCampbell\Exceptions\Traits\InfoTrait;
use Illuminate\Contracts\View\Factory as View;

/**
 * This is the plain displayer class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class PlainDisplayer implements DisplayerInterface
{
    use InfoTrait;

    /**
     * The view factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * Create a new plain displayer instance.
     *
     * @param \Illuminate\Contracts\View\Factory $view
     *
     * @return void
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * Get the content associated with the given exception.
     *
     * @param \Exception $exception
     * @param int        $code
     *
     * @return string
     */
    public function display(Exception $exception, $code)
    {
        $info = $this->info($code, $exception->getMessage());

        return $this->view->make('exceptions::plain', $info)->render();
    }
}
