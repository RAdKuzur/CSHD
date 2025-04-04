<?php

namespace frontend\services\event;

use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\team\ActParticipantWork;
use common\helpers\DateFormatter;
use common\helpers\files\filenames\ForeignEventFileNameGenerator;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\ActParticipant;
use common\repositories\act_participant\ActParticipantRepository;
use common\services\general\files\FileService;
use frontend\events\foreign_event\ParticipantAchievementEvent;
use frontend\events\general\FileCreateEvent;
use frontend\forms\event\EventParticipantForm;
use frontend\forms\event\ForeignEventForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class ForeignEventService
{
    private FileService $fileService;
    private ForeignEventFileNameGenerator $filenameGenerator;
    private ActParticipantRepository $actParticipantRepository;
    public function __construct(
        FileService $fileService,
        ForeignEventFileNameGenerator $filenameGenerator,
        ActParticipantRepository $actParticipantRepository
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
        $this->actParticipantRepository = $actParticipantRepository;
    }

    public function saveAchievementFileFromModel(ForeignEventForm $model)
    {
        if ($model->doc !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_DOC);

            $this->fileService->uploadFile(
                $model->doc,
                $filename,
                [
                    'tableName' => ForeignEventWork::tableName(),
                    'fileType' => FilesHelper::TYPE_DOC
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    ForeignEventWork::tableName(),
                    $model->id,
                    FilesHelper::TYPE_DOC,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }
    }

    public function saveParticipantFileFromModel(EventParticipantForm $model)
    {
        if ($model->fileMaterial !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_MATERIAL);

            $this->fileService->uploadFile(
                $model->fileMaterial,
                $filename,
                [
                    'tableName' => ActParticipantWork::tableName(),
                    'fileType' => FilesHelper::TYPE_MATERIAL
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    ActParticipantWork::tableName(),
                    $model->actParticipant->id,
                    FilesHelper::TYPE_MATERIAL,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model->actParticipant)
            );
        }
    }

    public function saveActFilesFromModel(ForeignEventWork $model , $actFiles , $number)
    {
        if ($actFiles != NULL) {
            for ($i = 1; $i < count($actFiles) + 1; $i++) {
                $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_PARTICIPATION, ['counter' => $i, 'number' => $number]);
                $this->fileService->uploadFile(
                    $actFiles[$i - 1],
                    $filename,
                    [
                        'tableName' => ForeignEventWork::tableName(),
                        'fileType' => FilesHelper::TYPE_PARTICIPATION
                    ]
                );
                $model->recordEvent(
                    new FileCreateEvent(
                        $model::tableName(),
                        $model->id,
                        FilesHelper::TYPE_PARTICIPATION,
                        $filename,
                        FilesHelper::LOAD_TYPE_SINGLE
                    ),
                    get_class($model)
                );
            }
        }
    }

    public function attachAchievement(ForeignEventForm $form)
    {
        foreach ($form->newAchievements as $participantAchievement) {
            $participantAchievement->date = DateFormatter::format($participantAchievement->date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
            $form->recordEvent(
                new ParticipantAchievementEvent($participantAchievement),
                get_class($participantAchievement)
            );
        }
    }

    public function getFilesInstances(ForeignEventForm $form)
    {
        $form->doc = UploadedFile::getInstance($form, 'doc');
    }

    public function getParticipantFilesInstances(EventParticipantForm $form)
    {
        $form->fileMaterial = UploadedFile::getInstance($form, 'fileMaterial');
    }
}