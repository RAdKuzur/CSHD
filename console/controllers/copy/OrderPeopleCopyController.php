<?php

namespace console\controllers\copy;

use common\services\general\PeopleStampService;
use Yii;
use yii\console\Controller;

class OrderPeopleCopyController extends Controller
{
    private PeopleStampService $peopleStampService;
    public function __construct(
        $id,
        $module,
        PeopleStampService $peopleStampService,
        $config = []
    )
    {
        $this->peopleStampService = $peopleStampService;
        parent::__construct($id, $module, $config);
    }
    public function actionCopyOrderPeople(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM responsible");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('order_people',
                [
                    'order_id' => $record['document_order_id'],
                    'people_id' => $record['people_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['people_id']) : NULL,
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteOrderPeople(){
        Yii::$app->db->createCommand()->delete('order_people')->execute();
    }
    public function actionCopyAll()
    {
        $this->actionCopyOrderPeople();
    }
    public function actionDeleteAll()
    {
        $this->actionDeleteOrderPeople();
    }
}