<?php

namespace PHQ\Widgets;

use PHQ\Http\JsonResponse;
use PHQ\Http\Renderer;
use Psr\Http\Message\ResponseInterface;

class WidgetsRenderer extends Renderer
{
    /**
     * @param $widgets
     * @return ResponseInterface
     */
    protected function jsonResponse($widgets): ResponseInterface
    {
        $result = array_reduce($widgets, function ($render, WidgetRenderer $widget) {
            $val = $widget->send(null);

            if ($val instanceof JsonResponse && empty($val = $val->getDataJson())) {
                return $render;
            }

            if (!is_array($val)) {
                $val = [$val];
            }

            if (empty($render)) {
                return $val;
            }

            return array_merge_recursive($render, $val);
        }, '');

        return json($result);
    }

    /**
     * @param $widgets
     * @return ResponseInterface
     */
    protected function normalResponse($widgets): ResponseInterface
    {
        $result = array_reduce($widgets, function (string $render, WidgetRenderer $widget) {
            return $render . (string) $widget->send(null)->getBody();
        }, '');

        return response($result);
    }
}
