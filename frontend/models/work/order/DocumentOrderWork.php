<?php

namespace frontend\models\work\order;

use common\components\interfaces\FileInterface;
use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\DocumentOrder;
use common\models\work\UserWork;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\general\OrderPeopleWork;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;
use Yii;
use yii\helpers\Url;

/* @property PeopleStampWork $bringWork */
/* @property PeopleStampWork $executorWork */
/* @property UserWork $creatorWork */
/* @property UserWork $lastUpdateWork */
/* @property ExpireWork[] $expireWorks */
/* @property OrderPeopleWork[] $orderPeopleWorks */

class DocumentOrderWork extends DocumentOrder
{
    use EventTrait;
    public const ORDER_INIT = 0;
    public const ORDER_MAIN = 1;
    public const ORDER_EVENT = 2;
    public const ORDER_TRAINING = 3;
    public const ERROR_DATE_PARTICIPANT = 1;
    public const ERROR_RELATION = 2;
    /**
     * Переменные для input-file в форме
     */
    public $scanFile;
    public $docFiles;
    public $appFiles;

    public $scanExist;
    public $docExist;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['order_date', 'order_name', 'executor_id', 'bring_id'], 'required'],
            [['scanFile'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, pdf, zip, rar, 7z, tag, txt'],
            [['docFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag, txt'],
            [['appFiles'], 'file', 'skipOnEmpty' => true,  'maxFiles' => 10,
                'extensions' => 'ppt, pptx, xls, xlsx, pdf, png, jpg, doc, docx, zip, rar, 7z, tag, txt'],
        ]);
    }

    public function getFullOrderName(){
        return $this->order_number . ' ' . $this->order_postfix . ' ' . $this->order_name;
    }

    public function getFullNumber()
    {
        if ($this->order_postfix == null) {
            return $this->order_number;
        }
        else {
            return $this->order_number.'/'.$this->order_postfix;
        }
    }

    public function getFullName()
    {
        $result = $this->getFullNumber();
        return "$result {$this->order_name}";
    }

    public function getOrderDate()
    {
        return $this->order_date;
    }

    public function getNumberPostfix()
    {
        if ($this->order_postfix == null) {
            return $this->order_number;
        }
        else {
            return $this->order_number.'/'.$this->order_postfix;
        }
    }

    public function getOrderName()
    {
        return $this->order_name;
    }

    public function getBringName()
    {
        $model = PeopleWork::findOne($this->bring_id);
        if($model != NULL) {
            return $model->getFullFio();
        }
        else {
            return $this->bring_id;
        }
    }

    public function getExecutorName()
    {
        $model = PeopleStampWork::findOne($this->executor_id);
        if($model != NULL) {
            return $model->getFullFio();
        }
        else {
            return $this->bring_id;
        }
    }

    public function getFileLinks($filetype) : array
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }
        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_SCAN:
                $addPath = FilesHelper::createAdditionalPath(DocumentOrderWork::tableName(), FilesHelper::TYPE_SCAN);
                break;
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath(DocumentOrderWork::tableName(), FilesHelper::TYPE_DOC);
                break;
        }
        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }

    public function setValuesForUpdate()
    {
        $this->bring_id = $this->bring->people_id;
        $this->executor_id = $this->executor->people_id;
        $this->signed_id = $this->signed->people_id;
    }

    public function isMain()
    {
        return $this->type == DocumentOrderWork::ORDER_MAIN;
    }

    public function isTraining()
    {
        return $this->type == DocumentOrderWork::ORDER_TRAINING;
    }

    public function isEvent()
    {
        return $this->type == DocumentOrderWork::ORDER_EVENT;
    }

    public function beforeSave($insert)
    {
        if(!(Yii::$app instanceof yii\console\Application)) {
            if ($this->creator_id == null) {
                $this->creator_id = Yii::$app->user->identity->getId();
            }
            $this->last_edit_id = Yii::$app->user->identity->getId();
        }

        return parent::beforeSave($insert);
    }

    public function getBringWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'bring_id']);
    }

    public function getExecutorWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'executor_id']);
    }

    public function getCreatorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'creator_id']);
    }

    public function getLastUpdateWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'last_edit_id']);
    }

    public function getExpireWorks()
    {
        return $this->hasMany(ExpireWork::class, ['active_regulation_id' => 'id']);
    }

    public function checkFilesExist()
    {
        $this->scanExist = count($this->getFileLinks(FilesHelper::TYPE_SCAN)) > 0;
        $this->docExist = count($this->getFileLinks(FilesHelper::TYPE_DOC)) > 0;
    }

    public function getOrderPeopleWorks()
    {
        return $this->hasMany(OrderPeopleWork::class, ['order_id' => 'id']);
    }

    public function getPrettyResponsibles()
    {
        $result = [];
        foreach ($this->orderPeopleWorks as $orderPeopleWork) {
            $result[] = HtmlBuilder::createSubtitleAndClarification(
                $orderPeopleWork->peopleStampWork->peopleWork->getFIO(PersonInterface::FIO_FULL),
                ''
            );
        }

        return HtmlBuilder::arrayToAccordion($result);
    }

    public function getKeyWords()
    {
        return $this->key_words;
    }

    public function getFullDoc()
    {
        $link = '#';
        if ($this->docExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_DOC, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }

    public function getFullScan()
    {
        $link = '#';
        if ($this->scanExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_SCAN, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }
}