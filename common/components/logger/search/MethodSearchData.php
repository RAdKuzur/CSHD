<?php

namespace common\components\logger\search;

use common\repositories\log\LogRepository;
use yii\db\ActiveQuery;

class MethodSearchData implements SearchDataInterface
{
    /**
     * @var string[] $controllerNames
     * @var string[] $actionNames
     * @var int[] $callTypes
     */
    public array $controllerNames;
    public array $actionNames;
    public array $callTypes;

    public function __construct(
        array $controllerNames = [],
        array $actionNames = [],
        array $callTypes = []
    )
    {
        $this->controllerNames = $controllerNames;
        $this->actionNames = $actionNames;
        $this->callTypes = $callTypes;
    }

    /**
     * @var string[] $controllerNames
     * @var string[] $actionNames
     * @var int[] $callTypes
     */
    public static function create(
        array $controllerNames = [],
        array $actionNames = [],
        array $callTypes = []
    ) : MethodSearchData
    {
        $entity = new static();
        $entity->controllerNames = $controllerNames;
        $entity->actionNames = $actionNames;
        $entity->callTypes = $callTypes;

        return $entity;
    }

    /**
     * Проверка на хотя бы один установленный фильтр
     * Если фильтры не установлены, то haveData по умолчанию true
     *
     * @return bool
     */
    private function isHaveFilter() : bool
    {
        return
            count($this->controllerNames) > 0 ||
            count($this->actionNames) > 0 ||
            count($this->callTypes) > 0;
    }


    /**
     * Проверяет, есть ли в данных в виде json строки совпадающие key-value значения
     * Пример:
     *   addData {controllerName: 'test', callType: 12}
     *   $this->controllerNames = ['some', 'test']
     *   return - true
     *
     * @param string $addData
     * @return bool
     */
    public function haveData(string $addData) : bool
    {
        if (!$this->isHaveFilter()) {
            return true;
        }

        $data = json_decode($addData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        $checks = [
            'controllerName' => $this->controllerNames,
            'actionName' => $this->actionNames,
            'callType' => $this->callTypes,
        ];

        foreach ($checks as $key => $values) {
            if (!empty($values) && !(isset($data[$key]) && in_array($data[$key], $values, true))) {
                return false;
            }
        }

        return true;
    }
}