<?php

namespace console\controllers;

use common\services\monitoring\PermissionLinksMonitor;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class MonitoringController extends Controller
{
    private PermissionLinksMonitor $permissionLinksMonitor;

    public function __construct(
        $id,
        $module,
        PermissionLinksMonitor $permissionLinksMonitor,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->permissionLinksMonitor = $permissionLinksMonitor;
    }

    public function actionPermissions()
    {
        $existPermissions = $this->permissionLinksMonitor->checkPermissionsExistence();

        $this->stdout("\r\nЗдесь предоставлена информация о правилах в БД и config-файлах\r\n", Console::FG_GREEN);
        $this->stdout(str_repeat('-', 62) . "\r\n", Console::FG_GREY);
        $this->stdout('Правил в базе данных: ' . count($existPermissions['db_permissions']) . "\r\n", Console::FG_BLUE);
        $this->stdout('Правил в config-файле: ' . count($existPermissions['config_permissions']) . "\r\n\r\n", Console::FG_PURPLE);
        $this->stdout(str_repeat('-', 62) . "\r\n", Console::FG_GREY);

        if (count($existPermissions['db_missing']) > 0) {
            $this->stdout('В базе данных не найдены следующие правила из config-файла' . "\r\n", Console::FG_BLUE);
            foreach ($existPermissions['db_missing'] as $permission) {
                $this->stdout($permission . "\r\n", Console::FG_RED);
            }
        }

        if (count($existPermissions['config_missing']) > 0) {
            $this->stdout("\r\n");
            $this->stdout('В config-файле не найдены следующие правила из базы данных' . "\r\n", Console::FG_PURPLE);
            foreach ($existPermissions['config_missing'] as $permission) {
                $this->stdout($permission . "\r\n", Console::FG_RED);
            }
        }

        $this->stdout(str_repeat('-', 62) . "\r\n", Console::FG_GREY);

        if (count($existPermissions['db_missing']) == 0 && count($existPermissions['config_missing']) == 0) {
            $this->stdout('Проблем синхронизации правил не обнаружено' . "\r\n", Console::FG_GREEN);
        }
        else {
            $this->stdout('Правила в БД и config-файле не синхронизированы' . "\r\n\r\n", Console::FG_RED);
        }

        $this->stdout(str_repeat('-', 62) . "\r\n", Console::FG_GREY);
        $missControllerAction = $this->permissionLinksMonitor->checkUnlinkedActions();
        if (count($missControllerAction[1]) > 0 || count($missControllerAction[0]) > 0) {
            $this->stdout('Обнаружены следующие проблемы' . "\r\n\r\n", Console::FG_RED);
            if (count($missControllerAction[0]) > 0) {
                foreach ($missControllerAction[0] as $missController) {
                    $this->stdout('В правилах config-файла не найден контроллер: ' .$missController . "\r\n", Console::FG_YELLOW);
                }
            }
            $this->stdout("\r\n");
            if (count($missControllerAction[1]) > 0) {
                foreach ($missControllerAction[1] as $missAction) {
                    $this->stdout('В правилах config-файла не найден экшн: ' . $missAction[0] . ' ' . $missAction[1] . "\r\n", Console::FG_YELLOW);
                }
            }
        }
        else {
            $this->stdout('Все контроллеры и экшны присутствуют в правилах config-файла' . "\r\n", Console::FG_GREEN);
        }
        $this->stdout(str_repeat('-', 62) . "\r\n", Console::FG_GREY);
    }
}