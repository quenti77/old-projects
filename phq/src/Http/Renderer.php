<?php

namespace PHQ\Http;

use PHQ\Rendering\IRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Renderer
{
    /**
     * @var ServerRequest $serverRequest
     */
    protected $serverRequest;

    /**
     * @var IRenderer $renderer
     */
    protected $renderer;

    public function __construct(ServerRequestInterface $serverRequest, IRenderer $renderer)
    {
        $this->serverRequest = $serverRequest;
        $this->renderer = $renderer;
    }

    public function send($data): ResponseInterface
    {
        if ($this->serverRequest->isJson()) {
            return $this->jsonResponse($data);
        }

        return $this->normalResponse($data);
    }

    protected function jsonResponse($data): ResponseInterface
    {
        return json($data);
    }

    protected function normalResponse($data): ResponseInterface
    {
        return response($data);
    }
}
