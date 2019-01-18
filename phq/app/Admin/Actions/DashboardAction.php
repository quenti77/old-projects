<?php

namespace App\Admin\Actions;

use App\Admin\Renderers\DashboardRenderer;
use PHQ\Widgets\WidgetAction;
use PHQ\Widgets\WidgetsRenderer;
use Psr\Container\ContainerInterface;

/**
 * Class DashboardAction
 * @package App\Admin\Actions
 */
class DashboardAction extends WidgetAction
{

    /**
     * DashboardAction constructor.
     * @param ContainerInterface $container
     * @param DashboardRenderer $dashboardRenderer
     * @param WidgetsRenderer $widgetsRenderer
     * @param array $widgets
     */
    public function __construct(
        ContainerInterface $container,
        DashboardRenderer $dashboardRenderer,
        WidgetsRenderer $widgetsRenderer,
        array $widgets
    ) {
        parent::__construct($container, $dashboardRenderer, $widgetsRenderer, $widgets);
    }
}
