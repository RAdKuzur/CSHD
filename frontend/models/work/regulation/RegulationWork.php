<?php

namespace frontend\models\work\regulation;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\Regulation;
use common\models\work\UserWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderMainWork;
use InvalidArgumentException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * @property UserWork $creatorWork
 * @property UserWork $lastEditorWork
 * @property OrderMainWork $orderWork
 */

class RegulationWork extends Regulation
{
    use EventTrait;
    const STATE_ACTIVE = 1;
    const STATE_EXPIRE = 0;

    public $expires; //документ, отменяющий текущее положение
    public $scanExist;  // существование файлов

    /**
     * Переменные для input-file в форме
     */
    public $scanFile;

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function() {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['date' , 'name'], 'required'],
            [['scanFile'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, pdf, zip, rar, 7z, tag, txt'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'date' => 'Дата положения',
            'name' => 'Наименование',
            'short_name' => 'Короткое наименование',
            'orderName' => 'Приказ',
            'state' => 'Состояние',
            'ped_council_number' => '№ пед.совета',
            'ped_council_date' => 'Дата пед.совета',
            'par_council_number' => '№ совета род.',
            'par_council_date' => 'Дата совета род.',
        ]);
    }

    public function getShortName()
    {
        return $this->short_name;
    }

    public function getName()
    {
        return $this->name;
    }

    public static function states()
    {
        return [
            self::STATE_ACTIVE => 'Актуально',
            self::STATE_EXPIRE => 'Утратило силу',
        ];
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getPedCouncilNumber()
    {
        return $this->ped_council_number;
    }

    public function getPedCouncilDate()
    {
        return $this->ped_council_date;
    }

    public function getParCouncilDate()
    {
        return $this->par_council_date;
    }

    public function getStates()
    {
        $statuses = self::states();
        if (!array_key_exists($this->state, $statuses)) {
            throw new InvalidArgumentException('Неизвестный статус положения');
        }

        return $statuses[$this->state];
    }

    public function checkFilesExist()
    {
        $this->scanExist = count($this->getFileLinks(FilesHelper::TYPE_SCAN)) > 0;
    }

    public function getFullScan()
    {
        $link = '#';
        if ($this->scanExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_SCAN, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }

    public function getOrderName()
    {
        $order = $this->orderWork;
        return $order ? $order->getFullName() : '---';
    }

    public function getCreatorName()
    {
        $creator = $this->creatorWork;
        return $creator ? $creator->getFullName() : '---';
    }

    public function getLastEditorName()
    {
        $editor = $this->lastEditorWork;
        return $editor ? $editor->getFullName() : '---';
    }

    public function getCreatorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'creator_id']);
    }

    public function getLastEditorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'last_edit_id']);
    }

    public function getDocumentOrderWork()
    {
        return $this->hasOne(DocumentOrderWork::class, ['id' => 'order_id']);
    }

    public function getOrderWork()
    {
        return $this->hasOne(OrderMainWork::class, ['id' => 'order_id']);
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
        return FilesHelper::createFileLinks($this, $filetype, $this->createAddPaths($filetype));
    }

    private function createAddPaths($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_SCAN:
                $addPath = FilesHelper::createAdditionalPath(RegulationWork::tableName(), FilesHelper::TYPE_SCAN);
                break;
        }

        return $addPath;
    }

    public function beforeSave($insert)
    {
        if ($this->creator_id == null) {
            $this->creator_id = Yii::$app->user->identity->getId();
        }
        $this->last_edit_id = Yii::$app->user->identity->getId();

        return parent::beforeSave($insert); 
    }

    // ТОЛЬКО для предварительной обработки полей. Остальные действия - через Event
    public function beforeValidate()
    {
        $this->state = RegulationWork::STATE_ACTIVE;
        $this->date = DateFormatter::format($this->date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->ped_council_date = DateFormatter::format($this->ped_council_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->par_council_date = DateFormatter::format($this->par_council_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); 
    }
}
