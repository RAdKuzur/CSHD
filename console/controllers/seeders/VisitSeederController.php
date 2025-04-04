<?php

namespace console\controllers\seeders;

use common\repositories\educational\VisitRepository;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\console\Controller;

class VisitSeederController extends Controller
{
    private RandomHelper $randomHelper;
    private VisitRepository $visitRepository;
    public function __construct(
        $id,
        $module,
        RandomHelper $randomHelper,
        VisitRepository $visitRepository,
        $config = []
    )
    {
        $this->randomHelper = $randomHelper;
        $this->visitRepository = $visitRepository;
        parent::__construct($id, $module, $config);
    }
    public function actionRun()
    {
        $groups = TrainingGroupWork::find()->all();
        foreach ($groups as $group) {
            $participants = TrainingGroupParticipantWork::find()->where(['training_group_id' => $group->id])->all();
            foreach ($participants as $participant) {
                $lessons = [];
                $lessonsDB = TrainingGroupLessonWork::find()->where(['training_group_id' => $group->id])->all();
                foreach ($lessonsDB as $lesson) {
                    $lessons[] = '{"lesson_id":' . $lesson->id . ',' . '"status":' . rand(0,3). '}';
                }
                $lessons = '['.(implode(',', $lessons)).']';
                $command = Yii::$app->db->createCommand();
                $command->insert('visit', [
                    'lessons' => $lessons,
                    'training_group_participant_id' => $participant->id,
                ]);
                $command->execute();
            }

        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('visit')->execute();
    }
}