<?php

namespace frontend\services\event;

use common\helpers\files\filenames\EventNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\services\DatabaseServiceInterface;
use common\services\general\files\FileService;
use common\services\general\PeopleStampService;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\document_in_out\DocumentOutWork;
use frontend\models\work\event\EventWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class EventService implements DatabaseServiceInterface
{
    private FileService $fileService;
    private PeopleStampService $peopleStampService;
    private EventNameGenerator $filenameGenerator;

    public function __construct(
        FileService $fileService,
        PeopleStampService $peopleStampService,
        EventNameGenerator $filenameGenerator
    )
    {
        $this->fileService = $fileService;
        $this->peopleStampService = $peopleStampService;
        $this->filenameGenerator = $filenameGenerator;
    }

    public function getFilesInstances(EventWork $model)
    {
        $model->protocolFiles = UploadedFile::getInstances($model, 'protocolFiles');
        $model->reportingFiles = UploadedFile::getInstances($model, 'reportingFiles');
        $model->photoFiles = UploadedFile::getInstances($model, 'photoFiles');
        $model->otherFiles = UploadedFile::getInstances($model, 'otherFiles');
    }

    public function saveFilesFromModel(EventWork $model)
    {
        for ($i = 1; $i < count($model->protocolFiles) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_PROTOCOL, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->protocolFiles[$i - 1],
                $filename,
                [
                    'tableName' => EventWork::tableName(),
                    'fileType' => FilesHelper::TYPE_PROTOCOL
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_PROTOCOL,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                get_class($model)
            );
        }

        for ($i = 1; $i < count($model->reportingFiles) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_REPORT, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->reportingFiles[$i - 1],
                $filename,
                [
                    'tableName' => EventWork::tableName(),
                    'fileType' => FilesHelper::TYPE_REPORT
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_REPORT,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                get_class($model)
            );
        }

        for ($i = 1; $i < count($model->photoFiles) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_PHOTO, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->photoFiles[$i - 1],
                $filename,
                [
                    'tableName' => EventWork::tableName(),
                    'fileType' => FilesHelper::TYPE_PHOTO
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_PHOTO,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                get_class($model)
            );
        }

        for ($i = 1; $i < count($model->otherFiles) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_OTHER, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->otherFiles[$i - 1],
                $filename,
                [
                    'tableName' => EventWork::tableName(),
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

    public function getUploadedFilesTables(EventWork $model)
    {
        $photoLinks = $model->getFileLinks(FilesHelper::TYPE_PHOTO);
        $photoFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($photoLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($photoLinks), $model->id), 'fileId' => ArrayHelper::getColumn($photoLinks, 'id')])
            ]
        );

        $protocolLinks = $model->getFileLinks(FilesHelper::TYPE_PROTOCOL);
        $protocolFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($protocolLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($protocolLinks), $model->id), 'fileId' => ArrayHelper::getColumn($protocolLinks, 'id')])
            ]
        );

        $reportingLinks = $model->getFileLinks(FilesHelper::TYPE_REPORT);
        $reportingFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($reportingLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($reportingLinks), $model->id), 'fileId' => ArrayHelper::getColumn($reportingLinks, 'id')])
            ]
        );

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

        return ['protocol' => $protocolFiles, 'photo' => $photoFiles, 'report' => $reportingFiles, 'other' => $otherFiles];
    }

    public function isAvailableDelete($id)
    {
        return [];
    }

    public function getPeopleStamps(EventWork $model)
    {
        if ($model->responsible1_id !== "") {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->responsible1_id);
            $model->responsible1_id = $peopleStampId;
        }

        if ($model->responsible2_id !== "") {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->responsible2_id);
            $model->responsible2_id = $peopleStampId;
        }
    }
}