<?php

namespace frontend\events\general;

use common\events\EventInterface;
use common\helpers\files\FilesHelper;
use common\repositories\general\FilesRepository;
use Yii;

class FileCreateEvent implements EventInterface
{
    private $tableName;
    private $tableRowId;
    private $filetype;
    private $filepath;
    private $loadType;
    private FilesRepository $repository;

    public function __construct(
        $tableName,
        $tableRowId,
        $filetype,
        $filepath,
        $loadType
    )
    {
        $this->tableName = $tableName;
        $this->tableRowId = $tableRowId;
        $this->filetype = $filetype;
        $this->filepath = $filepath;
        $this->loadType = $loadType;
        $this->repository = Yii::createObject(FilesRepository::class);
    }

    public function isSingleton(): bool
    {
        return true;
    }

    public function isSingleLoad()
    {
        return $this->loadType == FilesHelper::LOAD_TYPE_SINGLE;
    }

    public function isMultiLoad()
    {
        return $this->loadType == FilesHelper::LOAD_TYPE_MULTI;
    }

    public function execute()
    {
        if ($this->isMultiLoad()) {
            return [
                $this->repository->prepareCreate(
                    $this->tableName,
                    $this->tableRowId,
                    $this->filetype,
                    $this->filepath
                )
            ];
        }

        if ($this->isSingleLoad()) {
            $exists = count($this->repository->get($this->tableName, $this->tableRowId, $this->filetype)) > 0;

            return [
                $exists ?
                    $this->repository->prepareUpdate(
                        $this->tableName,
                        $this->tableRowId,
                        $this->filetype,
                        $this->filepath
                    ) :
                    $this->repository->prepareCreate(
                        $this->tableName,
                        $this->tableRowId,
                        $this->filetype,
                        $this->filepath
                    )
            ];
        }

        return [];
    }
}