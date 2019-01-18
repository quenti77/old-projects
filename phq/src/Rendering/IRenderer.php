<?php

namespace PHQ\Rendering;

use Psr\Http\Message\ResponseInterface;

interface IRenderer
{
    /**
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * @param string $key
     * @param $value
     */
    public function addGlobal(string $key, $value): void;

    /**
     * @param string $view
     * @param array $params
     * @return ResponseInterface
     */
    public function render(string $view, array $params = []): ResponseInterface;
}
