<?php

namespace common\components\logger\method;

use common\components\logger\base\BaseLog;
use common\components\logger\base\LogInterface;
use common\components\logger\search\SearchLog;
use common\components\logger\search\SearchLogInterface;
use common\components\logger\search\MethodSearchData;
use common\models\work\LogWork;
use common\repositories\log\LogRepository;

class MethodLog extends BaseLog implements LogInterface
{
    // Типы вызовов логируемого метода
    const CTYPE_ACTION = 0;
    const CTYPE_SYSTEM = 1;

    public string $controllerName;
    public string $actionName;
    public int $callType;

    // query-параметры url или параметры вызываемой функции
    public array $queryParams;

    public function __construct(
        string $datetime,
        int $level,
        int $type,
        int $userId,
        string $text,
        string $controllerName,
        string $actionName,
        int $callType,
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

        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
        $this->callType = $callType;
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
            'controllerName' => $this->controllerName,
            'actionName' => $this->actionName,
            'callType' => $this->callType,
        ]);
    }
}