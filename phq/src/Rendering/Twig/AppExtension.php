<?php

namespace PHQ\Rendering\Twig;

use PHQ\Routing\Router;
use Twig_Extension;
use Twig_Function;

class AppExtension extends Twig_Extension
{
    const BGC_TYPES = [
        'info' => 'indigo lighten-2',
        'success' => 'green lighten-2',
        'warning' => 'orange lighten-3',
        'danger' => 'red lighten-2'
    ];

    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new Twig_Function('addFlash', [$this, 'addFlashMessage']),
            new Twig_Function('flash', [$this, 'getFlashMessage']),
            new Twig_Function('typeFlash', [$this, 'getBackgroundByType']),
            new Twig_Function('csrf', [$this, 'getCsrf']),
            new Twig_Function('path', [$this, 'generateUri']),
            new Twig_Function('hasErrors', [$this, 'hasErrors']),
            new Twig_Function('errors', [$this, 'getErrors'])
        ];
    }

    /**
     * @param string $type
     * @param string $content
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function addFlashMessage(string $type, string $content): void
    {
        flash($type, $content);
    }

    /**
     * @param string|null $type
     * @return array
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function getFlashMessage(string $type = null): array
    {
        $flashes = session()->offsetGet('__flash');

        if ($type !== null) {
            $result = $flashes[$type] ?? [];

            unset($flashes[$type]);
            session()->offsetSet('__flash', $flashes);

            return $result;
        }

        $result = $flashes;
        if ($result === null) {
            $result = [];
        }

        if (!is_array($result)) {
            $result = [$result];
        }

        session()->offsetSet('__flash', []);
        return $result;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getBackgroundByType(string $type): string
    {
        return self::BGC_TYPES[$type] ?? 'gray';
    }

    /**
     * @param bool $field
     * @return string
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function getCsrf(bool $field = true): string
    {
        $csrf = session()->offsetGet('__csrf');

        if ($field) {
            return "<input type='hidden' name='__csrf' value='{$csrf}'>";
        }

        return $csrf;
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    public function generateUri(string $name, array $params = []): string
    {
        return $this->router->generateUri($name, $params);
    }

    /**
     * @return bool
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function hasErrors(): bool
    {
        return session()->offsetExists('__errors');
    }

    /**
     * @return array
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function getErrors(): array
    {
        $errors = session()->offsetGet('__errors') ?? [];
        session()->offsetUnset('__errors');

        return $errors;
    }
}
