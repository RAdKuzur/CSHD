<?php

namespace console\controllers\seeders;

use common\models\work\UserWork;
use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\order\DocumentOrderWork;
use Yii;
use yii\console\Controller;

class ForeignEventSeederController extends Controller
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
            $beginDate = $this->randomHelper->randomDate();
            $endDate = $this->randomHelper->randomDate($beginDate);
            $command = Yii::$app->db->createCommand();
            $command->insert('foreign_event', [
                'order_participant_id' => $this->randomHelper->randomItem(DocumentOrderWork::find()->where(['type' => DocumentOrderWork::ORDER_EVENT])->all())['id'],
                'name' => $this->randomHelper->generateRandomString(25),
                'organizer_id' => $this->randomHelper->randomItem(CompanyWork::find()->all())['id'],
                'begin_date' => $beginDate,
                'end_date' => $endDate,
                'city' => $this->randomHelper->generateRandomString(),
                'format' => rand(1,3),
                'level' => rand(3,8),
                'minister' => rand(0, 1),
                'min_age' => rand(0, 10),
                'max_age' => rand(10, 20),
                'key_words' => $this->randomHelper->generateRandomString(40),
                'escort_id' => $this->randomHelper->randomItem(PeopleStampWork::find()->all())['id'],
                'add_order_participant_id' => NULL,
                'order_business_trip_id' => NULL,
                'creator_id' => $this->randomHelper->randomItem(UserWork::find()->all())['id'],
                'last_edit_id' => $this->randomHelper->randomItem(UserWork::find()->all())['id'],
            ]);
            $command->execute();
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('foreign_event')->execute();
    }
}