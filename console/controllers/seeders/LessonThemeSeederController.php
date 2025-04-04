<?php

namespace console\controllers\seeders;

use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_program\ThematicPlanWork;
use frontend\models\work\general\PeopleStampWork;
use Yii;
use yii\console\Controller;

class LessonThemeSeederController extends Controller
{
    private RandomHelper $randomHelper;
    public function __construct(
        $id,
        $module,
        RandomHelper $randomHelper,
        $config = []
    )
    {
        $this->randomHelper = $randomHelper;
        parent::__construct($id, $module, $config);
    }
    public function actionRun(){
        $teacherId = $this->randomHelper->randomItem(PeopleStampWork::find()->all())['id'];
        $lessons = TrainingGroupLessonWork::find()->all();
        foreach ($lessons as $lesson){
            $command = Yii::$app->db->createCommand();
            $command->insert('lesson_theme', [
                'training_group_lesson_id' => $lesson->id,
                'thematic_plan_id' => $this->randomHelper->randomItem(ThematicPlanWork::find()->all())['id'],
                'teacher_id' => $teacherId
            ]);
            $command->execute();
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('lesson_theme')->execute();
    }
}