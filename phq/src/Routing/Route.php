<?php

namespace PHQ\Routing;

class Route implements IRoute
{

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var callable|string $callback
     */
    private $callback;

    /**
     * @var string[] $params
     */
    private $params;

    /**
     * Route constructor.
     * @param string $name
     * @param $callback
     * @param array $params
     */
    public function __construct(string $name, $callback, array $params)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return callable|string
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
