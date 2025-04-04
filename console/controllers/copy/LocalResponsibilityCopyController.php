<?php

namespace console\controllers\copy;

use common\services\general\PeopleStampService;
use Yii;
use yii\console\Controller;

class LocalResponsibilityCopyController extends Controller
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

    public function actionCopyAuditorium(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM auditorium");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('auditorium',
                [
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'square' => $record['square'],
                    'text' => $record['text'],
                    'capacity' => $record['capacity'],
                    'is_education' => $record['is_education'],
                    'branch' => $record['branch_id'],
                    'include_square' => $record['include_square'],
                    'window_count' => $record['window_count'],
                    'auditorium_type' => $record['auditorium_type_id'],
                ]
            );
            //add files
            $command->execute();
        }
    }
    public function actionCopyLocalResponsibility()
    {
        $query = Yii::$app->old_db->createCommand("SELECT * FROM local_responsibility");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('local_responsibility',
                [
                    'id' => $record['id'],
                    'responsibility_type' => $record['responsibility_type_id'],
                    'branch' => $record['branch_id'],
                    'auditorium_id' => $record['auditorium_id'],
                    'quant' => $record['quant'],
                    'people_stamp_id' => $record['people_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['people_id']) : NULL,
                    'regulation_id' => $record['regulation_id'],
                ]
            );
            //add files
            $command->execute();
        }
    }
    public function actionCopyLegacyResponsible()
    {
        $query = Yii::$app->old_db->createCommand("SELECT * FROM legacy_responsible");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('legacy_responsible',
                [
                    'id' => $record['id'],
                    'people_stamp_id' => $record['people_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['people_id']) : NULL,
                    'responsibility_type' => $record['responsibility_type_id'],
                    'branch' => $record['branch_id'],
                    'auditorium_id' => $record['auditorium_id'],
                    'quant' => $record['quant'],
                    'start_date' => $record['start_date'],
                    'end_date' => $record['end_date'],
                    'order_id' => $record['order_id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteAuditorium()
    {
        Yii::$app->db->createCommand()->delete('auditorium')->execute();
    }
    public function actionDeleteLocalResponsibility()
    {
        Yii::$app->db->createCommand()->delete('local_responsibility')->execute();
    }
    public function actionDeleteLegacyResponsible(){
        Yii::$app->db->createCommand()->delete('legacy_responsible')->execute();
    }
    public function actionDeleteAll()
    {
        $this->actionDeleteLocalResponsibility();
        $this->actionDeleteLegacyResponsible();
        $this->actionDeleteAuditorium();
    }
    public function actionCopyAll(){
        $this->actionCopyAuditorium();
        $this->actionCopyLocalResponsibility();
        $this->actionCopyLegacyResponsible();
    }
}