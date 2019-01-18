<?php

namespace PHQ\Middlewares;

use GuzzleHttp\Psr7\Response;
use PHQ\Rendering\IRenderer;
use PHQ\Routing\Route;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatcherMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(Route::class);
        if ($route === null) {
            return $handler->handle($request);
        }

        $callback = $route->getCallback();

        if (!($callback instanceof MiddlewareInterface)) {
            throw new \Exception('Action n\'implémente pas une MiddlewareInterface');
        }

        return $callback->process($request, $handler);
    }
}
