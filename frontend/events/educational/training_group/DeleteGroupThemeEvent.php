<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\educational\GroupProjectThemesRepository;
use Yii;

class DeleteGroupThemeEvent implements EventInterface
{
    private $id;

    private GroupProjectThemesRepository $repository;

    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->repository = Yii::createObject(GroupProjectThemesRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareDelete(
                $this->id
            )
        ];
    }
}