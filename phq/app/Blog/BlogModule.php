<?php

namespace App\Blog;

use App\Blog\Actions\IndexAction;
use App\Blog\Actions\StoreAction;
use PHQ\Module;
use PHQ\Rendering\IRenderer;
use PHQ\Rendering\TwigRenderer;
use PHQ\Routing\Router;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Twig_Error_Loader;

class BlogModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';
    const MIGRATIONS = __DIR__ . '/Db/migrations';
    const SEEDS = __DIR__ . '/Db/seeds';

    public function __construct(ContainerInterface $container)
    {
        try {
            $this->initRouter($container->get(Router::class));
            $this->initRenderer($container->get(IRenderer::class));
        } catch (NotFoundExceptionInterface $e) {
        } catch (ContainerExceptionInterface $e) {
        } catch (Twig_Error_Loader $e) {
        }
    }

    private function initRouter(Router $router): void
    {
        $router->get('/blog', IndexAction::class, 'blog.index');
        $router->post('/blog', StoreAction::class, 'blog.store');
    }

    /**
     * @param TwigRenderer $renderer
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Twig_Error_Loader
     */
    private function initRenderer(TwigRenderer $renderer): void
    {
        $renderer->addPath('blog', __DIR__ . '/Views');
    }
}
