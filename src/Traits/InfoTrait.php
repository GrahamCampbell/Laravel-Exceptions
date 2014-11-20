<?php

/**
 * This file is part of Laravel Exceptions by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://bit.ly/UWsjkb.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Exceptions\Traits;

/**
 * This is the exception info trait.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Exceptions/blob/master/LICENSE.md> Apache 2.0
 */
trait InfoTrait
{
    /**
     * Get the exception information.
     *
     * @param int    $code
     * @param string $msg
     *
     * @return array
     */
    protected function info($code, $msg)
    {
        switch ($code) {
            case 400:
                $name = 'Bad Request';
                $message = 'The request cannot be fulfilled due to bad syntax.';
                break;
            case 401:
                $name = 'Unauthorized';
                $message = 'Authentication is required and has failed or has not yet been provided.';
                break;
            case 403:
                $name = 'Forbidden';
                $message = 'The request was a valid request, but the server is refusing to respond to it.';
                break;
            case 404:
                $name = 'Not Found';
                $message = 'The requested resource could not be found but may be available again in the future.';
                break;
            case 405:
                $name = 'Method Not Allowed';
                $message = 'A request was made of a resource using a request method not supported by that resource.';
                break;
            case 406:
                $name = 'Not Acceptable';
                $message = 'The requested resource is only capable of generating content not acceptable.';
                break;
            case 409:
                $name = 'Conflict';
                $message = 'The request could not be processed because of conflict in the request.';
                break;
            case 410:
                $name = 'Gone';
                $message = 'The requested resource is no longer available and will not be available again.';
                break;
            case 411:
                $name = 'Length Required';
                $message = 'The request did not specify the length of its content, which is required by the requested resource.';
                break;
            case 412:
                $name = 'Precondition Failed';
                $message = 'The server does not meet one of the preconditions that the requester put on the request.';
                break;
            case 415:
                $name = 'Unsupported Media Type';
                $message = 'The request entity has a media type which the server or resource does not support.';
                break;
            case 422:
                $name = 'Unprocessable Entity';
                $message = 'The request was well-formed but was unable to be followed due to semantic errors.';
                break;
            case 428:
                $name = 'Precondition Required';
                $message = 'The origin server requires the request to be conditional.';
                break;
            case 429:
                $name = 'Too Many Requests';
                $message = 'The user has sent too many requests in a given amount of time.';
                break;
            case 500:
                $name = 'Internal Server Error';
                $message = 'An error has occurred and this resource cannot be displayed.';
                break;
            case 501:
                $name = 'Not Implemented';
                $message = 'The server either does not recognize the request method, or it lacks the ability to fulfil the request.';
                break;
            case 502:
                $name = 'Bad Gateway';
                $message = 'The server was acting as a gateway or proxy and received an invalid response from the upstream server.';
                break;
            case 503:
                $name = 'Service Unavailable';
                $message = 'The server is currently unavailable. It may be overloaded or down for maintenance.';
                break;
            case 504:
                $name = 'Gateway Timeout';
                $message = 'The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.';
                break;
            case 505:
                $name = 'HTTP Version Not Supported';
                $message = 'The server does not support the HTTP protocol version used in the request.';
                break;
            default:
                $code = 500;
                $name = 'Internal Server Error';
                $message = 'An error has occurred and this resource cannot be displayed.';
        }

        $extra = (!$msg || strlen($msg) > 35 || strlen($msg) < 5) ? 'Houston, We Have A Problem' : $msg;

        return compact('code', 'name', 'message', 'extra');
    }
}
