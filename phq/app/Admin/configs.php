<?php

use App\Admin\Actions\DashboardAction;
use App\Admin\Widgets\BaseAdminWidget;
use App\Admin\Widgets\SecondAdminWidget;

return [
    'admin.widgets' => \DI\add([
        BaseAdminWidget::class,
        SecondAdminWidget::class
    ]),

    DashboardAction::class => DI\object(DashboardAction::class)
        ->constructorParameter('widgets', DI\get('admin.widgets'))
];
