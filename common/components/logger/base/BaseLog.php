<?php

namespace common\components\logger\base;

use common\models\work\LogWork;
use common\repositories\log\LogRepository;
use Yii;

class BaseLog implements LogInterface
{
    public string $datetime;
    public int $level;
    public int $type;
    public ?int $userId;
    public string $text;

    protected LogRepository $repository;

    public function __construct(
        string $datetime,
        int $level,
        int $type,
        ?int $userId,
        string $text,
        LogRepository $repository = null
    )
    {
        $this->datetime = $datetime;
        $this->level = $level;
        $this->type = $type;
        $this->userId = $userId;
        $this->text = $text;

        if (!$repository) {
            $repository = Yii::createObject(LogRepository::class);
        }
        /** @var LogRepository $repository */
        $this->repository = $repository;
    }

    public function createEntity() : LogWork
    {
        return LogWork::fill(
            $this->datetime,
            $this->level,
            $this->type,
            $this->userId,
            $this->text
        );
    }

    /**
     * @throws \yii\db\Exception
     */
    public function write(): int
    {
        $log = self::createEntity();
        return $this->repository->save($log);
    }

    public function createAddData(): string
    {
        return json_encode([]);
    }
}