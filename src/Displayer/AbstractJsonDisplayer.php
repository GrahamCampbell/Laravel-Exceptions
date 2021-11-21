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
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

/**
 * This is the abstract json displayer class.
 *
 * @internal
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
abstract class AbstractJsonDisplayer implements DisplayerInterface
{
    /**
     * The exception information instance.
     *
     * @var \GrahamCampbell\Exceptions\Information\InformationInterface
     */
    private $info;

    /**
     * Create a new json displayer instance.
     *
     * @param \GrahamCampbell\Exceptions\Information\InformationInterface $info
     *
     * @return void
     */
    public function __construct(InformationInterface $info)
    {
        $this->info = $info;
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

        $content = json_encode([
            'errors' => [
                ['id' => $id, 'status' => $info['code'], 'title' => $info['name'], 'detail' => $info['detail']],
            ],
        ]);

        return JsonResponse::fromJsonString($content, $code, array_merge($headers, ['Content-Type' => $this->contentType()]));
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
