<?php

namespace PHQ\Utils;

use Psr\Container\ContainerInterface;

trait StringToClassTrait
{
    /**
     * @param ContainerInterface $container
     * @param string[] $classNames
     * @return array
     */
    protected function stringToClass(ContainerInterface $container, array $classNames)
    {
        return array_map(function (string $className) use ($container) {
            return $container->get($className);
        }, $classNames);
    }
}
