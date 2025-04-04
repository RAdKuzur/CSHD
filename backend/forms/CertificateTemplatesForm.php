<?php

namespace backend\forms;

use common\helpers\common\ImageHelper;
use common\helpers\files\FilePaths;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use common\Model;
use common\repositories\educational\CertificateTemplatesRepository;
use common\services\general\files\FileService;
use frontend\models\work\CertificateTemplatesWork;
use Yii;
use yii\helpers\Url;

class CertificateTemplatesForm extends Model
{
    const STANDARD_TEMPLATE_HEIGHT = 795;
    const STANDARD_TEMPLATE_WIDTH = 1125;

    public $name;
    public $templateFile;

    public CertificateTemplatesWork $entity;

    public function __construct(
        $id = -1,
        $config = []
    )
    {
        parent::__construct($config);

        $this->entity = new CertificateTemplatesWork();
        if ($id !== -1) {
            $this->entity = (Yii::createObject(CertificateTemplatesRepository::class))->get($id);
            $this->name = $this->entity->name;
        }
    }

    public function rules()
    {
        return [
            [['name'], 'string'],
            [['templateFile'], 'file', 'extensions' => 'jpg, png, pdf, jpeg', 'skipOnEmpty' => false],
        ];
    }

    public function fillEntity()
    {
        $this->entity->name = $this->name;
        if (!$this->entity->path) {
            $this->entity->path = 'frontend' . FilePaths::CERTIFICATE_TEMPLATES;
        }
    }

    public function uploadTemplateFile()
    {
        $filename = 'Шаблон "'.$this->entity->name.'"';
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res) . '.' . $this->templateFile->extension;
        $loadedPath = '\\..\\' . $this->entity->path;
        $this->entity->path .= $res;

        if (!ImageHelper::checkImgSize(
            $this->templateFile,
            self::STANDARD_TEMPLATE_WIDTH,
            self::STANDARD_TEMPLATE_HEIGHT
        )) {
            $file = ImageHelper::resizeImg(
                $this->templateFile,
                self::STANDARD_TEMPLATE_WIDTH,
                self::STANDARD_TEMPLATE_HEIGHT,
            );
            (Yii::createObject(FileService::class))->uploadFileFromImagine($file, $loadedPath, $res);
        }
        else {
            (Yii::createObject(FileService::class))->uploadFile($this->templateFile, $res, ['filepath' => $loadedPath]);
        }
    }

    public function getFullScan()
    {
        $link = '#';
        if ($this->templateFile) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_SCAN, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }
}