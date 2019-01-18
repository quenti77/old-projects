<?php

namespace PHQ\Utils;

use PHQ\Rendering\IRenderer;
use Psr\Http\Message\ResponseInterface;

trait NormalWidgetResponder
{
    /**
     * @param ResponseInterface $widgets
     * @param IRenderer $renderer
     * @param string $path
     * @return ResponseInterface
     */
    protected function normalRender(ResponseInterface $widgets, IRenderer $renderer, string $path): ResponseInterface
    {
        return $renderer->render($path, [
            'widgets' => (string) $widgets->getBody()
        ]);
    }
}
