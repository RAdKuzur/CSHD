<?php

namespace frontend\models\work\responsibility;

use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use common\models\scaffold\LocalResponsibility;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\regulation\RegulationWork;
use InvalidArgumentException;
use Yii;

/**
 * @property PeopleStampWork $peopleStampWork
 * @property AuditoriumWork $auditoriumWork
 * @property RegulationWork $regulationWork
 */

class LocalResponsibilityWork extends LocalResponsibility
{
    use EventTrait;

    public $filesList;

    public function rules()
    {
        return [
            [['filesList'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10]
        ];
    }

    public function loadField($responsibilityType, $branch, $auditoriumId, $quant, $peopleStampId, $regulationId, $filesList)
    {
        $this->responsibility_type = $responsibilityType;
        $this->branch = $branch;
        $this->auditorium_id = $auditoriumId;
        $this->quant = $quant;
        $this->people_stamp_id = $peopleStampId;
        $this->regulation_id = $regulationId;
        $this->filesList = $filesList;
    }

    public static function fill($responsibilityType, $branch, $auditoriumId, $quant, $peopleStampId, $regulationId, $filesList)
    {
        $entity = new static();
        $entity->responsibility_type = $responsibilityType;
        $entity->branch = $branch;
        $entity->auditorium_id = $auditoriumId;
        $entity->quant = $quant;
        $entity->people_stamp_id = $peopleStampId;
        $entity->regulation_id = $regulationId;
        $entity->filesList = $filesList;

        return $entity;
    }

    /**
     * Возвращает массив
     * link => форматированная ссылка на документ
     * id => ID записи в таблице files
     * @param $filetype
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_OTHER:
                $addPath = FilesHelper::createAdditionalPath(LocalResponsibilityWork::tableName(), FilesHelper::TYPE_OTHER);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }

    public function getPeopleStampWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'people_stamp_id']);
    }

    public function getAuditoriumWork()
    {
        return $this->hasOne(AuditoriumWork::class, ['id' => 'auditorium_id']);
    }

    public function getRegulationWork()
    {
        return $this->hasOne(RegulationWork::class, ['id' => 'regulation_id']);
    }

    public function beforeSave($insert)
    {
        if ($this->creator_id == null) {
            $this->creator_id = Yii::$app->user->identity->getId();
        }
        $this->last_edit_id = Yii::$app->user->identity->getId();

        return parent::beforeSave($insert); 
    }
}