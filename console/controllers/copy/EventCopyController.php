<?php

namespace console\controllers\copy;

use common\services\general\PeopleStampService;
use console\helper\FileTransferHelper;
use frontend\models\work\event\EventWork;
use Yii;
use yii\console\Controller;

class EventCopyController extends Controller
{
    private FileTransferHelper $fileTransferHelper;
    private PeopleStampService $peopleStampService;
    public function __construct(
        $id,
        $module,
        FileTransferHelper $fileTransferHelper,
        PeopleStampService $peopleStampService,
        $config = [])
    {
        $this->fileTransferHelper = $fileTransferHelper;
        $this->peopleStampService = $peopleStampService;
        parent::__construct($id, $module, $config);
    }

    public function actionCopyEvent(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM event");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('event',
                [
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'start_date' => $record['start_date'],
                    'finish_date' => $record['finish_date'],
                    'event_type' => $record['event_type_id'],
                    'event_form' => $record['event_form_id'],
                    'event_level' => $record['event_level_id'],
                    'event_way' => $record['event_way_id'],
                    'address' => $record['address'],
                    'is_federal' => $record['is_federal'],
                    'responsible1_id' => $record['responsible_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['responsible_id']) : NULL,
                    'responsible2_id' => $record['responsible2_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['responsible2_id']) : NULL,
                    'key_words' => $record['key_words'],
                    'comment' => $record['comment'],
                    'order_id' => $record['order_id'],
                    'regulation_id' => $record['regulation_id'],
                    'contains_education' => $record['contains_education'],
                    'participation_scope' => $record['participation_scope_id'],
                    //'child_participants_count' => ,
                    //'child_rst_participants_count' => ,
                    //'teacher_participants_count' => ,
                    //'other_participants_count' => ,
                    //'age_left_border' => ,
                    //'age_right_border' => ,
                    'creator_id' => $record['creator_id'],
                ]

            );
            $command->execute();
            //protocol, photos, reporting_doc & other_files
            //$this->fileTransferHelper->createFiles();
        }
    }
    public function actionCopyEventBranch()
    {
        $query = Yii::$app->old_db->createCommand("SELECT * FROM event_branch");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('event_branch',
                [
                    'id' => $record['id'],
                    'event_id' => $record['event_id'],
                    'branch' => $record['branch_id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionCopyEventScope(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM event_scope");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('event_scope',
                [
                    'id' => $record['id'],
                    'event_id' => $record['event_id'],
                    'participation_scope' => $record['participation_scope_id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteEvent(){
        Yii::$app->db->createCommand()->delete('event')->execute();
    }
    public function actionDeleteEventBranch(){
        Yii::$app->db->createCommand()->delete('event_branch')->execute();
    }
    public function actionDeleteEventScope(){
        Yii::$app->db->createCommand()->delete('event_scope')->execute();
    }
    public function actionDeleteAll()
    {
        $this->actionDeleteEventScope();
        $this->actionDeleteEventBranch();
        $this->actionDeleteEvent();
    }
    public function actionCopyAll()
    {
        $this->actionCopyEvent();
        $this->actionCopyEventBranch();
        $this->actionCopyEventScope();
    }
}