<?php

namespace App\Admin\Widgets;

use PHQ\Widgets\WidgetRenderer;
use Psr\Http\Message\ResponseInterface;

class BaseAdminWidget extends WidgetRenderer
{
    protected function getData()
    {
        return [
            'base' => true,
            'pi' => 3.14
        ];
    }


    protected function normalResponse($data): ResponseInterface
    {
        return $this->renderer->render('@admin/widgets/base', compact('data'));
    }
}
