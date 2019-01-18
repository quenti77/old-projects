<?php

namespace App\Admin;

use App\Admin\Actions\DashboardAction;
use PHQ\Module;
use PHQ\Rendering\IRenderer;
use PHQ\Routing\Router;
use Psr\Container\ContainerInterface;

class AdminModule extends Module
{
    const DEFINITIONS = __DIR__.'/configs.php';

    /**
     * AdminModule constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->initRouter($container->get(Router::class));
        $this->initRenderer($container->get(IRenderer::class));
    }

    private function initRouter(Router $router): void
    {
        $router->get('/admin', DashboardAction::class, 'admin.index');
    }

    /**
     * @param IRenderer $renderer
     */
    private function initRenderer(IRenderer $renderer): void
    {
        $renderer->addPath('admin', __DIR__.'/Views');
    }
}
