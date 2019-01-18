<?php

namespace PHQ\Rendering;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class PHPRenderer implements IRenderer
{
    const DEFAULT_NAMESPACE = '__MAIN';

    /**
     * @var string[] $paths
     */
    private $paths;

    /**
     * @var array $globals
     */
    private $globals;

    /**
     * Renderer constructor.
     * @param null|string $defaultPath
     */
    public function __construct(?string $defaultPath = null)
    {
        if ($defaultPath !== null) {
            $this->addPath($defaultPath);
        }
    }

    public function addPath(string $namespace, ?string $path = null): void
    {
        if ($path === null) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
    }

    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    /**
     * @param string $view
     * @param array $params
     * @return ResponseInterface
     */
    public function render(string $view, array $params = []): ResponseInterface
    {
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view).'.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE].'/'.$view.'.php';
        }

        ob_start();
        $renderer = $this;
        extract($this->globals);
        extract($params);
        require($path);
        $content = ob_get_clean();

        return new Response(200, [], $content);
    }

    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@'.$namespace, $this->paths[$namespace], $view);
    }
}
