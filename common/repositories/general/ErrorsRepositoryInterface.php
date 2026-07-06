<?php

namespace common\repositories\general;

use common\models\Error;
use common\models\work\ErrorsWork;

interface ErrorsRepositoryInterface
{
    public function get(int $id);
    public function getChangeableErrors();
    public function getErrorsByTableName(string $tableName);
    public function getErrorsByTableRow(string $tableName, int $rowId);
    public function getAmnestyErrorsByTableRow(string $tableName, int $rowId);
    public function getErrorsByTableRowsBranchTypes(string $tableName, array $rowIds, int $branch = null, array $types = []);
    public function getErrorsByTableRowError(string $tableName, int $rowId, string $error);
    public function getErrorByTableName($tableName);
    public function getErrorsIdsByTableName($tableName);
    public function getQueryForErrorsByTable(string $tableName);
    public function delete(ErrorsWork $model);
    public function deleteByListId(array $ids);
    public function save(ErrorsWork $model);
    public function saveMultiple(array $models);
}