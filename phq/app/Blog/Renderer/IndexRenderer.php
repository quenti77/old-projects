<?php

namespace App\Blog\Renderer;

use PHQ\Http\Renderer;
use Psr\Http\Message\ResponseInterface;

class IndexRenderer extends Renderer
{
    /**
     * @param $data
     * @return ResponseInterface
     */
    protected function normalResponse($data): ResponseInterface
    {
        return $this->renderer->render('@blog/index', ['posts' => $data]);
    }
}
