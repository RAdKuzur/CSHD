<?php

namespace frontend\events\general;

use common\events\EventInterface;
use common\repositories\general\FilesRepository;
use Yii;

class MultiplyFilesCreateEvent implements EventInterface
{
    private $tableName;
    private $tableRowId;
    private $filetype;
    private $filepath = [];
    private FilesRepository $repository;

    public function __construct(
        $tableName,
        $tableRowId,
        $filetype,
        array $filepath
    )
    {
        $this->tableName = $tableName;
        $this->tableRowId = $tableRowId;
        $this->filetype = $filetype;
        $this->filepath = $filepath;
        $this->repository = Yii::createObject(FilesRepository::class);
    }

    public function isSingleton(): bool
    {
        return true;
    }

    public function execute()
    {
        $queries = [];
        foreach ($this->filepath as $filepath) {
            $queries[] =
                $this->repository->prepareCreate(
                    $this->tableName,
                    $this->tableRowId,
                    $this->filetype,
                    $filepath
                );
        }

        return $queries;
    }
}