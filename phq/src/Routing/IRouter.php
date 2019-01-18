<?php

namespace PHQ\Routing;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface IRouter
 * @package PHQ\Routing
 */
interface IRouter
{
    /**
     * Ajout d'une route au routeur
     *
     * @param array $methods
     * @param string $uri
     * @param callable|string $callback
     * @param string $name
     */
    public function addRoute(array $methods, string $uri, $callback, string $name): void;

    /**
     * @param ServerRequestInterface $request
     * @return IRoute|null
     */
    public function match(ServerRequestInterface $request): ?IRoute;

    /**
     * @param string $name
     * @param array $params
     * @return null|string
     */
    public function generateUri(string $name, array $params = []): ?string;
}
