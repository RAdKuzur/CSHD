<?php

namespace frontend\models\work\document_in_out;

use common\components\interfaces\FileInterface;
use common\components\traits\ErrorTrait;
use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use common\models\scaffold\DocumentIn;
use common\models\work\UserWork;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\document_in_out\DocumentOutRepository;
use common\repositories\document_in_out\InOutDocumentsRepository;
use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;
use Yii;
use yii\helpers\Url;

/**
 * @property PeopleStampWork $correspondentWork
 * @property PositionWork $positionWork
 * @property CompanyWork $companyWork
 * @property InOutDocumentsWork $inOutDocumentWork
 * @property UserWork $creatorWork
 * @property UserWork $lastEditorWork
 */
class DocumentInWork extends DocumentIn implements FileInterface
{
    use EventTrait, ErrorTrait;

    /**
     * Имена файлов для сохранения в БД
     */
    public $scanExist;
    public $docExist;
    public $appExist;

    /**
     * Переменные для input-file в форме
     */
    public $scanFile;
    public $docFiles;
    public $appFiles;

    public $needAnswer;
    public $dateAnswer;
    public $nameAnswer;

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fullNumber' => '№ п/п',
            'localDate' => 'Дата документа',
            'realDate' => 'Дата входящего документа',
            'realNumber' => 'Рег. номер входящего док.',
            'companyName' => 'Наименование корреспондента',
            'documentTheme' => 'Тема документа',
            'sendMethodName' => 'Способ получения',
            'needAnswer' => 'Ответ',
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['needAnswer', 'nameAnswer'], 'integer'],
            [['dateAnswer'], 'string'],
            [['scanFile'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, pdf, zip, rar, 7z, tag, txt'],
            [['docFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag, txt'],
            [['appFiles'], 'file', 'skipOnEmpty' => true,  'maxFiles' => 10,
                'extensions' => 'ppt, pptx, xls, xlsx, pdf, png, jpg, doc, docx, zip, rar, 7z, tag, txt'],
        ]);
    }

    public static function fill($localNumber = 0, $creatorId = null, $app = '', $doc = '', $scan = '')
    {
        $entity = new static();
        $entity->creator_id = $creatorId ?: Yii::$app->user->identity->getId();
        $entity->local_number = $localNumber;
        $entity->app = $app;
        $entity->doc = $doc;
        $entity->scan = $scan;
    }

    public function checkFilesExist()
    {
        $this->scanExist = count($this->getFileLinks(FilesHelper::TYPE_SCAN)) > 0;
        $this->docExist = count($this->getFileLinks(FilesHelper::TYPE_DOC)) > 0;
        $this->appExist = count($this->getFileLinks(FilesHelper::TYPE_APP)) > 0;
    }

    public function getFullScan()
    {
        $link = '#';
        if ($this->scanExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_SCAN, 'id' => $this->id]);
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

    public function getFullApp()
    {
        $link = '#';
        if ($this->appExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_APP, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }

    public function getFullName()
    {
        return "Входящее № {$this->getFullNumber()} от {$this->getLocalDate()} из {$this->getCompanyShortName()} \"{$this->getDocumentTheme()}\"";
    }

    public function getFullNumber()
    {
        if ($this->local_postfix == null)
            return $this->local_number;
        else
            return $this->local_number.'/'.$this->local_postfix;
    }

    public function getCompanyName()
    {
        $company = $this->companyWork;
        return $company ? $company->getName() : '---';
    }

    public function getCompanyShortName()
    {
        $company = $this->companyWork;
        return $company ? $company->getShortName() : '---';
    }

    public function getCorrespondentName()
    {
        $correspondent = $this->correspondentWork;
        return $correspondent ? $correspondent->getFIO(PeopleWork::FIO_SURNAME_INITIALS_WITH_POSITION) : '---';
    }

    public function getSendMethodName()
    {
        return Yii::$app->sendMethods->get($this->send_method);
    }

    public function getKeyWords()
    {
        return $this->key_words ? $this->key_words : '---';
    }

    public function getRealDate()
    {
        return $this->real_date;
    }

    public function getLocalDate()
    {
        return $this->local_date;
    }

    public function getRealNumber()
    {
        return $this->real_number ? $this->real_number : '---';
    }

    public function getDocumentTheme()
    {
        return $this->document_theme;
    }

    public function getResponsibleName()
    {
        if ($this->getNeedAnswer() && $responsible = $this->inOutDocumentWork->responsibleWork) {
            return $responsible->getSurnameInitials();
        }

        return '---';
    }

    public function getResponsibleDate()
    {
        if ($this->getNeedAnswer() && $date = $this->inOutDocumentWork) {
            return $date->getDate();
        }

        return '---';
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

    private function createAddPaths($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_SCAN:
                $addPath = FilesHelper::createAdditionalPath(DocumentInWork::tableName(), FilesHelper::TYPE_SCAN);
                break;
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath(DocumentInWork::tableName(), FilesHelper::TYPE_DOC);
                break;
            case FilesHelper::TYPE_APP:
                $addPath = FilesHelper::createAdditionalPath(DocumentInWork::tableName(), FilesHelper::TYPE_APP);
                break;
        }

        return $addPath;
    }

    /**
     * Возвращает массив
     * link => форматированная ссылка на документ
     * id => ID записи в таблице files
     * @param $filetype
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getFileLinks($filetype) : array
    {
        return FilesHelper::createFileLinks($this, $filetype, $this->createAddPaths($filetype));
    }

    public function getFilePaths($filetype): array
    {
        return FilesHelper::createFilePaths($this, $filetype, $this->createAddPaths($filetype));
    }

    public function getAnswerNotEmpty()
    {
        if ($this->getNeedAnswer()) {
            return !$this->inOutDocumentWork->getIsEmptyDocumentOut();
        }

        return false;
    }

    public function getAnswer(int $format = StringFormatter::FORMAT_RAW)
    {
        $inOutDoc = Yii::createObject(InOutDocumentsRepository::class)->getByDocumentInId($this->id);
        $docOut = Yii::createObject(DocumentOutRepository::class)->get($inOutDoc->document_out_id);
        $answerName = "Исходящее № {$docOut->getFullNumber()} от {$docOut->getDate()} \"{$docOut->getDocumentTheme()}\"";

        return StringFormatter::stringAsLink($answerName, Url::to([Yii::$app->frontUrls::DOC_IN_VIEW, 'id' => $inOutDoc->document_out_id]));
    }

    /**
     * Возвращает строку с отображением необходимости ответа на входящий документ
     * @param int $format формат возвращаемого значения (при наличии такой опции) @see StringFormatter
     * @return string
     */
    public function getNeedAnswerString(int $format = StringFormatter::FORMAT_RAW)
    {
        if($this->need_answer != 0){
            $links = (Yii::createObject(InOutDocumentsRepository::class))->getByDocumentInId($this->id);

            if($links->document_out_id != null) {
                $str = 'Исходящий документ "' . (Yii::createObject(DocumentOutRepository::class))->get($links->document_out_id)->document_theme . '"';
                return $format == StringFormatter::FORMAT_LINK ?
                    StringFormatter::stringAsLink($str, Url::to([Yii::$app->frontUrls::DOC_IN_VIEW, 'id' => $links->document_out_id])) : $str;
            }
            else {
                return $links->date ? 'Требуется указать ответ до ' . DateFormatter::format($links->date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot) : 'Требуется указать ответ';
            }
        }
        return '';
    }

    public function generateDocumentNumber()
    {
        $year = substr(DateFormatter::format($this->local_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash), 0, 4);
        $local_date = DateFormatter::format($this->local_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $docs = Yii::createObject(DocumentInRepository::class)->getAll();
        if($docs == NULL){
            $this->local_number = '1';
            $this->local_postfix = 0;
        }
        else {
            $down = Yii::createObject(DocumentInRepository::class)->findDownNumber($year, $local_date);
            $up = Yii::createObject(DocumentInRepository::class)->findUpNumber($year, $local_date);
            $down_max = Yii::createObject(DocumentInRepository::class)->findMaxDownNumber($year, $local_date);
            if($up == null && $down == null) {
                $this->local_number = '1';
                $this->local_postfix = 0;
            }
            if($up == null && $down != null) {
                $this->local_number = $down_max + 1;
                $this->local_postfix = 0;
            }
            if($up != null && $down == null){
                $this->local_number = '0';
                $this->local_postfix = '0';
            }
            if($up != null && $down != null){
                $this->local_number = $down_max ;
                $max_postfix  = Yii::createObject(DocumentInRepository::class)->findMaxPostfix($year, $this->local_number);
                $this->local_postfix = $max_postfix + 1;
            }
        }
    }

    public function beforeValidate()
    {
        $this->need_answer = $this->needAnswer;
        $this->local_date = DateFormatter::format($this->local_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->real_date = DateFormatter::format($this->real_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->dateAnswer = DateFormatter::format($this->dateAnswer, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); 
    }

    public function beforeSave($insert)
    {
        if ($this->creator_id == null) {
            $this->creator_id = Yii::$app->user->identity->getId();
        }
        $this->last_edit_id = Yii::$app->user->identity->getId();

        return parent::beforeSave($insert); 
    }

    // --relationships--
    public function getCompanyWork()
    {
        return $this->hasOne(CompanyWork::class, ['id' => 'company_id']);
    }

    public function getPositionWork()
    {
        return $this->hasOne(PositionWork::class, ['id' => 'position_id']);
    }

    public function getInOutDocumentWork()
    {
        return $this->hasOne(InOutDocumentsWork::class, ['document_in_id' => 'id']);
    }

    public function getCorrespondentWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'correspondent_id']);
    }

    public function getCreatorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'creator_id']);
    }

    public function getLastEditorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'last_edit_id']);
    }

    public function getNeedAnswer()
    {
        return $this->need_answer;
    }

    public function isNeedAnswer()
    {
        return $this->getNeedAnswer() == 1;
    }

    public function setNeedAnswer()
    {
        $this->needAnswer = (Yii::createObject(InOutDocumentsRepository::class))->getByDocumentInId($this->id) ? 1 : 0;
    }

    public function setValuesForUpdate()
    {
        $this->correspondent_id = $this->correspondentWork->people_id;
        $this->setNeedAnswer();
        if ($this->isNeedAnswer()) {
            $this->nameAnswer = $this->inOutDocumentWork->responsibleWork->people_id;
            $this->dateAnswer = $this->inOutDocumentWork->date;
        }
    }
}