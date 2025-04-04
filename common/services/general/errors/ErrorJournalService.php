<?php

namespace common\services\general\errors;

use common\components\access\pbac\PbacLessonAccess;
use common\components\dictionaries\base\CertificateTypeDictionary;
use common\components\dictionaries\base\ErrorDictionary;
use common\helpers\files\FilesHelper;
use common\models\work\ErrorsWork;
use common\repositories\educational\CertificateRepository;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\LessonThemeRepository;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\educational\VisitRepository;
use common\repositories\general\ErrorsRepository;
use common\repositories\order\DocumentOrderRepository;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\BranchProgramWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\event\ForeignEventWork;
use Yii;
use yii\helpers\ArrayHelper;

class ErrorJournalService
{
    private ErrorsRepository $errorsRepository;
    private TrainingGroupRepository $groupRepository;
    private TeacherGroupRepository $teacherGroupRepository;
    private OrderTrainingGroupParticipantRepository $orderParticipantRepository;
    private TrainingGroupLessonRepository $lessonRepository;
    private LessonThemeRepository $themeRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private CertificateRepository $certificateRepository;
    private TrainingProgramRepository $programRepository;
    private GroupProjectThemesRepository $projectRepository;
    private VisitRepository $visitRepository;

    public function __construct(
        ErrorsRepository $errorsRepository,
        TrainingGroupRepository $groupRepository,
        TeacherGroupRepository $teacherGroupRepository,
        OrderTrainingGroupParticipantRepository $orderParticipantRepository,
        TrainingGroupLessonRepository $lessonRepository,
        LessonThemeRepository $themeRepository,
        TrainingGroupParticipantRepository $participantRepository,
        CertificateRepository $certificateRepository,
        TrainingProgramRepository $programRepository,
        GroupProjectThemesRepository $projectRepository,
        VisitRepository $visitRepository
    )
    {
        $this->errorsRepository = $errorsRepository;
        $this->groupRepository = $groupRepository;
        $this->teacherGroupRepository = $teacherGroupRepository;
        $this->orderParticipantRepository = $orderParticipantRepository;
        $this->lessonRepository = $lessonRepository;
        $this->themeRepository = $themeRepository;
        $this->participantRepository = $participantRepository;
        $this->certificateRepository = $certificateRepository;
        $this->programRepository = $programRepository;
        $this->projectRepository = $projectRepository;
        $this->visitRepository = $visitRepository;
    }

    // Проверяет на отсутствие прикрепленного к группе педагога (хотя бы одного)
    public function makeJournal_001($rowId)
    {
        /** @var TeacherGroupWork[] $teachers */
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        $teachers = $this->teacherGroupRepository->getAllTeachersFromGroup($rowId);
        if (count($teachers) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_001,
                    TrainingGroupWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_001)->getErrorState(),
                    $group->branch
                )
            );
        }
    }

    public function fixJournal_001($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TeacherGroupWork[] $teachers */
        $error = $this->errorsRepository->get($errorId);
        $teachers = $this->teacherGroupRepository->getAllTeachersFromGroup($error->table_row_id);
        if (count($teachers) != 0) {
            $this->errorsRepository->delete($error);
        }
    }

    /*
     * Проверка на отсутствие приказов в группе
     * 1. На момент начала занятий должен быть как минимум 1 приказ о зачислении
     * 2. На момент окончания занятий должно быть как минимум 2 приказа: не менее 1 о зачислении и не менее 1 об отчислении
     */
    public function makeJournal_002($rowId)
    {
        /** @var TrainingGroupWork $group */
        $errFlag = true;
        $group = $this->groupRepository->get($rowId);
        if (date('Y-m-d') >= $group->start_date) {
            $orderEnrollParticipants = $this->orderParticipantRepository->getEnrollByGroupId($rowId);
            if (count($orderEnrollParticipants) >= 1) {
                $errFlag = false;
            }
        }
        if (date('Y-m-d') >= $group->finish_date) {
            $orderExclusionParticipants = $this->orderParticipantRepository->getExlusionByGroupId($rowId);
            if (count($orderExclusionParticipants) >= 1) {
                $errFlag = false;
            }
        }

        if ($errFlag) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_002,
                    TrainingGroupWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_002)->getErrorState(),
                    $group->branch
                )
            );
        }
    }

    public function fixJournal_002($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $errFlag = false;
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        if (!(date('Y-m-d') >= $group->start_date)) {
            $orderEnrollParticipants = $this->orderParticipantRepository->getEnrollByGroupId($error->table_row_id);
            $errFlag = count($orderEnrollParticipants) >= 1;
        }
        if (date('Y-m-d') >= $group->finish_date) {
            $orderExclusionParticipants = $this->orderParticipantRepository->getExlusionByGroupId($error->table_row_id);
            $errFlag = count($orderExclusionParticipants) >= 1;
        }

        if ($errFlag) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие фотоматериалов
    public function makeJournal_003($rowId)
    {
        $daysCount = 4;
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        if (strtotime($group->finish_date) <= strtotime("+$daysCount days")) {
            if (count($group->getFileLinks(FilesHelper::TYPE_PHOTO)) == 0) {
                $this->errorsRepository->save(
                    ErrorsWork::fill(
                        ErrorDictionary::JOURNAL_003,
                        TrainingGroupWork::tableName(),
                        $rowId,
                        Yii::$app->errors->get(ErrorDictionary::JOURNAL_003)->getErrorState(),
                        $group->branch
                    )
                );
            }
        }
    }

    public function fixJournal_003($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        if (count($group->getFileLinks(FilesHelper::TYPE_PHOTO)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие презентационных материалов
    public function makeJournal_004($rowId)
    {
        $daysCount = 4;
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        if (strtotime($group->finish_date) <= strtotime("+$daysCount days")) {
            if (count($group->getFileLinks(FilesHelper::TYPE_PRESENTATION)) == 0) {
                $this->errorsRepository->save(
                    ErrorsWork::fill(
                        ErrorDictionary::JOURNAL_004,
                        TrainingGroupWork::tableName(),
                        $rowId,
                        Yii::$app->errors->get(ErrorDictionary::JOURNAL_004)->getErrorState(),
                        $group->branch
                    )
                );
            }
        }
    }

    public function fixJournal_004($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        if (count($group->getFileLinks(FilesHelper::TYPE_PRESENTATION)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие рабочих материалов
    public function makeJournal_005($rowId)
    {
        $daysCount = 4;
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        if (strtotime($group->finish_date) <= strtotime("+$daysCount days")) {
            if (count($group->getFileLinks(FilesHelper::TYPE_WORK)) == 0) {
                $this->errorsRepository->save(
                    ErrorsWork::fill(
                        ErrorDictionary::JOURNAL_005,
                        TrainingGroupWork::tableName(),
                        $rowId,
                        Yii::$app->errors->get(ErrorDictionary::JOURNAL_005)->getErrorState(),
                        $group->branch
                    )
                );
            }
        }
    }

    public function fixJournal_005($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        if (count($group->getFileLinks(FilesHelper::TYPE_WORK)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на соответствие объема программы и расписания группы
    public function makeJournal_006($rowId)
    {
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        $lessons = $this->lessonRepository->getLessonsFromGroup($rowId);
        if ($group->trainingProgramWork->capacity != count($lessons)) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_006,
                    TrainingGroupWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_006)->getErrorState(),
                    $group->branch
                )
            );
        }
    }

    public function fixJournal_006($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        $lessons = $this->lessonRepository->getLessonsFromGroup($error->table_row_id);
        if ($group->trainingProgramWork->capacity == count($lessons)) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие сертификатов
    public function makeJournal_008($rowId)
    {
        /** @var TrainingGroupParticipantWork[] $participants */
        $participants = $this->participantRepository->getSuccessParticipantsFromGroup($rowId);
        foreach ($participants as $participant) {
            if (!$this->certificateRepository->getByGroupParticipantId($participant->id)) {
                $this->errorsRepository->save(
                    ErrorsWork::fill(
                        ErrorDictionary::JOURNAL_008,
                        TrainingGroupWork::tableName(),
                        $rowId,
                        Yii::$app->errors->get(ErrorDictionary::JOURNAL_008)->getErrorState(),
                        $participant->trainingGroupWork->branch
                    )
                );
                break;
            }
        }
    }

    public function fixJournal_008($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupParticipantWork[] $participants */
        $error = $this->errorsRepository->get($errorId);
        $participants = $this->participantRepository->getSuccessParticipantsFromGroup($error->table_row_id);
        foreach ($participants as $participant) {
            if (!$this->certificateRepository->getByGroupParticipantId($participant->id)) {
                return;
            }
        }

        $this->errorsRepository->delete($error);
    }

    // Проверка на отсутствие явок
    public function makeJournal_009($rowId)
    {
        /** @var VisitWork[] $visits */
        /** @var TrainingGroupLessonWork[] $lessonsToCheck */
        $visits = $this->visitRepository->getByTrainingGroup($rowId);

        // Получаем все ID занятий, которые подлежат проверке на ошибки
        $lessonIdsToCheck = ArrayHelper::getColumn(
            array_filter(
                $this->lessonRepository->getLessonsFromGroup($rowId),
                function (TrainingGroupLessonWork $lesson) {
                    $currentDate = strtotime("today");
                    $lowerBound = strtotime("-" . PbacLessonAccess::LESSON_OFFSET_DOWN . " days", $currentDate);
                    $targetDate = strtotime($lesson->lesson_date);
                    return $targetDate < $lowerBound;
                }
            ),
            'id'
        );

        /*
         * Создаем массив для отслеживания состояния каждого занятия
         * 0 - для занятия нет ни одной отметки
         * 1 - есть хотя бы одна отметка
         */
        $lessonChecker = array_fill_keys($lessonIdsToCheck, 0);

        foreach ($visits as $visit) {
            $lessonsFromVisit = VisitLesson::fromString($visit->lessons, $this->lessonRepository);
            foreach ($lessonsFromVisit as $lesson) {
                if (in_array($lesson->lessonId, $lessonIdsToCheck) && $lesson->status != VisitWork::NONE) {
                    $lessonChecker[$lesson->lessonId] = 1;
                }
            }

            // Если остался 0 хотя бы у одного занятия
            if (array_sum($lessonChecker) != count($lessonChecker)) {
                $this->errorsRepository->save(
                    ErrorsWork::fill(
                        ErrorDictionary::JOURNAL_009,
                        TrainingGroupWork::tableName(),
                        $rowId,
                        Yii::$app->errors->get(ErrorDictionary::JOURNAL_009)->getErrorState(),
                        $visits[0]->trainingGroupParticipantWork->trainingGroupWork->branch
                    )
                );
            }
        }
    }

    public function fixJournal_009($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var VisitWork[] $visits */
        /** @var TrainingGroupLessonWork[] $lessonsToCheck */
        $error = $this->errorsRepository->get($errorId);
        $visits = $this->visitRepository->getByTrainingGroup($error->table_row_id);

        // Получаем все ID занятий, которые подлежат проверке на ошибки
        $lessonIdsToCheck = ArrayHelper::getColumn(
            array_filter(
                $this->lessonRepository->getLessonsFromGroup($error->table_row_id),
                function (TrainingGroupLessonWork $lesson) {
                    $currentDate = strtotime("today");
                    $lowerBound = strtotime("-" . PbacLessonAccess::LESSON_OFFSET_DOWN . " days", $currentDate);
                    $targetDate = strtotime($lesson->lesson_date);
                    return $targetDate < $lowerBound;
                }
            ),
            'id'
        );

        /*
         * Создаем массив для отслеживания состояния каждого занятия
         * 0 - для занятия нет ни одной отметки
         * 1 - есть хотя бы одна отметка
         */
        $lessonChecker = array_fill_keys($lessonIdsToCheck, 0);

        foreach ($visits as $visit) {
            $lessonsFromVisit = VisitLesson::fromString($visit->lessons, $this->lessonRepository);
            foreach ($lessonsFromVisit as $lesson) {
                if (in_array($lesson->lessonId, $lessonIdsToCheck) && $lesson->status != VisitWork::NONE) {
                    $lessonChecker[$lesson->lessonId] = 1;
                }
            }

            // Если не осталось 0 ни у одного занятия
            if (array_sum($lessonChecker) == count($lessonChecker)) {
                $this->errorsRepository->delete($error);
            }
        }
    }

    // Проверка на отсутствие тематического направления в образовательной программе
    public function makeJournal_010($rowId)
    {
        /** @var TrainingProgramWork $program */
        $program = $this->programRepository->get($rowId);
        if (is_null($program->thematic_direction)) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_010,
                    TrainingProgramWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_010)->getErrorState(),
                    $program->branchProgramWork[0] ? $program->branchProgramWork[0]->branch : null
                )
            );
        }
    }

    public function fixJournal_010($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingProgramWork $program */
        $error = $this->errorsRepository->get($errorId);
        $program = $this->programRepository->get($error->table_row_id);
        if ($program->thematic_direction) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие формы контроля
    public function makeJournal_011($rowId)
    {
        /** @var TrainingProgramWork $program */
        $program = $this->programRepository->get($rowId);
        if (is_null($program->certificate_type)) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_011,
                    TrainingProgramWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_011)->getErrorState(),
                    $program->branchProgramWork[0] ? $program->branchProgramWork[0]->branch : null
                )
            );
        }
    }

    public function fixJournal_011($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingProgramWork $program */
        $error = $this->errorsRepository->get($errorId);
        $program = $this->programRepository->get($error->table_row_id);
        if ($program->thematic_direction) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на соответствие объема образовательной программы с ее УТП
    public function makeJournal_012($rowId)
    {
        /** @var TrainingProgramWork $program */
        $program = $this->programRepository->get($rowId);
        $plan = $this->programRepository->getThematicPlan($rowId);
        if (count($plan) != $program->capacity) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_012,
                    TrainingProgramWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_012)->getErrorState(),
                    $program->branchProgramWork[0] ? $program->branchProgramWork[0]->branch : null
                )
            );
        }
    }

    public function fixJournal_012($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingProgramWork $program */
        $error = $this->errorsRepository->get($errorId);
        $program = $this->programRepository->get($error->table_row_id);
        $plan = $this->programRepository->getThematicPlan($error->table_row_id);
        if (count($plan) == $program->capacity) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие авторов программы
    public function makeJournal_013($rowId)
    {
        /** @var TrainingProgramWork $program */
        $program = $this->programRepository->get($rowId);
        $authors = $this->programRepository->getAuthors($rowId);
        if (count($authors) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_013,
                    TrainingProgramWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_013)->getErrorState(),
                    $program->branchProgramWork[0] ? $program->branchProgramWork[0]->branch : null
                )
            );
        }
    }

    public function fixJournal_013($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingProgramWork $program */
        $error = $this->errorsRepository->get($errorId);
        $authors = $this->programRepository->getAuthors($error->table_row_id);
        if (count($authors) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка учебной группы на использование некорректных помещений для занятий
    public function makeJournal_014($rowId)
    {
        /** @var TrainingGroupLessonWork[] $lessons */
        $lessons = $this->lessonRepository->getLessonsFromGroup($rowId);
        foreach ($lessons as $lesson) {
            if ($lesson->auditoriumWork && !$lesson->auditoriumWork->isEducation()) {
                $this->errorsRepository->save(
                    ErrorsWork::fill(
                        ErrorDictionary::JOURNAL_014,
                        TrainingGroupWork::tableName(),
                        $rowId,
                        Yii::$app->errors->get(ErrorDictionary::JOURNAL_014)->getErrorState(),
                        $lesson->trainingGroupWork->branch
                    )
                );
                break;
            }
        }
    }

    public function fixJournal_014($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupLessonWork[] $lessons */
        $error = $this->errorsRepository->get($errorId);
        $lessons = $this->lessonRepository->getLessonsFromGroup($error->table_row_id);
        foreach ($lessons as $lesson) {
            if (!$lesson->auditoriumWork->isEducation()) {
                return;
            }
        }

        $this->errorsRepository->delete($error);
    }

    public function makeJournal_015($rowId)
    {
        // deprecated
    }

    public function fixJournal_015($errorId)
    {
        // deprecated
    }

    // Проверка на несовпадение даты последнего занятия и даты окончания обучения группы
    public function makeJournal_016($rowId)
    {
        /** @var TrainingGroupWork $group */
        /** @var TrainingGroupLessonWork[] $lessons */
        $group = $this->groupRepository->get($rowId);
        $lessons = $this->lessonRepository->getLessonsFromGroup($rowId);
        if ($group->finish_date != end($lessons)->lesson_date) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_016,
                    TrainingGroupWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_016)->getErrorState(),
                    $group->branch
                )
            );
        }
    }

    public function fixJournal_016($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        $lessons = $this->lessonRepository->getLessonsFromGroup($error->table_row_id);
        if ($group->finish_date == end($lessons)->lesson_date) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на нахождение группы в архиве
    public function makeJournal_017($rowId)
    {
        $daysCount = 5;
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        $currentDate = strtotime($group->finish_date);
        $upperBound = strtotime("+$daysCount days", $currentDate);
        $targetDate = strtotime("today");

        if ($targetDate > $upperBound && !$group->isArchive()) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_017,
                    TrainingGroupWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_017)->getErrorState(),
                    $group->branch
                )
            );
        }
    }

    public function fixJournal_017($errorId)
    {
        $daysCount = 5;
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        $currentDate = strtotime($group->finish_date);
        $upperBound = strtotime("+$daysCount days", $currentDate);
        $targetDate = strtotime("today");

        if ($targetDate <= $upperBound || $group->isArchive()) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на наличие отдела реализации образовательной программы
    public function makeJournal_018($rowId)
    {
        /** @var BranchProgramWork[] $program */
        /** @var TrainingProgramWork $program */
        $program = $this->programRepository->get($rowId);
        $branches = $this->programRepository->getBranches($rowId);
        if (count($branches) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_018,
                    TrainingProgramWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_018)->getErrorState(),
                    $program->branchProgramWork[0] ? $program->branchProgramWork[0]->branch : null
                )
            );
        }
    }

    public function fixJournal_018($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $branches = $this->programRepository->getBranches($error->table_row_id);
        if (count($branches) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие даты педагогического совета
    public function makeJournal_019($rowId)
    {
        /** @var TrainingProgramWork $program */
        $program = $this->programRepository->get($rowId);
        if (is_null($program->ped_council_date)) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_019,
                    TrainingProgramWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_019)->getErrorState(),
                    $program->branchProgramWork[0] ? $program->branchProgramWork[0]->branch : null
                )
            );
        }
    }

    public function fixJournal_019($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingProgramWork $program */
        $error = $this->errorsRepository->get($errorId);
        $program = $this->programRepository->get($error->table_row_id);
        if ($program->ped_council_date) {
            $this->errorsRepository->delete($error);
        }
    }

    /*
     * Проверка на наличие обучающихся, не фигурирующих в соответствующих приказах
     * 1. На момент начала занятий должен быть как минимум 1 приказ о зачислении
     * 2. На момент окончания занятий должно быть как минимум 2 приказа: не менее 1 о зачислении и не менее 1 об отчислении
     */
    public function makeJournal_020($rowId)
    {
        /** @var TrainingGroupWork $group */
        $errFlag = true;
        $group = $this->groupRepository->get($rowId);
        $participants = $this->participantRepository->getParticipantsFromGroups([$rowId]);
        if (date('Y-m-d') >= $group->start_date) {
            $orderEnrollParticipants = $this->orderParticipantRepository->getEnrollByGroupId($rowId);
            if (count($orderEnrollParticipants) != count($participants)) {
                $errFlag = false;
            }
        }
        if (date('Y-m-d') >= $group->finish_date) {
            $orderExclusionParticipants = $this->orderParticipantRepository->getExlusionByGroupId($rowId);
            if (count($orderExclusionParticipants) != count($participants)) {
                $errFlag = false;
            }
        }

        if ($errFlag) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_020,
                    TrainingGroupWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_020)->getErrorState(),
                    $group->branch
                )
            );
        }
    }

    public function fixJournal_020($errorId)
    {
        $errFlag = false;
        /** @var ErrorsWork $error */
        /** @var TrainingProgramWork $program */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        $participants = $this->participantRepository->getParticipantsFromGroups([$error->table_row_id]);
        if (date('Y-m-d') >= $group->start_date) {
            $orderEnrollParticipants = $this->orderParticipantRepository->getEnrollByGroupId($error->table_row_id);
            if (count($orderEnrollParticipants) == count($participants)) {
                $errFlag = true;
            }
        }
        if (date('Y-m-d') >= $group->finish_date) {
            $orderExclusionParticipants = $this->orderParticipantRepository->getExlusionByGroupId($error->table_row_id);
            if (count($orderExclusionParticipants) == count($participants)) {
                $errFlag = true;
            }
        }

        if ($errFlag) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие даты защиты
    public function makeJournal_021($rowId)
    {
        $daysCount = 4;
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        $currentDate = strtotime($group->finish_date);
        $lowerBound = strtotime("-$daysCount days", $currentDate);
        $targetDate = strtotime("today");

        if (is_null($group->protection_date) && $targetDate >= $lowerBound) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_021,
                    TrainingGroupWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_021)->getErrorState(),
                    $group->branch
                )
            );
        }
    }

    public function fixJournal_021($errorId)
    {
        $daysCount = 4;
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        $currentDate = strtotime($group->finish_date);
        $lowerBound = strtotime("-$daysCount days", $currentDate);
        $targetDate = strtotime("today");

        if (!(is_null($group->protection_date) && $targetDate >= $lowerBound)) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие темы проекта
    public function makeJournal_022($rowId)
    {
        $daysCount = 10;
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        $projectThemes = $this->projectRepository->getProjectThemesFromGroup($rowId);
        $currentDate = strtotime($group->finish_date);
        $lowerBound = strtotime("-$daysCount days", $currentDate);
        $targetDate = strtotime("today");

        if (
            count($projectThemes) == 0 &&
            $targetDate >= $lowerBound &&
            $group->trainingProgramWork->certificate_type == CertificateTypeDictionary::PROJECT_PITCH
        ) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_022,
                    TrainingGroupWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_022)->getErrorState(),
                    $group->branch
                )
            );
        }
    }

    public function fixJournal_022($errorId)
    {
        $daysCount = 10;
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        $projectThemes = $this->projectRepository->getProjectThemesFromGroup($error->table_row_id);
        $currentDate = strtotime($group->finish_date);
        $lowerBound = strtotime("-$daysCount days", $currentDate);
        $targetDate = strtotime("today");

        if (!(count($projectThemes) == 0 && $targetDate >= $lowerBound)) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на наличие эксперта в группе
    public function makeJournal_023($rowId)
    {
        $daysCount = 4;
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        $experts = $this->groupRepository->getExperts($rowId);
        $currentDate = strtotime($group->finish_date);
        $lowerBound = strtotime("-$daysCount days", $currentDate);
        $targetDate = strtotime("today");

        if (count($experts) == 0 && $targetDate >= $lowerBound) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_023,
                    TrainingGroupWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_023)->getErrorState(),
                    $group->branch
                )
            );
        }
    }

    public function fixJournal_023($errorId)
    {
        $daysCount = 4;
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        $experts = $this->groupRepository->getExperts($error->table_row_id);
        $currentDate = strtotime($group->finish_date);
        $lowerBound = strtotime("-$daysCount days", $currentDate);
        $targetDate = strtotime("today");

        if (!(count($experts) == 0 && $targetDate >= $lowerBound)) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на соответствие даты защиты и даты окончания занятий
    public function makeJournal_024($rowId)
    {
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);

        if (!empty($group->protection_date) && $group->finish_date >= $group->protection_date) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_024,
                    TrainingGroupWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_024)->getErrorState(),
                    $group->branch
                )
            );
        }
    }

    public function fixJournal_024($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);

        if ($group->finish_date < $group->protection_date) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие тем проекта, прикрепленных к обучающимся
    public function makeJournal_025($rowId)
    {
        $daysCount = 4;
        /** @var TrainingGroupWork $group */
        /** @var TrainingGroupParticipantWork[] $participants */
        $group = $this->groupRepository->get($rowId);
        $participants = $this->participantRepository->getEnrolledParticipantsFromGroup($rowId);
        $currentDate = strtotime($group->finish_date);
        $lowerBound = strtotime("-$daysCount days", $currentDate);
        $targetDate = strtotime("today");

        if ($targetDate >= $lowerBound && $group->trainingProgramWork->certificate_type == CertificateTypeDictionary::PROJECT_PITCH) {
            foreach ($participants as $participant) {
                if (is_null($participant->group_project_themes_id)) {
                    $this->errorsRepository->save(
                        ErrorsWork::fill(
                            ErrorDictionary::JOURNAL_025,
                            TrainingGroupWork::tableName(),
                            $rowId,
                            Yii::$app->errors->get(ErrorDictionary::JOURNAL_025)->getErrorState(),
                            $group->branch
                        )
                    );
                    break;
                }
            }
        }
    }

    public function fixJournal_025($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        /** @var TrainingGroupParticipantWork[] $participants */
        $error = $this->errorsRepository->get($errorId);
        $participants = $this->participantRepository->getEnrolledParticipantsFromGroup($error->table_row_id);

        foreach ($participants as $participant) {
            if (is_null($participant->group_project_themes_id)) {
                return;
            }
        }

        $this->errorsRepository->delete($error);
    }

    // Проверка на наличие документа программы в образовательной программе
    public function makeJournal_026($rowId)
    {
        /** @var TrainingProgramWork $program */
        $program = $this->programRepository->get($rowId);
        if (count($program->getFileLinks(FilesHelper::TYPE_MAIN)) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_026,
                    TrainingProgramWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_026)->getErrorState(),
                    $program->branchProgramWork[0] ? $program->branchProgramWork[0]->branch : null
                )
            );
        }
    }

    public function fixJournal_026($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingProgramWork $program */
        $error = $this->errorsRepository->get($errorId);
        $program = $this->programRepository->get($error->table_row_id);
        if (count($program->getFileLinks(FilesHelper::TYPE_MAIN)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на наличие редактируемого документа в образовательной программе
    public function makeJournal_027($rowId)
    {
        /** @var TrainingProgramWork $program */
        $program = $this->programRepository->get($rowId);
        if (count($program->getFileLinks(FilesHelper::TYPE_DOC)) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_027,
                    TrainingProgramWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::JOURNAL_027)->getErrorState(),
                    $program->branchProgramWork[0] ? $program->branchProgramWork[0]->branch : null
                )
            );
        }
    }

    public function fixJournal_027($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingProgramWork $program */
        $error = $this->errorsRepository->get($errorId);
        $program = $this->programRepository->get($error->table_row_id);
        if (count($program->getFileLinks(FilesHelper::TYPE_DOC)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }
}