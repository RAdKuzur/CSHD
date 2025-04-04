<?php

namespace frontend\services\document;

use common\helpers\files\filenames\DocumentInFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\services\DatabaseServiceInterface;
use common\services\general\files\FileService;
use common\services\general\PeopleStampService;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\document_in_out\DocumentInWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class DocumentInService implements DatabaseServiceInterface
{
    private FileService $fileService;
    private PeopleStampService $peopleStampService;
    private DocumentInFileNameGenerator $filenameGenerator;

    public function __construct(
        FileService $fileService,
        PeopleStampService $peopleStampService,
        DocumentInFileNameGenerator $filenameGenerator
    )
    {
        $this->fileService = $fileService;
        $this->peopleStampService = $peopleStampService;
        $this->filenameGenerator = $filenameGenerator;
    }

    public function getFilesInstances(DocumentInWork $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->appFiles = UploadedFile::getInstances($model, 'appFiles');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
    }

    public function saveFilesFromModel(DocumentInWork $model)
    {
        if ($model->scanFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);

            $this->fileService->uploadFile(
                $model->scanFile,
                $filename,
                [
                    'tableName' => DocumentInWork::tableName(),
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

        for ($i = 1; $i < count($model->docFiles) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_DOC, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->docFiles[$i - 1],
                $filename,
                [
                    'tableName' => DocumentInWork::tableName(),
                    'fileType' => FilesHelper::TYPE_DOC
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_DOC,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                get_class($model)
            );
        }

        for ($i = 1; $i < count($model->appFiles) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_APP, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->appFiles[$i - 1],
                $filename,
                [
                    'tableName' => DocumentInWork::tableName(),
                    'fileType' => FilesHelper::TYPE_APP
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_APP,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                get_class($model)
            );
        }
    }

    public function getUploadedFilesTables(DocumentInWork $model)
    {
        $scanLinks = $model->getFileLinks(FilesHelper::TYPE_SCAN);
        $scanFile = HtmlBuilder::createTableWithActionButtons(
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

        $docLinks = $model->getFileLinks(FilesHelper::TYPE_DOC);
        $docFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($docLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($docLinks), $model->id), 'fileId' => ArrayHelper::getColumn($docLinks, 'id')])
            ]
        );

        $appLinks = $model->getFileLinks(FilesHelper::TYPE_APP);
        $appFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($appLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($appLinks), $model->id), 'fileId' => ArrayHelper::getColumn($appLinks, 'id')])
            ]
        );

        return ['scan' => $scanFile, 'doc' => $docFiles, 'app' => $appFiles];
    }

    public function isAvailableDelete($id)
    {
        return [];
    }

    public function getPeopleStamps(DocumentInWork $model)
    {
        if ($model->correspondent_id != "") {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->correspondent_id);
            $model->correspondent_id = $peopleStampId;
        }

        if ($model->nameAnswer !== '' && $model->nameAnswer !== NULL) {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->nameAnswer);
            $model->nameAnswer = $peopleStampId;
        }
    }
}