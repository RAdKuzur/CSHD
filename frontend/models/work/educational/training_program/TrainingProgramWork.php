<?php

namespace frontend\models\work\educational\training_program;

use common\components\dictionaries\base\CertificateTypeDictionary;
use common\components\traits\ErrorTrait;
use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\html\HtmlCreator;
use common\helpers\StringFormatter;
use common\models\scaffold\TrainingProgram;
use common\models\work\UserWork;
use common\repositories\educational\TrainingProgramRepository;
use common\services\general\files\FileService;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/** @property AuthorProgramWork $authorsProgramWork */
/** @property BranchProgramWork[] $branchProgramWork */

class TrainingProgramWork extends TrainingProgram
{
    use EventTrait, ErrorTrait;

    public $mainFile;
    public $docFiles;
    public $contractFile;
    public $utpFile;

    public $branches;

    public $themes;
    public $controls;
    public $authors;

    public $mainExist;
    public $docExist;
    public $contractExist;
    const LEVEL_LIST = [1, 2, 3];
    const ACTUAL = 1;
    const NON_ACTUAL = 0;

    private FileService $fileService;
    private TrainingProgramRepository $repository;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->init();
        $this->fileService = Yii::createObject(FileService::class);
        $this->repository = Yii::createObject(TrainingProgramRepository::class);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
                [['name', 'capacity', 'hour_capacity'], 'required'],
                [['branches'], 'safe'],
                [['mainFile'], 'file', 'skipOnEmpty' => true,
                    'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag'],
                [['docFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                    'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag'],
                [['contractFile'], 'file', 'skipOnEmpty' => true,
                    'extensions' => 'ppt, pptx, xls, xlsx, pdf, png, jpg, doc, docx, zip, rar, 7z, tag, txt'],
                [['utpFile'], 'file', 'extensions' => 'xls, xlsx', 'skipOnEmpty' => true],
            ]
        );
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => 'Название',
            'namePretty' => 'Название',
            'thematic_direction' => 'Тематическое направление',
            'level' => 'Уровень сложности',
            'levelNumber' => 'Уровень<br>сложности',
            'branch' => 'Место реализации',
            'branchString' => 'Место<br>реализации',
            'authorString' => 'Составители',
            'agePeriod' => 'Возрастные<br>ограничения',
            'ped_council_date' => 'Дата педагогического совета',
            'ped_council_number' => 'Номер протокола педагогического совета',
            'capacity' => 'Объем, ак. час.',
            'hour_capacity' => 'Длительность 1 академического часа в минутах',
            'student_left_age' => 'Мин. возраст учащихся, лет',
            'student_right_age' => 'Макс. возраст учащихся, лет',
            'focus' => 'Направленность',
            'focusString' => 'Направленность',
            'allow_remote' => 'Форма реализации',
            'actual' => 'Образовательная программа актуальна',
            'certificate_type' => 'Итоговая форма контроля',
            'description' => 'Описание',
            'key_words' => 'Ключевые слова',
            'is_network' => 'Сетевая форма обучения',
            'mainFile' => 'Документ программы',
            'docFiles' => 'Редактируемые документы',
            'contractFile' => 'Договор о сетевой форме обучения',
            'utpFile' => 'Файл УТП',
        ]);
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

    public function beforeSave($insert)
    {
        if ($this->creator_id == null) {
            $this->creator_id = Yii::$app->user->identity->getId();
        }
        $this->last_edit_id = Yii::$app->user->identity->getId();
        return parent::beforeSave($insert);
    }

    public function beforeValidate()
    {
        $this->ped_council_date = DateFormatter::format($this->ped_council_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate();
    }

    public function getFullDirectionName()
    {
        return Yii::$app->thematicDirection->getFullnameList()[$this->thematic_direction];
    }

    public function getActual()
    {
        return $this->isActual() ? 'Да' : 'Нет';
    }

    public function isActual()
    {
        return $this->actual == self::ACTUAL;
    }

    /**
     * Иконка не акутального статуса
     * @return string
     */
    public function getRawActual()
    {
        if (!$this->isActual()) {
            return HtmlCreator::archiveTooltip();
        }
        return '';
    }

    /**
     * Отображает название и иконки архива (если программа в архиве)
     * @return string
     */
    public function getNamePretty()
    {
        return '<div class=flexx>' . $this->name . ' ' . $this->getRawActual() . '</div>';
    }

    public function getLevelNumber()
    {
        return $this->level+1;
    }

    public function getAgePeriod()
    {
        return $this->student_left_age . ' - ' . $this->student_right_age . ' лет';
    }

    public function getCapacityAndHour()
    {
        return $this->capacity . ' ак. час. по ' . $this->hour_capacity . ' мин.';
    }

    public function getFocusString()
    {
        return Yii::$app->focus->get($this->focus);
    }

    public function getAllowRemote()
    {
        return Yii::$app->allowRemote->get($this->allow_remote);
    }

    public function getIsNetwork()
    {
        return $this->is_network == 0 ? 'Нет' : 'Да';
    }

    public function getBranchString()
    {
        $branchesPrograms = $this->repository->getBranches($this->id);
        $result = '';

        foreach ($branchesPrograms as $branches)
        {
            $result .= Yii::$app->branches->get($branches->branch) . ', ';
        }
        return substr($result, 0, -2);
    }

    public function getCertificateType()
    {
        return Yii::$app->certificateType->get($this->certificate_type);
    }

    public function isProjectCertificate()
    {
        return $this->certificate_type === CertificateTypeDictionary::PROJECT_PITCH;
    }

    public function isControlWorkCertificate()
    {
        return $this->certificate_type === CertificateTypeDictionary::CONTROL_WORK;
    }

    public function isOtherCertificate()
    {
        return $this->certificate_type === CertificateTypeDictionary::OTHER_CONTROL;
    }

    public function isOpenLessonCertificate()
    {
        return $this->certificate_type === CertificateTypeDictionary::OPEN_LESSON;
    }

    public function getThematicPlaneRaw()
    {
        $thematicPlaneArr = $this->repository->getThematicPlan($this->id);
        $thematicPlaneStr = '<ol>';

        foreach ($thematicPlaneArr as $oneTheme)
        {
            $thematicPlaneStr .= '<li>' . $oneTheme->theme . ' (' . Yii::$app->controlType->get($oneTheme->control_type) . ')</li>';
        }
        $thematicPlaneStr .= '</ol>';

        return HtmlBuilder::createAccordion($thematicPlaneStr);
    }

    public function getDescription()
    {
        return HtmlBuilder::createAccordion($this->description);
    }

    public function getPedCouncilDate()
    {
        return DateFormatter::format($this->ped_council_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
    }

    public function getPedCouncilNumber()
    {
        return $this->ped_council_number;
    }

    public function getAuthorString(int $formatter = null)
    {
        $authors = $this->repository->getAuthors($this->id);
        $result = '';

        foreach ($authors as $author)
        {
            if ($formatter == StringFormatter::FORMAT_LINK)
            {
                $result .= StringFormatter::stringAsLink(
                        $author->authorWork->peopleWork->getFio(PeopleWork::FIO_SURNAME_INITIALS),
                        Url::to([Yii::$app->frontUrls::PEOPLE_VIEW, 'id' => $author->authorWork->peopleWork->id])) . '<br>';
            }
            else {
                $result .= $author->authorWork->peopleWork->getFio(PeopleWork::FIO_SURNAME_INITIALS) . '<br>';
            }
        }
        return substr($result, 0, -4);
    }

    public function getTrainingProgramRaw()
    {
        $result = '';
        $trGroups = $this->repository->getTrainingGroups($this->id);

        foreach ($trGroups as $trGroup)
        {
            $result .= StringFormatter::stringAsLink(
                    $trGroup->getNumber(),
                    Url::to([Yii::$app->frontUrls::TRAINING_GROUP_VIEW, 'id' => $trGroup->id])) . ', ';
        }
        return substr($result, 0, -2);
    }

    public function getKeyWords()
    {
        return $this->key_words;
    }

    public function checkFilesExist()
    {
        $this->mainExist = count($this->getFileLinks(FilesHelper::TYPE_MAIN)) > 0;
        $this->docExist = count($this->getFileLinks(FilesHelper::TYPE_DOC)) > 0;
        $this->contractExist = count($this->getFileLinks(FilesHelper::TYPE_CONTRACT)) > 0;
    }

    public function getFullMainFiles()
    {
        $link = '#';
        if ($this->mainExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_MAIN, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }

    public function getFullContract()
    {
        $link = '#';
        if ($this->contractExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_CONTRACT, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }

    public function getFullDoc()
    {
        $link = '#';
        if ($this->docExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_DOC, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
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
            case FilesHelper::TYPE_MAIN:
                $addPath = FilesHelper::createAdditionalPath(TrainingProgramWork::tableName(), FilesHelper::TYPE_MAIN);
                break;
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath(TrainingProgramWork::tableName(), FilesHelper::TYPE_DOC);
                break;
            case FilesHelper::TYPE_CONTRACT:
                $addPath = FilesHelper::createAdditionalPath(TrainingProgramWork::tableName(), FilesHelper::TYPE_CONTRACT);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
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

    public function setBranches()
    {
        if ($this->id) {
            $this->branches = ArrayHelper::getColumn(
                $this->repository->getBranches($this->id),
                'branch'
            );
        }
    }

    public function setActual(int $actual)
    {
        $this->actual = $actual;
    }

    public function getCreatorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'creator_id']);
    }

    public function getLastEditorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'last_edit_id']);
    }

    public function getAuthorsProgramWork()
    {
        return $this->hasMany(AuthorProgramWork::class, ['training_program_id' => 'id']);
    }

    public function getBranchProgramWork()
    {
        return $this->hasMany(BranchProgramWork::class, ['training_program_id' => 'id']);
    }
}