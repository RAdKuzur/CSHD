<?php

namespace console\controllers\copy;

use common\models\scaffold\TrainingGroup;
use frontend\models\work\educational\training_group\LessonThemeWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\ThematicPlanWork;
use frontend\services\educational\TrainingGroupService;
use Yii;
use yii\console\Controller;
/* @var $lessonTheme LessonThemeWork*/
class FixCopyController extends Controller
{
    private TrainingGroupService $trainingGroupService;
    public function __construct(
        $id,
        $module,
        TrainingGroupService $trainingGroupService,
        $config = []
    )
    {
        $this->trainingGroupService = $trainingGroupService;
        parent::__construct($id, $module, $config);
    }

    public function actionFixThematicPlan()
    {
        ini_set('memory_limit', '-1');
        $groups = TrainingGroupWork::find()->all();
        foreach ($groups as $group) {
            $this->trainingGroupService->createLessonThemes($group->id);
        }
    }
}