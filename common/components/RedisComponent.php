<?php

namespace common\components;

use Yii;
use yii\base\Exception;
use yii\redis\Connection;

class RedisComponent extends Connection
{
    const HOST = '127.0.0.1';
    const PORT = 6379;

    public function isConnected()
    {
        if (Yii::$app->cache->exists('redis_state') && Yii::$app->cache->get('redis_state') === 'error') {
            return false;
        }

        $connection = @fsockopen(self::HOST, self::PORT, $errno, $errstr, 1); // 1 секунда тайм-аута

        if ($connection) {
            fclose($connection);
            return true;
        } else {
            // Записываем состояние ошибки в кэш для последующего обращения к нему, а не к функции проверки
            Yii::$app->cache->set('redis_state', 'error', 600);
            return false;
        }
    }
}