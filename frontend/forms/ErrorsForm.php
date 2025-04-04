<?php

namespace frontend\forms;

use common\models\work\ErrorsWork;

class ErrorsForm
{
    public $errors;
    public $errorsGroup;
    public $errorsProgram;
    public $errorsOrder;
    public $errorsEvent;
    public $errorsAchievement;
    public $errorsTreat;
    public function __construct($errors)
    {
        $this->errors = $errors;
        $this->errorsGroup = array_filter(
            $errors, function (ErrorsWork $value) {
            return $value->table_name === 'training_group';
        });
        $this->errorsProgram = array_filter(
            $errors, function (ErrorsWork $value) {
            return $value->table_name === 'training_program';
        });

        $this->errorsOrder = array_filter(
            $errors, function (ErrorsWork $value) {
            return $value->table_name === 'document_order';
        });
        $this->errorsEvent = array_filter(
            $errors, function (ErrorsWork $value) {
            return $value->table_name === 'event';
        });
        $this->errorsAchievement = array_filter(
            $errors, function (ErrorsWork $value) {
            return $value->table_name === 'foreign_event';
        });
        $this->errorsTreat = NULL;
    }
}