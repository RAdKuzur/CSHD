<?php

namespace common\components\logger\search;

use common\models\work\LogWork;
use common\repositories\log\LogRepository;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class SearchLog implements SearchLogInterface
{
    /**
     * @var int[] $levels
     * @var int[] $userIds
     * @var int[] $types
     */
    public array $levels;
    public string $startDatetime;
    public string $endDatetime;
    public array $userIds;
    public array $types;
    public string $partText;

    public MethodSearchData $methodData;
    public CrudSearchData $crudData;

    private LogRepository $repository;

    public function __construct(
        MethodSearchData $methodData = null,
        CrudSearchData $crudData = null,
        LogRepository $repository = null
    )
    {
        if (is_null($methodData)) {
            $methodData = new MethodSearchData();
        }

        if (is_null($crudData)) {
            $crudData = new CrudSearchData();
        }

        if (is_null($repository)) {
            $repository = Yii::createObject(LogRepository::class);
        }

        /** @var MethodSearchData $methodData */
        $this->methodData = $methodData;

        /** @var CrudSearchData $crudData */
        $this->crudData = $crudData;

        /** @var LogRepository $repository */
        $this->repository = $repository;
    }

    public function setMethodSearchData(MethodSearchData $data)
    {
        $this->methodData = $data;
    }

    public function setCrudSearchData(CrudSearchData $data)
    {
        $this->crudData = $data;
    }

    /**
     * @param int[] $levels
     * @return static
     */
    public static function byLevels(array $levels)
    {
        $entity = new static();
        $entity->levels = $levels;
        return $entity;
    }

    public static function betweenDatetimes(string $startDatetime, string $endDatetime)
    {
        $entity = new static();
        $entity->startDatetime = $startDatetime;
        $entity->endDatetime = $endDatetime;
        return $entity;
    }

    /**
     * @param int[] $userIds
     * @return static
     */
    public static function byUserIds(array $userIds)
    {
        $entity = new static();
        $entity->userIds = $userIds;
        return $entity;
    }

    /**
     * @param int[] $types
     * @return static
     */
    public static function byTypes(array $types)
    {
        $entity = new static();
        $entity->types = $types;
        return $entity;
    }

    public static function byPartText(string $partText)
    {
        $entity = new static();
        $entity->partText = $partText;
        return $entity;
    }

    /**
     * @param int[] $levels
     * @param string $startDatetime
     * @param string $endDatetime
     * @param int[] $userIds
     * @param int[] $types
     * @param string $partText
     * @return static
     */
    public static function byParams(
        array $levels = [],
        string $startDatetime = '1900-01-01',
        string $endDatetime = '1900-01-01',
        array $userIds = [],
        array $types = [],
        string $partText = ''
    )
    {
        $entity = new static();
        $entity->levels = $levels;
        $entity->startDatetime = $startDatetime;
        $entity->endDatetime = $endDatetime;
        $entity->userIds = $userIds;
        $entity->types = $types;
        $entity->partText = $partText;
        return $entity;
    }

    /**
     * Составляет ActiveQuery по основным полям таблицы и возвращает результат
     *
     * @return array|ActiveRecord[]
     */
    public function findByBaseData() : array
    {
        $baseQuery = $this->repository->query();
        if (isset($this->levels) && count($this->levels) > 0) {
            $baseQuery = $baseQuery->andWhere(['IN', 'level', $this->levels]);
        }
        if (isset($this->userIds) && count($this->userIds) > 0) {
            $baseQuery = $baseQuery->andWhere(['IN', 'user_id', $this->userIds]);
        }
        if (isset($this->types) && count($this->types) > 0) {
            $baseQuery = $baseQuery->andWhere(['IN', 'type', $this->types]);
        }
        if (isset($this->startDatetime) && $this->startDatetime != '1900-01-01') {
            $baseQuery = $baseQuery->andWhere(['>=', 'datetime', $this->startDatetime]);
        }
        if (isset($this->endDatetime) && $this->endDatetime != '1900-01-01') {
            $baseQuery = $baseQuery->andWhere(['<=', 'datetime', $this->endDatetime]);
        }
        if (isset($this->partText) && $this->partText != '') {
            $baseQuery = $baseQuery->andWhere(['LIKE', 'text', $this->partText]);
        }

        return $this->repository->findByQuery($baseQuery);
    }

    /**
     * Организует поиск по add_data (json-строка)
     *
     * @param LogWork[] $logs
     */
    public function findByAddData(array $logs)
    {
        $result = [];
        foreach ($logs as $log) {
            if ($this->haveAddData($log->add_data)) {
                $result[] = $log;
            }
        }

        return $result;
    }

    /**
     * Метод проверки add_data по всем полям имплементирующим SearchDataInterface
     *
     * @param string $addData
     * @return bool
     */
    private function haveAddData(string $addData)
    {
        return
            $this->methodData->haveData($addData) ||
            $this->crudData->haveData($addData);
    }
}