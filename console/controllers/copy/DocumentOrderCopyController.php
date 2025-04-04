<?php

namespace console\controllers\copy;

use common\components\dictionaries\base\NomenclatureDictionary;
use common\services\general\PeopleStampService;
use console\helper\FileTransferHelper;
use frontend\models\work\order\DocumentOrderWork;
use Yii;
use yii\console\Controller;

class DocumentOrderCopyController extends Controller
{
    private PeopleStampService $peopleStampService;
    private FileTransferHelper $fileTransferHelper;
    public function __construct(
        $id,
        $module,
        PeopleStampService $peopleStampService,
        FileTransferHelper $fileTransferHelper,
        $config = [])
    {
        $this->peopleStampService = $peopleStampService;
        $this->fileTransferHelper = $fileTransferHelper;
        parent::__construct($id, $module, $config);
    }

    public function actionOrderCopy(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM document_order");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('document_order',
                [
                    'id' => $record['id'],
                    'order_copy_id' => $record['order_copy_id'],
                    'order_number' => $record['order_number'],
                    'order_postfix' => $record['order_postfix'],
                    'order_name' => $record['order_name'],
                    'order_date' => $record['order_date'],
                    'signed_id' => $record['signed_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['signed_id']) : NULL,
                    'bring_id' => $record['bring_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['bring_id']) : NULL,
                    'executor_id' => $record['executor_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['executor_id']) : NULL,
                    'key_words' => $record['key_words'],
                    'creator_id' => $record['creator_id'],
                    'last_edit_id' => $record['last_edit_id'],
                    'type' => $this->setType($record),
                    'state' => $record['state'],
                    'nomenclature_id' => $record['nomenclature_id'],
                    'study_type' => $record['study_type'],
                ]
            );
            $command->execute();
        }
    }
    public function actionCopyDocumentOrderSupplement()
    {
        $query = Yii::$app->old_db->createCommand("SELECT * FROM document_order_supplement");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('order_event_generate',
                [
                    'id' => $record['id'],
                    'order_id' => $record['document_order_id'],
                    'purpose' => $record['foreign_event_goals_id'],
                    'doc_event' => $record['compliance_document'],
                    'resp_people_info_id' => $record['collector_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['collector_id']) : NULL,
                    'extra_resp_insert_id' => $record['contributor_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['contributor_id']) : NULL,
                    'time_provision_day' => $record['information_deadline'],
                    'time_insert_day' => $record['input_deadline'],
                    'document_details' => $record['document_details'],
                    'extra_resp_method_id' => $record['methodologist_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['methodologist_id']) : NULL,
                    'extra_resp_info_stuff_id' => $record['informant_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['informant_id']) : NULL,
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteDocumentOrder()
    {
        Yii::$app->db->createCommand()->delete('document_order')->execute();
    }
    public function actionDeleteDocumentOrderSupplement()
    {
        Yii::$app->db->createCommand()->delete('order_event_generate')->execute();
    }
    public function actionDeleteAll()
    {
        $this->actionDeleteDocumentOrderSupplement();
        $this->actionDeleteDocumentOrder();
    }
    public function actionCopyAll()
    {
        $this->actionOrderCopy();
        $this->actionCopyDocumentOrderSupplement();
    }
    public function setType($record){
        if ($record['type'] == DocumentOrderWork::ORDER_EVENT && $record['order_number'] == NomenclatureDictionary::ADMIN_ORDER){
            return DocumentOrderWork::ORDER_EVENT;
        }
        else if($record['order_number'] != '02-02' && $record['order_number'] != '01-04'){
            return DocumentOrderWork::ORDER_TRAINING;
        }
        else if($record['type'] != DocumentOrderWork::ORDER_EVENT &&
            ($record['order_number'] == NomenclatureDictionary::ADMIN_ORDER || $record['order_number'] == '01-04')){
            return DocumentOrderWork::ORDER_MAIN;
        }
        else {
            return DocumentOrderWork::ORDER_MAIN;
        }



    }
}