<?php

namespace PHQ\Routing;

use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Class Router
 * @package PHQ\Routing
 */
class Router implements IRouter
{
    /**
     * @var FastRouteRouter
     */
    private $router;

    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    /**
     * @param string $uri
     * @param MiddlewareInterface|string $callback
     * @param string $name
     * @throws Exception
     */
    public function get(string $uri, $callback, string $name): void
    {
        $this->addRoute(['GET'], $uri, $callback, $name);
    }

    /**
     * @param string $uri
     * @param MiddlewareInterface|string $callback
     * @param string $name
     * @throws Exception
     */
    public function post(string $uri, $callback, string $name): void
    {
        $this->addRoute(['POST'], $uri, $callback, $name);
    }

    /**
     * @param string $uri
     * @param MiddlewareInterface|string $callback
     * @param string $name
     * @throws Exception
     */
    public function put(string $uri, $callback, string $name): void
    {
        $this->addRoute(['PUT'], $uri, $callback, $name);
    }

    /**
     * @param string $uri
     * @param MiddlewareInterface|string $callback
     * @param string $name
     * @throws Exception
     */
    public function patch(string $uri, $callback, string $name): void
    {
        $this->addRoute(['PATCH'], $uri, $callback, $name);
    }

    /**
     * @param string $uri
     * @param MiddlewareInterface|string $callback
     * @param string $name
     * @throws Exception
     */
    public function delete(string $uri, $callback, string $name): void
    {
        $this->addRoute(['DELETE'], $uri, $callback, $name);
    }

    /**
     * @param string $uri
     * @param $callback
     * @param string $name
     * @throws Exception
     */
    public function any(string $uri, $callback, string $name): void
    {
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        $this->addRoute($methods, $uri, $callback, $name);
    }

    /**
     * Ajout d'une route au routeur
     *
     * @param array $methods
     * @param string $uri
     * @param MiddlewareInterface|string $callback
     * @param string $name
     * @throws Exception
     */
    public function addRoute(array $methods, string $uri, $callback, string $name): void
    {
        if (is_string($callback)) {
            $callback = app()->getContainer()->get($callback);
        }
        $this->router->addRoute(new ZendRoute($uri, $callback, $methods, $name));
    }

    /**
     * @param ServerRequestInterface $request
     * @return IRoute|null
     */
    public function match(ServerRequestInterface $request): ?IRoute
    {
        $routeResult = $this->router->match($request);
        if ($routeResult->isSuccess()) {
            $matchRoute = $routeResult->getMatchedRoute();
            return new Route(
                $matchRoute->getName(),
                $matchRoute->getMiddleware(),
                $routeResult->getMatchedParams()
            );
        }

        return null;
    }

    /**
     * @param string $name
     * @param array $params
     * @return null|string
     */
    public function generateUri(string $name, array $params = []): ?string
    {
        try {
            $name = $this->router->generateUri($name, $params);
            return $name;
        } catch (Exception $e) {
            return null;
        }
    }
}
