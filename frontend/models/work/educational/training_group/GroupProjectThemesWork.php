<?php

namespace frontend\models\work\educational\training_group;

use common\models\scaffold\GroupProjectThemes;
use common\models\scaffold\TrainingGroupExpert;
use common\models\scaffold\TrainingGroupLesson;
use common\repositories\dictionaries\AuditoriumRepository;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\ProjectThemeWork;
use Yii;

/**
 * @property ProjectThemeWork $projectThemeWork
 */

class GroupProjectThemesWork extends GroupProjectThemes
{
    const CONFIRM = 1;
    const NO_CONFIRM = 0;

    public static function fill(int $groupId, int $themeId, int $confirm, int $id = null)
    {
        $entity = new static();
        $entity->id = $id;
        $entity->training_group_id = $groupId;
        $entity->project_theme_id = $themeId;
        $entity->confirm = $confirm;

        return $entity;
    }

    public function __toString()
    {
        return "[GroupID: $this->training_group_id]
                [ThemeID: $this->project_theme_id]";
    }

    public function getProjectThemeWork()
    {
        return $this->hasOne(ProjectThemeWork::class, ['id' => 'project_theme_id']);
    }

    public function setConfirm(int $confirm)
    {
        $this->confirm = $confirm;
    }
}