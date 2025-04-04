<?php

namespace frontend\services\regulation;

use common\helpers\files\filenames\RegulationFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\services\DatabaseServiceInterface;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\regulation\RegulationWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class RegulationService implements DatabaseServiceInterface
{
    private FileService $fileService;
    private RegulationFileNameGenerator $filenameGenerator;

    public function __construct(
        FileService $fileService,
        RegulationFileNameGenerator $filenameGenerator
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
    }

    public function getFilesInstances(RegulationWork $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
    }

    public function saveFilesFromModel(RegulationWork $model)
    {
        if ($model->scanFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);

            $this->fileService->uploadFile(
                $model->scanFile,
                $filename,
                [
                    'tableName' => RegulationWork::tableName(),
                    'fileType' => FilesHelper::TYPE_SCAN
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_SCAN,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }
    }

    public function isAvailableDelete($id)
    {
        return [];
    }

    public function getUploadedFilesTables(RegulationWork $model)
    {
        $scanLinks = $model->getFileLinks(FilesHelper::TYPE_SCAN);
        $scanFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($scanLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($scanLinks), $model->id), 'fileId' => ArrayHelper::getColumn($scanLinks, 'id')])
            ]
        );

        return ['scan' => $scanFiles];
    }
}