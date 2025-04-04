<?php

namespace frontend\services\educational;

use common\components\logger\base\LogInterface;
use common\components\logger\LogFactory;
use common\components\wizards\ExcelWizard;
use common\helpers\files\filenames\TrainingProgramFileNameGenerator;
use common\helpers\files\FilePaths;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use common\repositories\educational\TrainingProgramRepository;
use common\services\DatabaseServiceInterface;
use common\services\general\files\FileService;
use common\services\general\PeopleStampService;
use DomainException;
use frontend\events\educational\training_program\CreateAuthorProgramEvent;
use frontend\events\educational\training_program\CreateThemeInPlanEvent;
use frontend\events\educational\training_program\ResetThematicPlanEvent;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\educational\training_program\AuthorProgramWork;
use frontend\models\work\educational\training_program\ThematicPlanWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class TrainingProgramService implements DatabaseServiceInterface
{
    private FileService $fileService;
    private PeopleStampService $peopleStampService;
    private TrainingProgramFileNameGenerator $filenameGenerator;
    private TrainingProgramRepository $repository;

    public function __construct(
        FileService $fileService,
        PeopleStampService $peopleStampService,
        TrainingProgramFileNameGenerator $filenameGenerator,
        TrainingProgramRepository $repository
    )
    {
        $this->fileService = $fileService;
        $this->peopleStampService = $peopleStampService;
        $this->repository = $repository;
    }

    public function getFilesInstances(TrainingProgramWork $model)
    {
        $model->mainFile = UploadedFile::getInstance($model, 'mainFile');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
        $model->contractFile = UploadedFile::getInstance($model, 'contractFile');
        $model->utpFile = UploadedFile::getInstance($model, 'utpFile');
    }

    public function saveUtpFromFile(TrainingProgramWork $model)
    {
        if ($model->utpFile !== null) {
            $model->recordEvent(new ResetThematicPlanEvent($model->id), ThematicPlanWork::class);

            $newFilename = StringFormatter::createHash(date("Y-m-d H:i:s")) . '.' . $model->utpFile->extension;
            $this->fileService->uploadFile($model->utpFile, $newFilename, ['filepath' => FilePaths::TEMP_FILEPATH . '/']);
            $data = ExcelWizard::getDataFromColumns(
                Yii::$app->basePath . FilePaths::TEMP_FILEPATH . '/' . $newFilename,
                ['Тема', 'Тип контроля']
            );

            for ($i = 0; $i < count($data['Тема']); $i++) {
                $model->recordEvent(new CreateThemeInPlanEvent($data['Тема'][$i], $model->id, $data['Тип контроля'][$i]), ThematicPlanWork::class);
            }

            $this->fileService->deleteFile(FilePaths::TEMP_FILEPATH . '/' . $newFilename);
        }
    }

    public function saveFilesFromModel(TrainingProgramWork $model)
    {
        if ($model->mainFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_MAIN);

            $this->fileService->uploadFile(
                $model->mainFile,
                $filename,
                [
                    'tableName' => TrainingProgramWork::tableName(),
                    'fileType' => FilesHelper::TYPE_MAIN
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_MAIN,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }

        if ($model->contractFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_CONTRACT);

            $this->fileService->uploadFile(
                $model->contractFile,
                $filename,
                [
                    'tableName' => TrainingProgramWork::tableName(),
                    'fileType' => FilesHelper::TYPE_CONTRACT
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_CONTRACT,
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
                    'tableName' => TrainingProgramWork::tableName(),
                    'fileType' => FilesHelper::TYPE_DOC
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_DOC,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }
    }

    public function getUploadedFilesTables(TrainingProgramWork $model)
    {
        $mainLinks = $model->getFileLinks(FilesHelper::TYPE_MAIN);
        $mainFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($mainLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($mainLinks), $model->id), 'fileId' => ArrayHelper::getColumn($mainLinks, 'id')])
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

        $contractLinks = $model->getFileLinks(FilesHelper::TYPE_CONTRACT);
        $contractFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($contractLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($contractLinks), $model->id), 'fileId' => ArrayHelper::getColumn($contractLinks, 'id')])
            ]
        );

        return ['main' => $mainFiles, 'doc' => $docFiles, 'contract' => $contractFiles];
    }

    public function getDependencyTables($authors, $themes)
    {
        $controlNames = Yii::$app->controlType->getList();
        $modelThematicPlan = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Тема'], ArrayHelper::getColumn($themes, 'theme')),
                array_merge(['Форма контроля'], array_map(function ($number) use ($controlNames) {
                    return $controlNames[$number] ?? null;
                }, ArrayHelper::getColumn($themes, 'control_type'))),
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Редактировать',
                    Url::to('update-theme'),
                    ['id' => ArrayHelper::getColumn($themes, 'id'), 'modelId' => ArrayHelper::getColumn($themes, 'training_program_id')]),
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-theme'),
                    ['id' => ArrayHelper::getColumn($themes, 'id'), 'modelId' => ArrayHelper::getColumn($themes, 'training_program_id')])
            ]
        );

        $nameAuthors = ArrayHelper::getColumn($authors, 'authorWork.peopleWork.firstname');
        $surnameAuthors = ArrayHelper::getColumn($authors, 'authorWork.peopleWork.surname');
        $patronymicAuthors = ArrayHelper::getColumn($authors, 'authorWork.peopleWork.patronymic');
        $modelAuthor = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['ФИО'], array_map(function($nameAuthors, $surnameAuthors, $patronymicAuthors) {
                    return "$nameAuthors $surnameAuthors $patronymicAuthors";
                }, $nameAuthors, $surnameAuthors, $patronymicAuthors))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-author'),
                    ['id' => ArrayHelper::getColumn($authors, 'id'), 'modelId' => ArrayHelper::getColumn($authors, 'training_program_id')])
            ]
        );

        return ['themes' => $modelThematicPlan, 'authors' => $modelAuthor];
    }

    public function isAvailableDelete($id)
    {
        //$authors = $this->repository->checkDeleteAvailable(AuthorProgram::tableName(), TrainingProgram::tableName(), $id);

        //return array_merge($authors);
        return [];
    }

    public function attachUtp(TrainingProgramWork $model, array $themes, array $controls)
    {
        if (!(count($themes) == count($controls))) {
            throw new DomainException('Размеры массивов $themes и $controls не совпадают');
        }

        for ($i = 0; $i < count($themes); $i++) {
            if ($themes[$i] !== "") {
                $model->recordEvent(new CreateThemeInPlanEvent($themes[$i], $model->id, (int)$controls[$i]), ThematicPlanWork::class);
            }
        }
    }

    public function attachAuthors(TrainingProgramWork $model, array $authors)
    {
        $authors = array_unique($authors);
        foreach ($authors as $author) {
            $currentAuthors = array_map(function (AuthorProgramWork $authorProgram) {
                return $authorProgram->authorWork->peopleWork->id;
            }, $this->repository->getAuthors($model->id));
            if (!StringFormatter::isEmpty($author) && !in_array($author, $currentAuthors)) {
                $authorStamp = $this->peopleStampService->createStampFromPeople($author);
                $model->recordEvent(new CreateAuthorProgramEvent($model->id, $authorStamp), AuthorProgramWork::class);
            }
        }
    }

    /**
     * Метод актуализации образовательных программ
     *
     * @param array $actual список id образовательных программ, которые требуется сделать актуальными
     * @param array $unactual список id образовательных программ, которые требуется сделать неактуальными
     * @return void
     */
    public function setProgramRelevance(array $actual, array $unactual)
    {
        foreach ($actual as $actualId) {
            /** @var TrainingProgramWork $program */
            $program = $this->repository->get($actualId);
            $program->setActual(TrainingProgramWork::ACTUAL);
            $this->repository->save($program);
        }

        foreach ($unactual as $unactualId) {
            /** @var TrainingProgramWork $program */
            $program = $this->repository->get($unactualId);
            $program->setActual(TrainingProgramWork::NON_ACTUAL);
            $this->repository->save($program);
        }
    }
}