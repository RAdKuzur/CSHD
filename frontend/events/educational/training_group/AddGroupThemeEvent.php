<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\educational\GroupProjectThemesRepository;
use Yii;

class AddGroupThemeEvent implements EventInterface
{
    private $groupId;
    private $themeId;
    private $confirm;

    private GroupProjectThemesRepository $repository;

    public function __construct(
        $groupId,
        $themeId,
        $confirm
    )
    {
        $this->groupId = $groupId;
        $this->themeId = $themeId;
        $this->confirm = $confirm;
        $this->repository = Yii::createObject(GroupProjectThemesRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareCreate(
                $this->groupId,
                $this->themeId,
                $this->confirm
            )
        ];
    }
}