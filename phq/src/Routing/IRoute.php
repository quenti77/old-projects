<?php

namespace PHQ\Routing;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Interface IRoute
 * @package PHQ\Routing
 */
interface IRoute
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return MiddlewareInterface|string
     */
    public function getCallback();

    /**
     * @return string[]
     */
    public function getParams(): array;
}
