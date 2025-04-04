<?php

namespace frontend\services\dictionaries;

use common\helpers\files\filenames\AuditoriumFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\services\DatabaseServiceInterface;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\document_in_out\DocumentInWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class AuditoriumService implements DatabaseServiceInterface
{
    private FileService $fileService;
    private AuditoriumFileNameGenerator $filenameGenerator;

    public function __construct(
        FileService $fileService,
        AuditoriumFileNameGenerator $filenameGenerator
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
    }

    public function getFilesInstances(AuditoriumWork $model)
    {
        $model->filesList = UploadedFile::getInstances($model, 'filesList');
    }

    public function saveFilesFromModel(AuditoriumWork $model)
    {
        for ($i = 1; $i < count($model->filesList) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_OTHER, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->filesList[$i - 1],
                $filename,
                [
                    'tableName' => AuditoriumWork::tableName(),
                    'fileType' => FilesHelper::TYPE_OTHER
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_OTHER,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                get_class($model)
            );
        }
    }

    public function isAvailableDelete($id)
    {
        // TODO: Implement isAvailableDelete() method.
    }

    public function getUploadedFilesTables(AuditoriumWork $model)
    {
        $otherLinks = $model->getFileLinks(FilesHelper::TYPE_OTHER);
        $otherFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($otherLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($otherLinks), $model->id), 'fileId' => ArrayHelper::getColumn($otherLinks, 'id')])
            ]
        );

        return ['other' => $otherFiles];
    }
}