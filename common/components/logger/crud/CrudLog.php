<?php

namespace common\components\logger\crud;

use common\components\logger\base\BaseLog;
use common\components\logger\base\LogInterface;
use common\repositories\log\LogRepository;

class CrudLog extends BaseLog implements LogInterface
{
    public string $query;

    public function __construct(
        string $datetime,
        int $level,
        int $type,
        ?int $userId,
        string $text,
        string $query,
        LogRepository $repository = null
    )
    {
        parent::__construct(
            $datetime,
            $level,
            $type,
            $userId,
            $text,
            $repository
        );

        $this->query = $query;
    }

    /**
     * @throws \yii\db\Exception
     */
    public function write(): int
    {
        $log = parent::createEntity();
        $log->setAddData($this->createAddData());

        return $this->repository->save($log);
    }

    public function createAddData(): string
    {
        return json_encode([
            'query' => $this->query,
        ]);
    }
}