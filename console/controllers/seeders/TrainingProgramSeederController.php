<?php

namespace console\controllers\seeders;

use common\models\work\UserWork;
use frontend\models\work\general\PeopleStampWork;
use Yii;
use yii\console\Controller;

class TrainingProgramSeederController extends Controller
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
    public function actionRun($amount = 10){
        for($i = 0; $i < $amount; $i++){
            $command = Yii::$app->db->createCommand();
            $command->insert('training_program', [
                'name' => $this->randomHelper->generateRandomString(8),
                'thematic_direction' => rand(0, 1),
                'level' => rand(3, 8),
                'ped_council_date' => $this->randomHelper->randomDate(),
                'ped_council_number' => rand(0, 10),
                'author_id' => $this->randomHelper->randomItem(PeopleStampWork::find()->all())['id'],
                'capacity' => rand(0, 10),
                'hour_capacity' => rand(0, 10),
                'student_left_age' => rand(0, 10),
                'student_right_age' => rand(10, 20),
                'focus' => rand(1, 5),
                'allow_remote' => rand(1, 3),
                'actual' => rand(0, 1),
                'certificate_type' => rand(1,4),
                'description' => $this->randomHelper->generateRandomString(20),
                'key_words' => $this->randomHelper->generateRandomString(40),
                'is_network' => rand(0, 1),
                'creator_id' => ($this->randomHelper->randomItem(UserWork::find()->all()))['id'],
                'last_edit_id' => $this->randomHelper->randomItem(UserWork::find()->all())['id'],
                'created_at' => NULL,
                'updated_at' => NULL
            ]);
            $command->execute();
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('training_program')->execute();
    }
}