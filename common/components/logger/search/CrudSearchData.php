<?php

namespace common\components\logger\search;

use common\repositories\log\LogRepository;
use yii\db\ActiveQuery;

class CrudSearchData implements SearchDataInterface
{
    /**
     * @var string $queryPart
     */
    public string $queryPart;

    public function __construct(
        string $queryPart = ''
    )
    {
        $this->queryPart = $queryPart;
    }

    /**
     * @var string $queryPart
     */
    public static function create(
        string $queryPart = ''
    ) : CrudSearchData
    {
        $entity = new static();
        $entity->queryPart = $queryPart;

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
            strlen($this->queryPart) > 0;
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
            'query' => $this->queryPart
        ];

        foreach ($checks as $key => $value) {
            var_dump($value);
            var_dump(strpos($data[$key], $value));
            if (!empty($value) && strpos($data[$key], $value) === false) {
                return false;
            }
        }

        return true;
    }
}