<?php

namespace App\Admin\Widgets;

use PHQ\Widgets\WidgetRenderer;
use Psr\Http\Message\ResponseInterface;

class SecondAdminWidget extends WidgetRenderer
{
    protected function getData()
    {
        return [
            'base' => false,
            'message' => 'Bienvenue'
        ];
    }

    protected function normalResponse($data): ResponseInterface
    {
        return $this->renderer->render('@admin/widgets/second', compact('data'));
    }
}
