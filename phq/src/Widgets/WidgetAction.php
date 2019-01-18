<?php

namespace PHQ\Widgets;

use PHQ\Http\Renderer;
use PHQ\Utils\StringToClassTrait;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class WidgetAction implements MiddlewareInterface
{
    use StringToClassTrait;

    /**
     * @var WidgetRenderer[] $widgets
     */
    protected $widgets;

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @var WidgetsRenderer $widgetsRenderer
     */
    protected $widgetsRenderer;
    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * WidgetAction constructor.
     * @param ContainerInterface $container
     * @param Renderer $renderer
     * @param WidgetsRenderer $widgetsRenderer
     * @param array $widgets
     */
    public function __construct(
        ContainerInterface $container,
        Renderer $renderer,
        WidgetsRenderer $widgetsRenderer,
        array $widgets
    ) {
        $this->container = $container;
        $this->widgets = $widgets;
        $this->widgetsRenderer = $widgetsRenderer;
        $this->renderer = $renderer;
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
        $widgetsClass = $this->stringToClass($this->container, $this->widgets);

        usort($widgetsClass, function (WidgetRenderer $a, WidgetRenderer $b) {
            return $a::POSITION <=> $b::POSITION;
        });

        $widgets = $this->widgetsRenderer->send($widgetsClass);
        return $this->renderer->send($widgets);
    }
}
