<?php

namespace PHQ\Middlewares;

use PHQ\Routing\Route;
use PHQ\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouterMiddleware implements MiddlewareInterface
{
    /**
     * @var Router $router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->router->match($request);
        if ($route === null) {
            return $handler->handle($request);
        }

        $request = $this->addParamsInRequest($request, $route->getParams());
        $request = $request->withAttribute(Route::class, $route);

        return $handler->handle($request);
    }

    private function addParamsInRequest(ServerRequestInterface $request, $params): ServerRequestInterface
    {
        return array_reduce(array_keys($params), function (ServerRequestInterface $request, string $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
    }
}
