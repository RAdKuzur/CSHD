<?php

namespace console\controllers\copy;

use Yii;
use yii\console\Controller;

class InitCopyController extends Controller
{
    public function actionCopyBotMessage(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM bot_message");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('bot_message', $record);
            $command->execute();
        }
    }
    public function actionCopyCertificateTemplates(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM certificat_templates");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('certificate_templates', $record);
            $command->execute();
        }
    }
    public function actionCopyCharacteristicObject(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM characteristic_object");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('characteristic_object', $record);
            $command->execute();
        }
    }
    public function actionCopyComplex(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM complex");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('complex', $record);
            $command->execute();
        }
    }
    public function actionCopyEntry(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM entry");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('entry', $record);
            $command->execute();
        }
    }
    public function actionCopyErrors(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM errors");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('errors', ['id' => $record['id'], 'number' => $record['number'], 'description' => $record['name']]);
            $command->execute();
        }
    }
    public function actionCopyForeignEventParticipants(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM foreign_event_participants");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('foreign_event_participants',
            [
                'id' => $record['id'],
                'firstname' => $record['firstname'],
                'surname' => $record['secondname'],
                'patronymic' => $record['patronymic'],
                'birthdate' => $record['birthdate'],
                'sex' => $record['sex'],
                'is_true' => $record['is_true'],
                'email' => $record['email'],
                'guaranteed_true' => $record['guaranted_true'],
            ]
            );
            $command->execute();
        }
    }
    public function actionCopyPatchnotes(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM patchnotes");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('patchnotes', $record);
            $command->execute();
        }
    }
    public function actionCopyPosition(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM position");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('position', $record);
            $command->execute();
        }
    }
    public function actionCopyProductUnion(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM product_union");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('product_union', $record);
            $command->execute();
        }
    }
    public function actionCopyProjectTheme(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM project_theme");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('project_theme', $record);
            $command->execute();
        }
    }
    public function actionCopyRussianNames()
    {
        //в миграции
        $query = Yii::$app->old_db->createCommand("SELECT * FROM russian_names");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('russian_names', $record);
            $command->execute();
        }
    }
    public function actionDeleteBotMessage(){
        Yii::$app->db->createCommand()->delete('bot_message')->execute();
    }
    public function actionDeleteCertificateTemplates(){
        Yii::$app->db->createCommand()->delete('certificate_templates')->execute();
    }
    public function actionDeleteCharacteristicObject(){
        Yii::$app->db->createCommand()->delete('characteristic_object')->execute();
    }
    public function actionDeleteComplex(){
        Yii::$app->db->createCommand()->delete('complex')->execute();
    }
    public function actionDeleteEntry(){
        Yii::$app->db->createCommand()->delete('entry')->execute();
    }
    public function actionDeleteErrors(){
        Yii::$app->db->createCommand()->delete('errors')->execute();
    }
    public function actionDeleteForeignEventParticipants(){
        Yii::$app->db->createCommand()->delete('foreign_event_participants')->execute();
    }
    public function actionDeletePatchnotes(){
        Yii::$app->db->createCommand()->delete('patchnotes')->execute();
    }
    public function actionDeletePosition(){
        Yii::$app->db->createCommand()->delete('position')->execute();
    }
    public function actionDeleteProductUnion(){
        Yii::$app->db->createCommand()->delete('product_union')->execute();
    }
    public function actionDeleteProjectTheme(){
        Yii::$app->db->createCommand()->delete('project_theme')->execute();
    }
    public function actionDeleteRussianNames(){
        Yii::$app->db->createCommand()->delete('russian_names')->execute();
    }
    public function actionDeleteAll()
    {
        $this->actionDeleteBotMessage();
        $this->actionDeleteCertificateTemplates();
        $this->actionDeleteCharacteristicObject();
        $this->actionDeleteComplex();
        $this->actionDeleteEntry();
        //$this->actionDeleteErrors();
        $this->actionDeleteForeignEventParticipants();
        $this->actionDeletePatchnotes();
        $this->actionDeletePosition();
        $this->actionDeleteProductUnion();
        $this->actionDeleteProjectTheme();
        $this->actionDeleteRussianNames();
    }
    public function actionCopyAll(){
        $this->actionCopyBotMessage();
        $this->actionCopyCertificateTemplates();
        $this->actionCopyCharacteristicObject();
        $this->actionCopyComplex();
        $this->actionCopyEntry();
        //$this->actionCopyErrors();
        $this->actionCopyForeignEventParticipants();
        $this->actionCopyPatchnotes();
        $this->actionCopyPosition();
        $this->actionCopyProductUnion();
        $this->actionCopyProjectTheme();
        //$this->actionCopyRussianNames();
    }
}