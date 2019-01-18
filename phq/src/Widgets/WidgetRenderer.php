<?php

namespace PHQ\Widgets;

use PHQ\Http\Renderer;
use Psr\Http\Message\ResponseInterface;

class WidgetRenderer extends Renderer
{
    /**
     * Défini la position du widget
     */
    const POSITION = 0;

    /**
     * @return mixed
     */
    protected function getData()
    {
        return null;
    }

    public function send($data): ResponseInterface
    {
        if ($data === null) {
            $data = $this->getData();
        }
        return parent::send($data);
    }
}
