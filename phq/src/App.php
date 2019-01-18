<?php

namespace PHQ;

use DI\Container;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Middlewares\Utils\CallableHandler;
use PHQ\Exceptions\CsrfTokenException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class App
 * @package PHQ
 */
class App implements MiddlewareInterface
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @var string[] $modules
     */
    private $modules = [];

    /**
     * @var array
     */
    private $migrations = [];

    /**
     * @var array
     */
    private $seeds = [];

    /**
     * @var string $configFolder
     */
    private $configFolder;

    /**
     * @var string[] $middlewares
     */
    private $middlewares = [];

    /**
     * @var int $index
     */
    private $index = 0;

    /**
     * App constructor.
     * @param string $configFolder
     * @throws Exception
     */
    public function __construct(string $configFolder)
    {
        $this->configFolder = $configFolder;
        $this->initApp();
    }

    /**
     * @return array
     */
    public function getMigrations(): array
    {
        return $this->migrations;
    }

    /**
     * @return array
     */
    public function getSeeds(): array
    {
        return $this->seeds;
    }

    /**
     * @param string $module
     * @return self
     * @throws Exception
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;

        if ($module::MIGRATIONS) {
            $this->migrations[] = $module::MIGRATIONS;
        }

        if ($module::SEEDS) {
            $this->seeds[] = $module::SEEDS;
        }

        return $this;
    }

    /**
     * @param string $middleware
     * @return self
     */
    public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface|null $handler
     * @return ResponseInterface
     * @throws DependencyException
     * @throws Exception
     * @throws NotFoundException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler = null): ResponseInterface
    {
        $middleware = $this->getMiddleware();

        if ($middleware instanceof MiddlewareInterface) {
            try {
                return $middleware->process($request, new CallableHandler([$this, 'process']));
            } catch (CsrfTokenException $csrfTokenException) {
                flash('danger', 'Jeton CSRF invalide');

                $url = $request->getServerParams()['HTTP_REFERER'] ?? '/';
                return redirect($url);
            }
        }

        throw new Exception('Aucun middleware n\'a intercepté cette requête');
    }

    /**
     * @return ResponseInterface
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function run(): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }

        $request = $this->getContainer()->get(ServerRequestInterface::class);
        return $this->process($request);
    }

    /**
     * Génère un container s'il n'existe pas et le retourne
     * ou le retourne directement si ce dernier est déjà créé
     *
     * @return Container
     */
    public function getContainer()
    {
        if ($this->container === null) {
            $containerBuilder = new ContainerBuilder();
            $this->initContainerBuilderWithFiles($containerBuilder);
            $this->initContainerBuilderWithModules($containerBuilder);

            // Add config if exists in root directory
            $configLast = ROOT.'/config.php';
            if (file_exists($configLast)) {
                $containerBuilder->addDefinitions($configLast);
            }

            $this->container = $containerBuilder->build();
        }

        return $this->container;
    }

    /**
     * Configuration de l'app via le fichier de config bin/app.php
     * @throws Exception
     */
    private function initApp()
    {
        $appConfig = require ROOT.'/bin/app.php';

        if (!empty($appConfig['modules'])) {
            foreach ($appConfig['modules'] as $module) {
                $this->addModule($module);
            }
        }

        if (!empty($appConfig['middlewares'])) {
            foreach ($appConfig['middlewares'] as $middleware) {
                $this->pipe($middleware);
            }
        }
    }

    /**
     * Ajoute la configuration des fichiers dans le DIC
     *
     * @param ContainerBuilder $containerBuilder
     */
    private function initContainerBuilderWithFiles(ContainerBuilder $containerBuilder)
    {
        $rdi = new RecursiveDirectoryIterator($this->configFolder, RecursiveDirectoryIterator::SKIP_DOTS);
        $rit = new RecursiveIteratorIterator($rdi);

        foreach ($rit as $file) {
            if ($file->getExtension() === 'php') {
                $containerBuilder->addDefinitions($file->getRealPath());
            }
        }
    }

    /**
     * Ajoute la configuration des modules dans le DIC
     *
     * @param ContainerBuilder $containerBuilder
     */
    private function initContainerBuilderWithModules(ContainerBuilder $containerBuilder)
    {
        foreach ($this->modules as $module) {
            if ($module::DEFINITIONS) {
                $containerBuilder->addDefinitions($module::DEFINITIONS);
            }
        }
    }

    /**
     * @return callable|null|object
     * @throws DependencyException
     * @throws NotFoundException
     */
    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->getContainer()
                ->get($this->middlewares[$this->index]);

            $this->index += 1;
            return $middleware;
        }

        return null;
    }
}
