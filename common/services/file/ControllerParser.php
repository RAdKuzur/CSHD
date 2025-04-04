<?php

namespace common\services\file;

use ReflectionClass;
use ReflectionMethod;

class ControllerParser
{
    public function getControllersInDirectory($directory, $basePath = '')
    {
        $controllers = [];

        $items = scandir($directory);
        foreach ($items as $item) {
            if ($item != '.' && $item != '..') {
                $path = $directory . DIRECTORY_SEPARATOR . $item;
                if (is_file($path)) {
                    $fileName = pathinfo($item, PATHINFO_FILENAME);
                    $controllers[] = $basePath . '\\' . $fileName;
                } elseif (is_dir($path)) {
                    $controllers = array_merge($controllers, $this->getControllersInDirectory($path, $basePath . '\\' . $item));
                }
            }
        }

        return $controllers;
    }

    public function getActionsFromController($controllerFilePath)
    {
        $actions = [];

        //require_once $controllerFilePath;

        $controllerClassName = pathinfo($controllerFilePath, PATHINFO_FILENAME);
        $reflectionClass = new ReflectionClass($controllerFilePath);

        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (substr($method->name, 0, 6) === 'action' && $method->name !== 'actions') {
                $actionName = substr($method->name, 6);
                $actions[] = strtolower($actionName[0]) . substr($actionName, 1);
            }
        }

        return $actions;
    }
}