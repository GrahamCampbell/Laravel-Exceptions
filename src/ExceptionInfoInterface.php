<?php

namespace GrahamCampbell\Exceptions;

use Exception;

interface ExceptionInfoInterface
{
    /**
     * Get the exception information.
     *
     * @param \Exception $exception
     * @param string     $id
     * @param int        $code
     *
     * @return array
     */
    public function generate(Exception $exception, string $id, int $code);
}
