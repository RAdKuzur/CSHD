<?php

namespace common\services\monitoring;

use common\helpers\StringFormatter;
use common\repositories\rubac\PermissionFunctionRepository;
use common\services\file\ControllerParser;
use frontend\models\work\rubac\PermissionFunctionWork;
use Yii;
use yii\helpers\ArrayHelper;

class PermissionLinksMonitor
{
    private ControllerParser $controllerParser;

    public function __construct(ControllerParser $controllerParser)
    {
        $this->controllerParser = $controllerParser;
    }

    /**
     * Проверяет синхронизированность config-файла и БД по части правил @see PermissionFunctionWork
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function checkPermissionsExistence()
    {
        $repository = Yii::createObject(PermissionFunctionRepository::class);
        $dbPermissions = ArrayHelper::getColumn($repository->getAllPermissions(), 'short_code');
        $configPermissions = Yii::$app->rulesConfig->getPermissionsName();

        $dbMissingPermissions = []; // те правила, что есть в конфиг файле, но нет в БД
        $configMissingPermissions = []; // те правила, что есть в БД, но нет в конфиг файле

        foreach ($dbPermissions as $dbPermission) {
            if (!in_array($dbPermission, $configPermissions)) {
                $configMissingPermissions[] = $dbPermission;
            }
        }

        foreach ($configPermissions as $configPermission) {
            if (!in_array($configPermission, $dbPermissions)) {
                $dbMissingPermissions[] = $configPermission;
            }
        }

        return [
            'db_permissions' => $dbPermissions,
            'config_permissions' => $configPermissions,
            'db_missing' => $dbMissingPermissions,
            'config_missing' => $configMissingPermissions
        ];
    }

    /**
     * Возвращает список контроллеров, существующих в системе, но не существующих в правилах config-файла;
     * Возвращает список экшнов, существующих в системе, но не существующих в правилах config-файла;
     * @return array[]
     */
    public function checkUnlinkedActions()
    {
        $backendControllersPath = Yii::getAlias('@backend/controllers');
        $frontendControllersPath = Yii::getAlias('@frontend/controllers');

        // получаем списки контроллеров из бэкенда и фронтенда
        $backendControllers = $this->controllerParser->getControllersInDirectory($backendControllersPath, 'backend\controllers');
        $frontendControllers = $this->controllerParser->getControllersInDirectory($frontendControllersPath, 'frontend\controllers');

        // получаем все бэкенд-экшны
        $backendActions = [];
        foreach ($backendControllers as $controller) {
            $backendActions[$controller] = $this->controllerParser->getActionsFromController($controller);
        }

        // получаем все фронтенд-экшны
        $frontendActions = [];
        foreach ($frontendControllers as $controller) {
            $frontendActions[$controller] = $this->controllerParser->getActionsFromController($controller);
        }

        // объединяем контроллеры в общий список и получаем список контроллеров из config-файла
        $controllersProject = array_merge($frontendActions, $backendActions);
        $controllersConfig = Yii::$app->rulesConfig->getAllControllers();

        // основная бизнес-логика проверки
        $missingControllers = [];
        $missingActions = [];
        foreach ($controllersProject as $name => $actions) {
            $actions = ArrayHelper::map(
                $actions,
                null,
                function ($value) {
                    return StringFormatter::formatStyle($value, StringFormatter::STYLE_CAMEL, StringFormatter::STYLE_KEBAB);
                });
            // проверяем, упоминается ли данный контроллер хотя бы в одном правиле
            if (!in_array($name, $controllersConfig)) {
                $missingControllers[] = $name;
            }
            // если упоминается, то проверяем все ли экшны контроллера встречаются хотя бы в одном правиле
            else {
                foreach ($actions as $action) {
                    if (!in_array($action, Yii::$app->rulesConfig->getAllActionsByController($name))) {
                        $missingActions[] = [$name, $action];
                    }
                }
            }
        }

        return [$missingControllers, $missingActions];
    }
}