<?php

namespace frontend\models\work\document_in_out;

use common\components\interfaces\FileInterface;
use common\components\traits\ErrorTrait;
use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use common\models\scaffold\DocumentOut;
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
 * @property PeopleStampWork $executorWork
 * @property PeopleStampWork $signedWork
 */
class DocumentOutWork extends DocumentOut implements FileInterface
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
    public $docFile;
    public $appFile;

    public $isAnswer;
    public $dateAnswer;
    public $nameAnswer;


    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fullNumber' => '№ п/п',
            'documentDate' => 'Дата<br>документа',
            'documentTheme' => 'Тема документа',
            'companyName' => 'Наименование<br>корреспондента',
            'executorName' => 'Исполнитель',
            'sendMethodName' => 'Способ получения',
            'sentDate' => 'Дата<br>отправления',
            'isAnswer' => 'Ответ',
        ]);
    }
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['isAnswer', 'nameAnswer'], 'integer'],
            [['dateAnswer'], 'string'],
            [['scanFile'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, pdf, zip, rar, 7z, tag, txt'],
            [['docFile'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag, txt'],
            [['appFile'], 'file', 'skipOnEmpty' => true,  'maxFiles' => 10,
                'extensions' => 'ppt, pptx, xls, xlsx, pdf, png, jpg, doc, docx, zip, rar, 7z, tag, txt'],
        ]);
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

    public function setValuesForUpdate()
    {
        $this->correspondent_id = $this->correspondentWork->people_id;
        $this->executor_id = $this->executorWork->people_id;
        $this->signed_id = $this->signedWork->people_id;
        $this->document_name = 'NAME';
        $this->setIsAnswer();
    }

    public function getFullName()
    {
        return "Исходящее № {$this->getFullNumber()} от {$this->getDate()} \"{$this->getDocumentTheme()}\" в {$this->getCompanyShortName()}";
    }

    public function getFullNumber()
    {
        if ($this->document_postfix == null) {
            return $this->document_number;
        }
        else {
            return $this->document_number . '/' . $this->document_postfix;
        }
    }

    public function getKeyWords()
    {
        return $this->key_words ? $this->key_words : '---';
    }

    public function getAnswer(){
        return $this->is_answer;
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

    public function getFilesAnswer()
    {
        $repository = Yii::createObject(DocumentOutRepository::class);

        return $repository->getDocumentInWithoutAnswer();
    }

    public function getDate()
    {
        return $this->document_date;
    }

    public function getSentDate()
    {
        return $this->sent_date;
    }

    public function getDocumentTheme()
    {
        return $this->document_theme;
    }
    public function getCompanyName()
    {
        return $this->companyWork->name;
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

    public function getPositionWork()
    {
        return $this->hasOne(PositionWork::class, ['id' => 'position_id']);
    }

    public function getInOutDocumentWork()
    {
        return $this->hasMany(InOutDocumentsWork::class, ['document_out_id' => 'id']);
    }

    public function getCorrespondentWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'correspondent_id']);
    }

    public function setIsAnswer()
    {
        $this->isAnswer = (Yii::createObject(InOutDocumentsRepository::class))->getByDocumentInId($this->id) ? 1 : 0;
    }
    public function getSendMethodName()
    {
        return Yii::$app->sendMethods->get($this->send_method);
    }

    public function getIsAnswer(int $format = StringFormatter::FORMAT_RAW)
    {
        if($this->is_answer != 0){
            $links = (Yii::createObject(InOutDocumentsRepository::class))->getByDocumentOutId($this->id);

            if($links != null) {
                $str = (Yii::createObject(DocumentInRepository::class))->get($links->document_in_id)->getFullName();
                    //'Входящий документ "' . (Yii::createObject(DocumentInRepository::class))->get($links->document_in_id)->document_theme . '"';
                return $format == StringFormatter::FORMAT_LINK ?
                    StringFormatter::stringAsLink($str, Url::to(['document/document-in/view', 'id' => $links->document_in_id])) : $str;
            }
            else {
                return 'Требуется указать ответ';
            }
        }
        return '';
    }
    public function getCompanyWork()
    {
        return $this->hasOne(CompanyWork::class, ['id' => 'company_id']);
    }

    public function getExecutorName()
    {
        $executorName = $this->executorWork;
        return $executorName ? $executorName->getFIO(PeopleWork::FIO_SURNAME_INITIALS) : '---';
    }

    public function getExecutorWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'executor_id']);
    }

    public function getSignedName()
    {
        $signedName = $this->signedWork;
        return $signedName ? $signedName->getFIO(PeopleWork::FIO_SURNAME_INITIALS) : '---';
    }

    public function getSignedWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'signed_id']);
    }

    public function generateDocumentNumber()
    {
        $year = substr(DateFormatter::format($this->document_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash), 0, 4);
        $document_date = DateFormatter::format($this->document_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $docs = DocumentOutWork::find()->all();
        if($docs == NULL){
            $this->document_number = '1';
            $this->document_postfix = 0;
        }
        else {
            $down = Yii::createObject(DocumentOutRepository::class)->findDownNumber($year, $document_date);
            $up = Yii::createObject(DocumentOutRepository::class)->findUpNumber($year, $document_date);
            $down_max = Yii::createObject(DocumentOutRepository::class)->findMaxDownNumber($year, $document_date);
            if($up == null && $down == null) {
                $this->document_number = '1';
                $this->document_postfix = 0;
            }
            if($up == null && $down != null) {
                $this->document_number = $down_max + 1;
                $this->document_postfix = 0;
            }
            if($up != null && $down == null){
                $this->document_number = '0';
                $this->document_postfix = '0';
            }
            if($up != null && $down != null){
                $this->document_number = $down_max ;
                $max_postfix  = Yii::createObject(DocumentOutRepository::class)->findMaxPostfix($year, $this->document_number);
                $this->document_postfix = $max_postfix + 1;
            }
        }
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

    public function beforeValidate()
    {
        $this->document_name = 'NAME';
        $this->is_answer = $this->isAnswer;
        $this->document_date = DateFormatter::format($this->document_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->sent_date = DateFormatter::format($this->sent_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
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
}