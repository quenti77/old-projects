<?php

namespace PHQ\Rendering;

use GuzzleHttp\Psr7\Response;
use PHQ\Rendering\Twig\AppExtension;
use PHQ\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;

class TwigRenderer implements IRenderer
{
    /**
     * @var TwigRenderer $twig
     */
    private $twig;

    /**
     * @var Twig_Loader_Filesystem $loader
     */
    private $loader;

    /**
     * Renderer constructor.
     * @param string $path
     * @param Router $router
     */
    public function __construct(string $path, Router $router)
    {
        $this->loader = new Twig_Loader_Filesystem($path);
        $this->twig = new Twig_Environment($this->loader, [
            'debug' => true
        ]);

        $this->twig->addExtension(new Twig_Extension_Debug());
        $this->twig->addExtension(new AppExtension($router));
    }

    /**
     * @param string $namespace
     * @param null|string $path
     * @throws \Twig_Error_Loader
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * @param string $key
     * @param $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    /**
     * @param string $view
     * @param array $params
     * @return ResponseInterface
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(string $view, array $params = []): ResponseInterface
    {
        return new Response(200, [], $this->twig->render($view.'.twig', $params));
    }
}
