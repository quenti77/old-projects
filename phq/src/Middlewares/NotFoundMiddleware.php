<?php

namespace PHQ\Middlewares;

use PHQ\Http\ServerRequest;
use PHQ\Rendering\IRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NotFoundMiddleware implements MiddlewareInterface
{
    /**
     * @var IRenderer $renderer
     */
    private $renderer;

    public function __construct(IRenderer $renderer)
    {
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
        if ($request instanceof ServerRequest && $request->isJson()) {
            return json([
                'message' => 'Page not found'
            ], 404);
        }

        return $this->renderer->render('404')->withStatus(404);
    }
}
