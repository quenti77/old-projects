<?php

namespace App\Admin\Renderers;

use App\Admin\Widgets\IAdminWidget;
use InvalidArgumentException;
use PHQ\Http\Renderer;
use PHQ\Utils\NormalWidgetResponder;
use Psr\Http\Message\ResponseInterface;

class DashboardRenderer extends Renderer
{
    use NormalWidgetResponder;

    /**
     * @param $widgets
     * @return ResponseInterface
     */
    protected function jsonResponse($widgets): ResponseInterface
    {
        return $widgets;
    }

    /**
     * @param $widgets
     * @return ResponseInterface
     */
    protected function normalResponse($widgets): ResponseInterface
    {
        return $this->normalRender($widgets, $this->renderer, '@admin/dashboard');
    }
}
