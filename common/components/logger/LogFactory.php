<?php

namespace common\components\logger;

use common\components\access\LogRecordComponent;
use common\components\logger\base\BaseLog;
use common\components\logger\base\LogInterface;
use common\components\logger\crud\CrudLog;
use common\components\logger\method\MethodLog;
use common\components\logger\search\SearchLog;
use Yii;

class LogFactory
{
    public static function createBaseLog(
        int $level,
        string $text,
        int $userId = -1,
        string $datetime = ''
    )
    {
        if ($userId === -1) {
            $userId = Yii::$app->user->identity->id ?: null;
        }

        if ($datetime === '') {
            $datetime = date('Y-m-d H:i:s');
        }

        $log = new BaseLog(
            $datetime,
            $level,
            LogInterface::TYPE_DEFAULT,
            $userId,
            $text
        );

        return $log->write();
    }

    public static function createMethodLog(
        string $datetime,
        int $level,
        int $userId,
        string $text,
        string $controllerName,
        string $actionName,
        int $callType
    )
    {
        $log = new MethodLog(
            $datetime,
            $level,
            LogInterface::TYPE_METHOD,
            $userId,
            $text,
            $controllerName,
            $actionName,
            $callType
        );

        return $log->write();
    }

    public static function createCrudLog(
        int $level,
        string $text,
        string $query,
        int $userId = -1,
        string $datetime = ''
    )
    {
        if (Yii::$app->logRecord->checkBlock('BLOCK_LOG')) {
            if ($userId === -1) {
                $userId = Yii::$app->user->identity->id ?: null;
            }

            if ($datetime === '') {
                $datetime = date('Y-m-d H:i:s');
            }

            $log = new CrudLog(
                $datetime,
                $level,
                LogInterface::TYPE_METHOD,
                $userId,
                $text,
                $query
            );
            return $log->write();
        }
        return NULL;
    }
}