<?php

namespace console\controllers\copy;

use common\services\general\PeopleStampService;
use console\helper\FileTransferHelper;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\models\work\document_in_out\DocumentOutWork;
use Yii;
use yii\console\Controller;

class DocumentInOutCopyController extends Controller
{
    private PeopleStampService $peopleStampService;
    private PeopleTablesCopyController $peopleTablesCopyController;
    public function __construct(
        $id,
        $module,
        PeopleStampService $peopleStampService,
        PeopleTablesCopyController $peopleTablesCopyController,
        $config = [])
    {
        $this->peopleStampService = $peopleStampService;
        $this->peopleTablesCopyController = $peopleTablesCopyController;
        parent::__construct($id, $module, $config);
    }

    public function actionCopyCompany(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM company");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('company',
                [
                    'id' => $record['id'],
                    'company_type' => $record['company_type_id'],
                    'name' => $record['name'],
                    'short_name' => $record['short_name'],
                    'is_contractor' => $record['is_contractor'],
                    'inn' => $record['inn'],
                    'category_smsp' => $record['category_smsp_id'],
                    'comment' => $record['comment'],
                    //'last_edit_id' => $record['last_edit_id'],
                    'phone_number' => $record['phone_number'],
                    'email' => $record['email'],
                    'site' => $record['site'],
                    'ownership_type' => $record['ownership_type_id'],
                    'okved' => $record['okved'],
                    'head_fio' => $record['head_fio'],
                ]
            );
            $command->execute();
        }
    }
    public function actionCopyUser(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM user");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('user',
                [
                    'id' => $record['id'],
                    'firstname' => $record['firstname'],
                    'surname' => $record['secondname'],
                    'patronymic' => $record['patronymic'],
                    'username' => $record['username'],
                    'auth_key' => $record['auth_key'],
                    'password_hash' => $record['password_hash'],
                    'password_reset_token' => $record['password_reset_token'],
                    'email' => $record['email'],
                    'aka' => $record['aka'],
                    'status' => $record['status'],
                    //'created_at' => $record['created_at'],
                    //'updated_at' => $record['updated_at'],
                    'creator_id' => $record['creator_id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionCopyPeople(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM people");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('people',
                [
                    'id' => $record['id'],
                    'firstname' => $record['firstname'],
                    'surname' => $record['secondname'],
                    'patronymic' => $record['patronymic'],
                    'company_id' => $record['company_id'],
                    'position_id' => $record['position_id'],
                    'short' => $record['short'],
                    'branch' => $record['branch_id'],
                    'birthdate' => $record['birthdate'],
                    'sex' => $record['sex'],
                    'genitive_surname' => $record['genitive'],
                ]
            );
            $command->execute();
        }
    }
    public function actionCopyDocumentIn()
    {
        $query = Yii::$app->old_db->createCommand("SELECT * FROM document_in");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('document_in',
                [
                    'id' => $record['id'],
                    'local_number' => $record['local_number'],
                    'local_postfix' => $record['local_postfix'],
                    'local_date' => $record['local_date'],
                    'real_number' => $record['real_number'],
                    'real_date' => $record['real_date'],
                    'correspondent_id' => $record['correspondent_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['correspondent_id']) : NULL,
                    'position_id' => $record['position_id'],
                    'company_id' => $record['company_id'],
                    'document_theme' => $record['document_theme'],
                    'signed_id' => $record['signed_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['signed_id']) : NULL,
                    'target' => $record['target'],
                    'get_id' => $record['get_id'],
                    'send_method' => $record['send_method_id'],
                    'key_words' => $record['key_words'],
                    'need_answer' => $record['needAnswer'],
                    'creator_id' => $record['creator_id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionCopyDocumentOut(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM document_out");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('document_out',
                [
                    'id' => $record['id'],
                    'document_number' => $record['document_number'],
                    'document_postfix' => $record['document_postfix'],
                    'document_date' => $record['document_date'],
                    'document_name' => $record['document_name'],
                    'document_theme' => $record['document_theme'],
                    'correspondent_id' => $record['correspondent_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['correspondent_id']) : NULL,
                    'position_id' => $record['position_id'],
                    'company_id' => $record['company_id'],
                    'signed_id' => $record['signed_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['signed_id']) : NULL,
                    'executor_id' => $record['executor_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['executor_id']) : NULL,
                    'send_method' => $record['send_method_id'],
                    'sent_date' => $record['sent_date'],
                    'key_words' => $record['key_words'],
                    'is_answer' => $record['isAnswer'],
                    'creator_id' => $record['creator_id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionCopyInOutDocuments(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM in_out_docs");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('in_out_documents',
                [
                    'id' => $record['id'],
                    'document_in_id' => $record['document_in_id'],
                    'document_out_id' => $record['document_out_id'],
                    'date' => $record['date'],
                    'responsible_id' => '' ? $this->peopleStampService->createStampFromPeople($record['people_id']) : NULL,
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteCompany(){
        Yii::$app->db->createCommand()->delete('company')->execute();
    }
    public function actionDeletePeople(){
        Yii::$app->db->createCommand()->delete('people')->execute();
    }
    public function deletePeopleStamp()
    {
        Yii::$app->db->createCommand()->delete('people_stamp')->execute();
    }
    public function actionDeleteUser(){
        Yii::$app->db->createCommand()->delete('user')->execute();
    }
    public function actionDeleteDocumentIn(){
        Yii::$app->db->createCommand()->delete('document_in')->execute();
    }
    public function actionDeleteDocumentOut(){
        Yii::$app->db->createCommand()->delete('document_out')->execute();
    }
    public function actionDeleteInOutDocuments(){
        Yii::$app->db->createCommand()->delete('in_out_documents')->execute();
    }
    public function actionDeleteFiles()
    {
        Yii::$app->db->createCommand()->delete('files')->execute();
    }
    public function actionDeleteAll(){
        $this->actionDeleteFiles();
        $this->actionDeleteInOutDocuments();
        $this->actionDeleteDocumentIn();
        $this->actionDeleteDocumentOut();
        $this->deletePeopleStamp();
        $this->actionDeleteUser();
        $this->actionDeletePeople();
        $this->actionDeleteCompany();
    }
    public function actionCopyAll(){
        $this->actionCopyCompany();
        $this->actionCopyPeople();
        $this->actionCopyUser();
        $this->peopleTablesCopyController->actionCopyAll();
        $this->actionCopyDocumentIn();
        $this->actionCopyDocumentOut();
        $this->actionCopyInOutDocuments();
    }
}