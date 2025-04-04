<?php

namespace frontend\forms\analytics;

use common\models\work\ErrorsWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\event\EventWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\order\DocumentOrderWork;
use yii\base\BaseObject;

/**
 * @property ErrorsWork[] $groupErrors
 * @property ErrorsWork[] $programErrors
 * @property ErrorsWork[] $orderErrors
 * @property ErrorsWork[] $eventErrors
 * @property ErrorsWork[] $foreignEventErrors
 */
class AnalyticErrorForm extends BaseObject
{
    /**
     * @var ErrorsWork[] $_groupErrors
     * @var ErrorsWork[] $_programErrors
     * @var ErrorsWork[] $_orderErrors
     * @var ErrorsWork[] $_eventErrors
     * @var ErrorsWork[] $_foreignEventErrors
     */
    private array $_groupErrors;
    private array $_programErrors;
    private array $_orderErrors;
    private array $_eventErrors;
    private array $_foreignEventErrors;

    public function __construct(
        array $errors,
        $config = [])
    {
        parent::__construct($config);
        //Ошибки в учебных группах
        $this->_groupErrors = array_filter(
            $errors, function (ErrorsWork $value) {
            return $value->table_name === TrainingGroupWork::tableName();
        });

        //Ошибки в образовательных программах
        $this->_programErrors = array_filter(
            $errors, function (ErrorsWork $value) {
            return $value->table_name === TrainingProgramWork::tableName();
        });

        //Ошибки в приказах
        $this->_orderErrors = array_filter(
            $errors, function (ErrorsWork $value) {
            return $value->table_name === DocumentOrderWork::tableName();
        });

        //Ошибки в мероприятиях
        $this->_eventErrors = array_filter(
            $errors, function (ErrorsWork $value) {
            return $value->table_name === EventWork::tableName();
        });

        //Ошибки в учете достижений
        $this->_foreignEventErrors = array_filter(
            $errors, function (ErrorsWork $value) {
            return $value->table_name === ForeignEventWork::tableName();
        });
    }

    public function getGroupErrors() : array
    {
        return $this->_groupErrors;
    }

    public function getProgramErrors() : array
    {
        return $this->_programErrors;
    }

    public function getOrderErrors() : array
    {
        return $this->_orderErrors;
    }

    public function getEventErrors() : array
    {
        return $this->_eventErrors;
    }

    public function getForeignEventErrors() : array
    {
        return $this->_foreignEventErrors;
    }

    public function setGroupErrors(array $errors)
    {
        $this->_groupErrors = $errors;
    }

    public function setProgramErrors(array $errors)
    {
        $this->_programErrors = $errors;
    }

    public function setOrderErrors(array $errors)
    {
        $this->_orderErrors = $errors;
    }

    public function setEventErrors(array $errors)
    {
        $this->_eventErrors = $errors;
    }

    public function setForeignEventErrors(array $errors)
    {
        $this->_foreignEventErrors = $errors;
    }
}