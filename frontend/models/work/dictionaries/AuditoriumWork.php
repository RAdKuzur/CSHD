<?php

namespace frontend\models\work\dictionaries;

use common\components\interfaces\FileInterface;
use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\Auditorium;
use InvalidArgumentException;
use Yii;
use yii\helpers\Url;

class AuditoriumWork extends Auditorium implements FileInterface
{
    use EventTrait;

    const NO_EDUCATION = 0;
    const IS_EDUCATION = 1;

    const NO_INCLUDE = 0;
    const IS_INCLUDE = 1;

    public $filesList;

    public $otherExist;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['filesList'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ]);
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
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_OTHER:
                $addPath = FilesHelper::createAdditionalPath(AuditoriumWork::tableName(), FilesHelper::TYPE_OTHER);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }

    public function checkFilesExist()
    {
        $this->otherExist = count($this->getFileLinks(FilesHelper::TYPE_OTHER)) > 0;
    }

    /**
     * Вывод номера и названия аудитории
     * @return string
     */
    public function getFullName()
    {
        return "$this->name ($this->text)";
    }

    public function isEducation()
    {
        return $this->is_education;
    }

    public function isIncludeSquare()
    {
        return $this->include_square;
    }

    public function getEducationPretty()
    {
        return $this->isEducation() ? 'Да' : 'Нет';
    }

    public function getIncludeSquarePretty()
    {
        return $this->isIncludeSquare() ? 'Да' : 'Нет';
    }

    public function getAuditoriumTypePretty()
    {
        return Yii::$app->auditoriumType->get($this->auditorium_type);
    }

    public function getFilePaths($filetype): array
    {
        return FilesHelper::createFilePaths($this, $filetype, $this->createAddPaths($filetype));
    }

    private function createAddPaths($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_OTHER:
                $addPath = FilesHelper::createAdditionalPath(AuditoriumWork::tableName(), FilesHelper::TYPE_OTHER);
                break;
        }

        return $addPath;
    }

    public function getFullOther()
    {
        $link = '#';
        if ($this->otherExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_OTHER, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }
}