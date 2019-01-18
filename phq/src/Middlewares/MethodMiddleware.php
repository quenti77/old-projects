<?php

namespace PHQ\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MethodMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $parseBody = $request->getParsedBody();

        if (array_key_exists('_method', $parseBody) &&
            in_array($parseBody['_method'], ['PUT', 'PATCH', 'DELETE'])) {
            $request = $request->withMethod($parseBody['_method']);
        }

        return $handler->handle($request);
    }
}
