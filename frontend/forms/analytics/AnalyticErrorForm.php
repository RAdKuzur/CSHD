<?php


namespace frontend\forms\analytics;

use common\models\work\ErrorsWork;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
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
 * @property ErrorsWork[] $foreignEventParticipantsErrors
 */
class AnalyticErrorForm extends BaseObject
{
    /**
     * @var ErrorsWork[] $_groupErrors
     * @var ErrorsWork[] $_programErrors
     * @var ErrorsWork[] $_orderErrors
     * @var ErrorsWork[] $_eventErrors
     * @var ErrorsWork[] $_foreignEventErrors
     * @var ErrorsWork[] $_foreignEventParticipantsErrors
     *
     */
    private array $_groupErrors = [];
    private array $_programErrors = [];
    private array $_orderErrors= [];
    private array $_eventErrors = [];
    private array $_foreignEventErrors = [];
    private array $_foreignEventParticipantsErrors = [];

    

    public function __construct(
        array $errors,
        $config = [])
    {
        parent::__construct($config);
        foreach ($errors as $error) {
            /** @var ErrorsWork $error */
            switch ($error->table_name) {
                //Ошибки в учебных группах
                case TrainingGroupWork::tableName():
                    $this->_groupErrors[] = $error;
                    break;
                //Ошибки в образовательных программах
                case TrainingProgramWork::tableName():
                    $this->_programErrors[]= $error;
                    break;
                //Ошибки в приказах
                case DocumentOrderWork::tableName():
                    $this->_orderErrors[] = $error;
                    break;
                //Ошибки в мероприятиях
                case EventWork::tableName():
                    $this->_eventErrors[] = $error;
                    break;
                //Ошибки в учете достижений
                case ForeignEventWork::tableName():
                    $this->_foreignEventErrors[] = $error;
                    break;
                case ForeignEventParticipantsWork::tableName():
                    $this->_foreignEventParticipantsErrors[] = $error;
            }
        }
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

    public function getForeignEventParticipantsErrors() : array
    {
        return $this->_foreignEventParticipantsErrors;
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

    public function setForeignEventParticipantsErrors(array $errors)
    {
        $this->_foreignEventParticipantsErrors = $errors;
    }
}