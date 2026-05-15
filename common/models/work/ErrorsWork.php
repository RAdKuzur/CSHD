<?php

namespace common\models\work;

use common\components\dictionaries\base\ErrorDictionary;
use common\helpers\StringFormatter;
use common\models\Error;
use common\models\scaffold\Errors;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\event\EventRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\order\DocumentOrderRepository;
use frontend\components\routes\Urls;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\event\EventWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\team\ActParticipantWork;
use Yii;
use yii\helpers\Url;
use function PHPUnit\Framework\returnArgument;

class ErrorsWork extends Errors
{
    public static function fill(
        string $error,
        string $tableName,
        int $rowId,
        int $state,
        int $branch = null,
        string $createDatetime = '',
        int $wasAmnesty = 0
    ): ErrorsWork
    {
        if (StringFormatter::isEmpty($createDatetime)) {
            $createDatetime = date('Y-m-d H:i:s');
        }

        $entity = new static();
        $entity->error = $error;
        $entity->table_name = $tableName;
        $entity->table_row_id = $rowId;
        $entity->state = $state;
        $entity->branch = $branch;
        $entity->create_datetime = $createDatetime;
        $entity->was_amnesty= $wasAmnesty;

        return $entity;
    }

    public function setAmnesty()
    {
        $this->was_amnesty = 1;
    }

    public function removeAmnesty()
    {
        $this->was_amnesty = 0;
    }

    /**
     * Возвращает место возникновения ошибки в виде строки
     *
     * @return string
     */
    public function getEntityName() : string
    {
        if ($this->table_name == TrainingGroupWork::tableName()) {
            /** @var TrainingGroupWork $group */
            $group = (Yii::createObject(TrainingGroupRepository::class))->get($this->table_row_id);
            return StringFormatter::stringAsLink($group->number, Url::to(['/' . Urls::TRAINING_GROUP_VIEW, 'id' => $group->id]));
        }

        if ($this->table_name == TrainingProgramWork::tableName()) {
            /** @var TrainingProgramWork $program */
            $program = (Yii::createObject(TrainingProgramRepository::class))->get($this->table_row_id);
            return StringFormatter::stringAsLink($program->name, Url::to(['/' . Urls::TRAINING_PROGRAM_VIEW, 'id' => $program->id]));
        }

        if ($this->table_name == EventWork::tableName()) {
            /** @var EventWork $event */
            $event = (Yii::createObject(EventRepository::class))->get($this->table_row_id);
            return StringFormatter::stringAsLink($event->name, Url::to(['/' . Urls::OUR_EVENT_VIEW, 'id' => $event->id]));
        }

        if ($this->table_name == ForeignEventWork::tableName()) {
            /** @var ForeignEventWork $event */
            $event = (Yii::createObject(ForeignEventRepository::class))->get($this->table_row_id);
            return StringFormatter::stringAsLink($event->name, Url::to(['/' . Urls::FOREIGN_EVENT_VIEW, 'id' => $event->id]));
        }

        if ($this->table_name == ActParticipantWork::tableName()) {
            /** @var ActParticipantWork $act */
            $act = (Yii::createObject(ActParticipantRepository::class))->get($this->table_row_id);
            $eventLink = StringFormatter::stringAsLink($act->foreignEventWork->name, Url::to(['/' . Urls::FOREIGN_EVENT_VIEW, 'id' => $act->foreign_event_id]));
            return "Акт участия в мероприятии {$eventLink}";
        }
        if ($this->table_name == ForeignEventParticipantsWork::tableName()) {
            $participant = (Yii::createObject(ForeignEventParticipantsRepository::class))
                ->get($this->table_row_id);

            if ($participant === null) {
                return "!!!Участник не найден!!!";
            }

            $link = StringFormatter::stringAsLink(
                $participant->getFIO(PersonInterface::FIO_SURNAME_INITIALS),
                Url::to(['/' . Urls::PARTICIPANT_VIEW , 'id' => $participant->id])
            );

            return "Участник деятельности {$link}";
        }

        if ($this->table_name == DocumentOrderWork::tableName()) {
            /** @var DocumentOrderWork $order */
            $order = (Yii::createObject(DocumentOrderRepository::class))->get($this->table_row_id);
            $url = '/' . Urls::ORDER_MAIN_VIEW;
            if ($order->isTraining()) {
                $url = '/' . Urls::ORDER_TRAINING_VIEW;
            }
            if ($order->isEvent()) {
                $url = '/' . Urls::ORDER_EVENT_VIEW;
            }
            return StringFormatter::stringAsLink($order->getOrderName(), Url::to([$url, 'id' => $order->id]));
        }

        return '';
    }

    public function getEntityNameForExport()
    {

        if ($this->table_name == TrainingGroupWork::tableName()) {
            
            $group = (Yii::createObject(TrainingGroupRepository::class))->get($this->table_row_id);
            if ($group === null) {
                return "!!!Группа не найдена!!!";
            }
            return $this->formatExcelHyperlink(
                $group->number,
                Url::to(['/' . Urls::TRAINING_GROUP_VIEW, 'id' => $group->id], true)
            );
        }

        if ($this->table_name == TrainingProgramWork::tableName()) {

            $program = (Yii::createObject(TrainingProgramRepository::class))->get($this->table_row_id);
            if ($program === null) {
                return "!!!Программа не найдена!!!";
            }
            return $this->formatExcelHyperlink(
                $program->name,
                Url::to(['/' . Urls::TRAINING_PROGRAM_VIEW, 'id' => $program->id], true)
            );
        }

        if ($this->table_name == EventWork::tableName()) {

            $event = (Yii::createObject(EventRepository::class))->get($this->table_row_id);
            if ($event === null) {
                return "!!!Мероприятие не найдено!!!";
            }
            return $this->formatExcelHyperlink(
                $event->name,
                Url::to(['/' . Urls::OUR_EVENT_VIEW, 'id' => $event->id], true)
            );
        }

        if ($this->table_name == ForeignEventWork::tableName()) {

            $event = (Yii::createObject(ForeignEventRepository::class))->get($this->table_row_id);
            if ($event === null) {
                return "!!!Мероприятие не найдено!!!";
            }
            return $this->formatExcelHyperlink(
                $event->name,
                Url::to(['/' . Urls::FOREIGN_EVENT_VIEW, 'id' => $event->id], true)
            );
        }

        if ($this->table_name == ActParticipantWork::tableName()) {

            $act = (Yii::createObject(ActParticipantRepository::class))->get($this->table_row_id);
            if ($act === null) {
                return "!!!Акт не найден!!!";
            }
            if ($act->foreignEventWork === null) {
                return "!!!Мероприятие акта не найдено!!!";
            }
            $text = "Акт участия в мероприятии " . $act->foreignEventWork->name;
            return $this->formatExcelHyperlink(
                $text,
                Url::to(['/' . Urls::FOREIGN_EVENT_VIEW, 'id' => $act->foreign_event_id], true)
            );
        }

        if ($this->table_name == ForeignEventParticipantsWork::tableName()) {

            $participant = (Yii::createObject(ForeignEventParticipantsRepository::class))->get($this->table_row_id);
            if ($participant === null) {
                return "!!!Участник не найден!!!";
            }
            $text = "Участник деятельности " . $participant->getFIO(PersonInterface::FIO_SURNAME_INITIALS);
            return $this->formatExcelHyperlink(
                $text,
                Url::to(['/' . Urls::PARTICIPANT_VIEW, 'id' => $participant->id], true)
            );
        }

        if ($this->table_name == DocumentOrderWork::tableName()) {

            $order = (Yii::createObject(DocumentOrderRepository::class))->get($this->table_row_id);
            if ($order === null) {
                return "!!!Приказ не найден!!!";
            }
            $url = '/' . Urls::ORDER_MAIN_VIEW;
            if ($order->isTraining()) {
                $url = '/' . Urls::ORDER_TRAINING_VIEW;
            }
            if ($order->isEvent()) {
                $url = '/' . Urls::ORDER_EVENT_VIEW;
            }
            return $this->formatExcelHyperlink(
                $order->getOrderName(),
                Url::to([$url, 'id' => $order->id], true)
            );
        }

        return '-';
    }

    /**
     * Вспомогательный метод — формирует корректную формулу Excel
     */
    private function formatExcelHyperlink(string $text, string $url): string
    {
        if (empty($url) || empty($text)) {
            return $text;
        }

        $textEsc = str_replace('"', '""', $text);
        $urlEsc  = str_replace('"', '""', $url);

        return '=HYPERLINK("' . $urlEsc . '", "' . $textEsc . '")';
    }

    public function setState(int $state = Error::TYPE_BASE)
    {
        $this->state = $state;
    }

    public function isCrititcal()
    {
        return $this->state == Error::TYPE_CRITICAL;
    }
}