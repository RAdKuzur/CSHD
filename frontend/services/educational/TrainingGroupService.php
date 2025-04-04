<?php

namespace frontend\services\educational;

use common\components\compare\GroupExpertCompare;
use common\components\compare\GroupThemeCompare;
use common\components\compare\LessonGroupCompare;
use common\components\compare\ParticipantGroupCompare;
use common\components\compare\TeacherGroupCompare;
use common\components\traits\CommonDatabaseFunctions;
use common\components\traits\Math;
use common\helpers\DateFormatter;
use common\helpers\files\filenames\TrainingGroupFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\PeopleStamp;
use common\models\scaffold\ThematicPlan;
use common\repositories\dictionaries\AuditoriumRepository;
use common\repositories\educational\LessonThemeRepository;
use common\repositories\educational\ProjectThemeRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\educational\VisitRepository;
use common\services\DatabaseServiceInterface;
use common\services\general\errors\ErrorService;
use common\services\general\files\FileService;
use common\services\general\PeopleStampService;
use DateTime;
use frontend\events\educational\training_group\AddGroupExpertEvent;
use frontend\events\educational\training_group\AddGroupThemeEvent;
use frontend\events\educational\training_group\CreateLessonGroupEvent;
use frontend\events\educational\training_group\CreateTeacherGroupEvent;
use frontend\events\educational\training_group\CreateTrainingGroupLessonEvent;
use frontend\events\educational\training_group\CreateTrainingGroupParticipantEvent;
use frontend\events\educational\training_group\DeleteGroupExpertEvent;
use frontend\events\educational\training_group\DeleteGroupThemeEvent;
use frontend\events\educational\training_group\DeleteLessonGroupEvent;
use frontend\events\educational\training_group\DeleteTeacherGroupEvent;
use frontend\events\educational\training_group\DeleteTrainingGroupParticipantEvent;
use frontend\events\educational\training_group\DeleteVisitEvent;
use frontend\events\educational\training_group\UpdateGroupExpertEvent;
use frontend\events\educational\training_group\UpdateProjectThemeEvent;
use frontend\events\educational\training_group\UpdateTrainingGroupParticipantEvent;
use frontend\events\general\FileCreateEvent;
use frontend\events\visit\AddLessonToVisitEvent;
use frontend\forms\training_group\PitchGroupForm;
use frontend\forms\training_group\TrainingGroupBaseForm;
use frontend\forms\training_group\TrainingGroupParticipantForm;
use frontend\forms\training_group\TrainingGroupScheduleForm;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\LessonThemeWork;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\ThematicPlanWork;
use frontend\models\work\ProjectThemeWork;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

class TrainingGroupService implements DatabaseServiceInterface
{
    use CommonDatabaseFunctions, Math;

    private TrainingGroupRepository $trainingGroupRepository;
    private TeacherGroupRepository $teacherGroupRepository;
    private TrainingGroupLessonRepository $trainingGroupLessonRepository;
    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    private ProjectThemeRepository $themeRepository;
    private TrainingProgramRepository $trainingProgramRepository;
    private VisitRepository $visitRepository;
    private LessonThemeRepository $lessonThemeRepository;
    private FileService $fileService;
    private TrainingGroupFileNameGenerator $filenameGenerator;
    private PeopleStampService $peopleStampService;
    private JournalService $journalService;
    private ErrorService $errorService;

    public function __construct(
        TrainingGroupRepository $trainingGroupRepository,
        TeacherGroupRepository $teacherGroupRepository,
        TrainingGroupLessonRepository $trainingGroupLessonRepository,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        ProjectThemeRepository $themeRepository,
        TrainingProgramRepository $trainingProgramRepository,
        VisitRepository $visitRepository,
        LessonThemeRepository $lessonThemeRepository,
        FileService $fileService,
        TrainingGroupFileNameGenerator $filenameGenerator,
        PeopleStampService $peopleStampService,
        JournalService $journalService,
        ErrorService $errorService
    )
    {
        $this->trainingGroupRepository = $trainingGroupRepository;
        $this->teacherGroupRepository = $teacherGroupRepository;
        $this->trainingGroupLessonRepository = $trainingGroupLessonRepository;
        $this->trainingGroupParticipantRepository = $trainingGroupParticipantRepository;
        $this->themeRepository = $themeRepository;
        $this->trainingProgramRepository = $trainingProgramRepository;
        $this->visitRepository = $visitRepository;
        $this->lessonThemeRepository = $lessonThemeRepository;
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
        $this->peopleStampService = $peopleStampService;
        $this->journalService = $journalService;
        $this->errorService = $errorService;
    }

    public function convertBaseFormToModel(TrainingGroupBaseForm $form)
    {
        if ($form->id !== null) {
            $entity = $this->trainingGroupRepository->get($form->id);
        }
        else {
            $entity = new TrainingGroupWork();
        }
        $entity->branch = $form->branch;
        $entity->training_program_id = $form->trainingProgramId;
        $entity->budget = $form->budget;
        $entity->is_network = $form->network;
        $entity->start_date = $form->startDate;
        $entity->finish_date = $form->endDate;
        $entity->order_stop = $form->endLoadOrders;

        return $entity;
    }

    public function getFilesInstances(TrainingGroupBaseForm $form)
    {
        $form->photos = UploadedFile::getInstances($form, 'photos');
        $form->presentations = UploadedFile::getInstances($form, 'presentations');
        $form->workMaterials = UploadedFile::getInstances($form, 'workMaterials');
    }

    public function saveFilesFromModel(TrainingGroupBaseForm $form)
    {
        for ($i = 1; $i < count($form->photos) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($form, FilesHelper::TYPE_PHOTO, ['counter' => $i]);

            $this->fileService->uploadFile(
                $form->photos[$i - 1],
                $filename,
                [
                    'tableName' => TrainingGroupWork::tableName(),
                    'fileType' => FilesHelper::TYPE_PHOTO
                ]
            );

            $form->recordEvent(
                new FileCreateEvent(
                    TrainingGroupWork::tableName(),
                    $form->id,
                    FilesHelper::TYPE_PHOTO,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                TrainingGroupWork::tableName()
            );
        }

        for ($i = 1; $i < count($form->presentations) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($form, FilesHelper::TYPE_PRESENTATION, ['counter' => $i]);

            $this->fileService->uploadFile(
                $form->presentations[$i - 1],
                $filename,
                [
                    'tableName' => TrainingGroupWork::tableName(),
                    'fileType' => FilesHelper::TYPE_PRESENTATION
                ]
            );

            $form->recordEvent(
                new FileCreateEvent(
                    TrainingGroupWork::tableName(),
                    $form->id,
                    FilesHelper::TYPE_PRESENTATION,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                TrainingGroupWork::tableName()
            );
        }

        for ($i = 1; $i < count($form->workMaterials) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($form, FilesHelper::TYPE_WORK, ['counter' => $i]);

            $this->fileService->uploadFile(
                $form->workMaterials[$i - 1],
                $filename,
                [
                    'tableName' => TrainingGroupWork::tableName(),
                    'fileType' => FilesHelper::TYPE_WORK
                ]
            );

            $form->recordEvent(
                new FileCreateEvent(
                    TrainingGroupWork::tableName(),
                    $form->id,
                    FilesHelper::TYPE_WORK,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                TrainingGroupWork::tableName()
            );
        }
    }

    public function getUploadedFilesTables(TrainingGroupBaseForm $form)
    {
        if ($form->id == null) {
            return [
                'photos' => '',
                'presentations' => '',
                'workMaterials' => '',
            ];
        }
        $model = $this->trainingGroupRepository->get($form->id);
        /** @var TrainingGroupWork $otherLinks */
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

        $presentationLinks = $model->getFileLinks(FilesHelper::TYPE_PRESENTATION);
        $presentationFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($presentationLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($presentationLinks), $model->id), 'fileId' => ArrayHelper::getColumn($presentationLinks, 'id')])
            ]
        );

        $workMaterialsLinks = $model->getFileLinks(FilesHelper::TYPE_WORK);
        $workMaterialsFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($workMaterialsLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($workMaterialsLinks), $model->id), 'fileId' => ArrayHelper::getColumn($workMaterialsLinks, 'id')])
            ]
        );

        return [
            'photos' => $photoFiles,
            'presentations' => $presentationFiles,
            'workMaterials' => $workMaterialsFiles
        ];
    }

    public function isAvailableDelete($id)
    {
        /*$docsIn = $this->documentInRepository->checkDeleteAvailable(DocumentIn::tableName(), Company::tableName(), $entityId);
        $docsOut = $this->documentOutRepository->checkDeleteAvailable(DocumentOut::tableName(), Company::tableName(), $entityId);
        $people = $this->peoplePositionCompanyBranchRepository->checkDeleteAvailable(PeoplePositionCompanyBranch::tableName(), Company::tableName(), $entityId);
        $peopleStamp = $this->peopleStampRepository->checkDeleteAvailable(PeopleStamp::tableName(), Company::tableName(), $entityId);

        return array_merge($docsIn, $docsOut, $people, $peopleStamp);*/
        return [];
    }

    public function attachTeachers(TrainingGroupBaseForm $form, array $modelTeachers)
    {
        $newTeachers = [];
        foreach ($modelTeachers as $teacher) {
            /** @var PeopleStamp $teacherStamp */
            /** @var TeacherGroupWork $teacher */
            if ($teacher->peopleId) {
                $teacherStamp = $this->peopleStampService->createStampFromPeople($teacher->peopleId);
                $newTeachers[] = TeacherGroupWork::fill($teacherStamp, $form->id);
            }

        }
        $newTeachers = array_unique($newTeachers);

        $addTeachers = $this->setDifference($newTeachers, $form->prevTeachers, TeacherGroupCompare::class);
        $delTeachers = $this->setDifference($form->prevTeachers, $newTeachers, TeacherGroupCompare::class);

        foreach ($addTeachers as $teacher) {
            $form->recordEvent(new CreateTeacherGroupEvent($form->id, $teacher->teacher_id), TrainingGroupWork::className());
        }

        foreach ($delTeachers as $teacher) {
            $form->recordEvent(new DeleteTeacherGroupEvent($teacher->id), TrainingGroupWork::className());
        }
    }

    public function attachParticipants(TrainingGroupParticipantForm $form)
    {
        $newParticipants = [];
        foreach ($form->participants as $participant) {
            /** @var TrainingGroupParticipantWork $participant */
            $newParticipants[] = TrainingGroupParticipantWork::fill(
                $form->id,
                $participant->participant_id,
                $participant->send_method,
                $participant->id ? : null
            );
        }
        $newParticipants = array_unique($newParticipants);

        $addParticipants = $this->setDifference($newParticipants, $form->prevParticipants, ParticipantGroupCompare::class);
        $delParticipants = $this->setDifference($form->prevParticipants, $newParticipants, ParticipantGroupCompare::class);

        foreach ($addParticipants as $participant) {
            $form->recordEvent(new CreateTrainingGroupParticipantEvent($form->id, $participant->participant_id, $participant->send_method), TrainingGroupParticipantWork::className());
        }

        foreach ($delParticipants as $participant) {
            $form->recordEvent(new DeleteVisitEvent($participant->id), VisitWork::className());
            $form->recordEvent(new DeleteTrainingGroupParticipantEvent($participant->id), TrainingGroupParticipantWork::className());
        }

        foreach ($newParticipants as $participant) {
            if ($participant->id !== null) {
                $form->recordEvent(new UpdateTrainingGroupParticipantEvent($participant->id, $participant->participant_id, $participant->send_method), TrainingGroupParticipantWork::className());
            }
        }
    }

    public function attachLessons(TrainingGroupScheduleForm $form)
    {
        $newLessons = [];
        foreach ($form->lessons as $lesson) {
            /** @var TrainingGroupLessonWork $lesson */
            $lessonEntity = TrainingGroupLessonWork::fill(
                $form->id,
                $lesson->lesson_date,
                $lesson->lesson_start_time,
                $lesson->branch,
                $lesson->auditorium_id,
                $lesson->lesson_end_time,
                $lesson->duration
            );
            if ($lessonEntity->isEnoughData()) {
                $newLessons[] = $lessonEntity;
            }
        }
        $newLessons = array_unique($newLessons);

        foreach ($newLessons as $lesson) {
            $form->recordEvent(
                new AddLessonToVisitEvent($form->trainingGroup->id, [$lesson]),
                TrainingGroupLessonWork::class
            );
            $this->trainingGroupLessonRepository->save($lesson);
            $form->releaseEvents();
        }
    }

    public function createNewThemes(PitchGroupForm $form)
    {
        $themeIds = [];
        foreach ($form->themes as $theme) {
            $themeIds[] = $this->themeRepository->save(
                ProjectThemeWork::fill(
                    $theme->name,
                    $theme->project_type,
                    $theme->description
                )
            );
        }

        $form->themeIds = $themeIds;
    }

    public function attachThemes(PitchGroupForm $form)
    {
        foreach ($form->themeIds as $themeId) {
            $form->recordEvent(
                new AddGroupThemeEvent(
                    $form->id,
                    $themeId,
                    GroupProjectThemesWork::NO_CONFIRM
                ),
                GroupProjectThemesWork::class
            );
        }
        $form->releaseEvents();
    }

    public function attachExperts(PitchGroupForm $form)
    {
        $newExperts = [];
        foreach ($form->experts as $expert) {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($expert->expertId);
            $groupExpertEntity = TrainingGroupExpertWork::fill(
                $form->id,
                $peopleStampId,
                $expert->expert_type,
                $expert->id !== '' ? : null,
            );
            $newExperts[] = $groupExpertEntity;
        }
        $newExperts = array_unique($newExperts);

        $addExperts = $this->setDifference($newExperts, $form->prevExperts, GroupExpertCompare::class);
        $delExperts = $this->setDifference($form->prevExperts, $newExperts, GroupExpertCompare::class);

        foreach ($addExperts as $expert) {
            /** @var TrainingGroupExpertWork $expert */
            $form->recordEvent(new AddGroupExpertEvent($form->id, $expert->expert_id, $expert->expert_type), TrainingGroupExpertWork::class);
        }

        foreach ($delExperts as $expert) {
            /** @var TrainingGroupExpertWork $expert */
            $form->recordEvent(new DeleteGroupExpertEvent($expert->id), TrainingGroupExpertWork::class);
        }

        foreach ($newExperts as $expert) {
            if ($expert->id !== null) {
                $form->recordEvent(new UpdateGroupExpertEvent($expert->id, $expert->expert_id, $expert->expert_type), TrainingGroupExpertWork::class);
            }
        }
    }

    public function preprocessingLessons(TrainingGroupScheduleForm $formSchedule)
    {
        foreach ($formSchedule->lessons as $lesson) {
            /** @var TrainingGroupLessonWork $lesson */
            $lesson->duration = 1;
            $capacity = $formSchedule->trainingProgram->hour_capacity ?: 40;
            $lesson->lesson_end_time = ((new DateTime($lesson->lesson_start_time))->modify("+{$capacity} minutes"))->format('H:i:s');
            $lesson->lesson_start_time = (new DateTime($lesson->lesson_start_time))->format('H:i:s');
        }
    }

    public function prepareFormScheduleData($id)
    {
        $formSchedule = new TrainingGroupScheduleForm($id);
        $auditoriums = (Yii::createObject(AuditoriumRepository::class))->getAll();
        $scheduleTable = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Дата занятия'], ArrayHelper::getColumn($formSchedule->prevLessons, 'lesson_date')),
                array_merge(['Время начала'], ArrayHelper::getColumn($formSchedule->prevLessons, 'lesson_start_time')),
                array_merge(['Время окончания'], ArrayHelper::getColumn($formSchedule->prevLessons, 'lesson_end_time')),
                array_merge(['Помещение'], ArrayHelper::getColumn($formSchedule->prevLessons, 'auditoriumName'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Редактировать',
                    Url::to('update-lesson'),
                    [
                        'groupId' => array_fill(0, count($formSchedule->prevLessons), $formSchedule->id),
                        'entityId' => ArrayHelper::getColumn($formSchedule->prevLessons, 'id')
                    ]
                ),
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-lesson'),
                    [
                        'groupId' => array_fill(0, count($formSchedule->prevLessons), $formSchedule->id),
                        'entityId' => ArrayHelper::getColumn($formSchedule->prevLessons, 'id')
                    ]
                )
            ]
        );

        if (count($formSchedule->lessons) > 0) {
            $scheduleTable = HtmlBuilder::wrapTableInCheckboxesColumn(
                Url::to(['group-deletion', 'id' => $formSchedule->id]),
                'Удалить выбранные',
                'check[]',
                ArrayHelper::getColumn($formSchedule->prevLessons, 'id'),
                $scheduleTable
            );
        }

        return [
            'formSchedule' => $formSchedule,
            'modelLessons' => [new TrainingGroupLessonWork],
            'auditoriums' => $auditoriums,
            'scheduleTable' => $scheduleTable
        ];
    }

    public function createLessonThemes($groupId)
    {
        /** @var TrainingGroupWork $group */
        $group = $this->trainingGroupRepository->get($groupId);
        $teachers = ArrayHelper::getColumn(
            $this->teacherGroupRepository->getAllTeachersFromGroup($groupId),
            'teacher_id'
        );

        if (!$group->haveProgram()) {
            return TrainingGroupWork::ERROR_NO_PROGRAM;
        }

        $lessons = $this->trainingGroupLessonRepository->getLessonsFromGroup($groupId);
        $thematicPlan = $this->trainingProgramRepository->getThematicPlan($group->training_program_id);

        if (count($lessons) !== count($thematicPlan)) {
            return TrainingGroupWork::ERROR_THEMES_MISMATCH;
        }

        $this->deleteLessonThemes($groupId);

        foreach ($thematicPlan as $key => $theme) {
            /** @var ThematicPlanWork $theme */
            $this->lessonThemeRepository->save(
                LessonThemeWork::fill(
                    $lessons[$key]->id,
                    $theme->id,
                    count($teachers) > 0 ? $teachers[0] : null
                )
            );
        }

        return true;
    }

    public function deleteLessonThemes($groupId)
    {
        $lessonIds = ArrayHelper::getColumn(
            $this->trainingGroupLessonRepository->getLessonsFromGroup($groupId),
            'id'
        );
        $lessonThemes = $this->lessonThemeRepository->getByLessonIds($lessonIds);

        foreach ($lessonThemes as $lessonTheme) {
            $this->lessonThemeRepository->delete($lessonTheme);
        }
    }

    /**
     * Метод архивации учебных групп
     *
     * @param array $actual список id учебных групп, которые требуется сделать актуальными
     * @param array $unactual список id учебных групп, которые требуется сделать архивными
     * @return array
     */
    public function setGroupArchive(array $actual, array $unactual)
    {
        $errors = [];
        foreach ($actual as $actualId) {
            /** @var TrainingGroupWork $group */
            $group = $this->trainingGroupRepository->get($actualId);
            $group->setArchive(TrainingGroupWork::NO_ARCHIVE);
            $this->trainingGroupRepository->save($group);
        }

        foreach ($unactual as $unactualId) {
            /** @var TrainingGroupWork $group */
            $group = $this->trainingGroupRepository->get($unactualId);
            if (!$this->errorService->isGroupRestrict($unactualId)) {
                $group->setArchive(TrainingGroupWork::IS_ARCHIVE);
                $this->trainingGroupRepository->save($group);
            }
            else {
                $errors[] = "Невозможно добавить группу $group->number с ошибками в архив";
            }
        }

        return $errors;
    }

    public function refreshVisitsByParticipants(int $groupId)
    {
        $participantIds = ArrayHelper::getColumn(
            $this->trainingGroupParticipantRepository->getParticipantsFromGroups([$groupId]),
            'id'
        );

        $visitLessons = [];
        $lessons = $this->trainingGroupLessonRepository->getLessonsFromGroup($groupId);
        foreach ($lessons as $lesson) {
            $visitLessons[] = new VisitLesson(
                $lesson->id,
                VisitWork::NONE
            );
        }
        $lessonsString = VisitLesson::toString($visitLessons);

        foreach ($participantIds as $participantId) {
            $visitEntity = $this->visitRepository->getByTrainingGroupParticipant($participantId);
            if (!$visitEntity) {
                $this->visitRepository->save(
                    VisitWork::fill(
                        $participantId,
                        $lessonsString
                    )
                );
            }
        }
    }
}