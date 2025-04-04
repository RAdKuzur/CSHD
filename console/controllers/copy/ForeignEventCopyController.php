<?php

namespace console\controllers\copy;

use common\services\general\PeopleStampService;
use Yii;
use yii\console\Controller;

class ForeignEventCopyController extends Controller
{
    private PeopleStampService $peopleStampService;
    public function __construct(
        $id,
        $module,
        PeopleStampService $peopleStampService,
        $config = [])
    {
        $this->peopleStampService = $peopleStampService;
        parent::__construct($id, $module, $config);
    }
    public function actionForeignEventCopy(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM foreign_event");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('foreign_event',
                [
                    'id' => $record['id'],
                    'order_participant_id' => $record['order_participation_id'],
                    'name' => $record['name'],
                    'organizer_id' => $record['company_id'],
                    'begin_date' => $record['start_date'],
                    'end_date' => $record['finish_date'],
                    'city' => $record['city'],
                    'format' => $record['event_way_id'],
                    'level' => $record['event_level_id'],
                    'minister' => $record['is_minpros'],
                    'min_age' => $record['min_participants_age'],
                    'max_age' => $record['max_participants_age'],
                    'key_words' => $record['key_words'],
                    'escort_id' => $record['escort_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['escort_id']) : NULL,
                    'add_order_participant_id' => $record['add_order_participation_id'],
                    'order_business_trip_id' => $record['order_business_trip_id'],
                    'creator_id' => $record['creator_id'],
                    'last_edit_id' => $record['last_edit_id']
                ]
            );
            //add files
            $command->execute();
        }
    }
    public function actionDeleteForeignEvent()
    {
        Yii::$app->db->createCommand()->delete('foreign_event')->execute();
    }
    public function actionDeleteAll(){
        $this->actionDeleteForeignEvent();
    }
    public function actionCopyAll(){
        $this->actionForeignEventCopy();
    }
}