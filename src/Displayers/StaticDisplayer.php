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
 * This is the static displayer class.
 *
 * @author Libern Lin <libernlin@gmail.com>
 */
class StaticDisplayer implements DisplayerInterface
{
    use InfoTrait;

    /**
     * The view.
     *
     * @var string
     */
    protected $view;

    /**
     * Create a new static displayer instance.
     */
    public function __construct()
    {
        $this->view = file_get_contents(__DIR__.'/../../views/plain.html');
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

        return $this->render($info);
    }

    /**
     * Render the view with given info.
     *
     * @param array $info
     *
     * @return string
     */
    private function render($info)
    {
        $info['home_url'] = asset('/');
        foreach ($info as $key => $val) {
            $this->view = str_replace("{{ $$key }}", $val, $this->view);
        }

        return $this->view;
    }

}
