<?php

namespace backend\services\report;

use backend\forms\report\ManHoursReportForm;
use backend\helpers\DebugReportHelper;
use backend\helpers\ReportHelper;
use common\helpers\DateFormatter;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\educational\LessonThemeRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\VisitRepository;
use common\repositories\event\ParticipantAchievementRepository;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\team\ActParticipantWork;
use Yii;
use yii\helpers\ArrayHelper;

class DebugReportService
{
    private TrainingGroupRepository $groupRepository;
    private TrainingGroupLessonRepository $lessonRepository;
    private LessonThemeRepository $lessonThemeRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private ActParticipantRepository $actParticipantRepository;
    private ParticipantAchievementRepository $participantAchievementRepository;
    private VisitRepository $visitRepository;

    public function __construct(
        TrainingGroupRepository $groupRepository,
        TrainingGroupLessonRepository $lessonRepository,
        LessonThemeRepository $lessonThemeRepository,
        TrainingGroupParticipantRepository $participantRepository,
        ActParticipantRepository $actParticipantRepository,
        ParticipantAchievementRepository $participantAchievementRepository,
        VisitRepository $visitRepository
    )
    {
        $this->groupRepository = $groupRepository;
        $this->lessonRepository = $lessonRepository;
        $this->lessonThemeRepository = $lessonThemeRepository;
        $this->participantRepository = $participantRepository;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->participantAchievementRepository = $participantAchievementRepository;
        $this->visitRepository = $visitRepository;
    }

    /**
     * @param TrainingGroupParticipantWork[] $participants
     * @return string[][]
     */
    public function createParticipantDebugData(array $participants): array
    {
        return DebugReportHelper::createParticipantDebugData($participants);
    }


    /**
     * @param TrainingGroupWork[] $groups
     * @param string $startDate
     * @param string $endDate
     * @param int $calculateType
     * @param int[] $teacherIds
     * @return string[][]
     */
    public function createManHoursDebugData(
        array $groups,
        string $startDate,
        string $endDate,
        int $calculateType,
        array $teacherIds = []
    ): array {
        $data = [];

        foreach ($groups as $group) {
            // Уроки группы
            $lessons = $this->groupRepository->getLessons($group->id);
            $lessonIds = ArrayHelper::getColumn($lessons, 'id');

            // Количество учеников
            $studentsCount = count($this->participantRepository->getParticipantsFromGroups([$group->id]));

            if ($calculateType === ManHoursReportForm::MAN_HOURS_PROGRAM) {
                // 1. Объем программы (capacity, ак. ч.)
                $capacity = 0;
                if (!empty($group->trainingProgramWork)) {
                    $capacity = (float)($group->trainingProgramWork->capacity ?? 0);
                } elseif (!empty($group->training_program_id)) {
                    $capacity = (float)(\frontend\models\work\educational\training_program\TrainingProgramWork::find()
                        ->select('capacity')
                        ->where(['id' => $group->training_program_id])
                        ->scalar() ?? 0);
                }

                // 2. Кол-во занятий в расписании (непосредственно число уроков группы)
                $lessonsScheduleCount = count($lessonIds);

                // 3. Прогноз ч/ч: Ученики * Объем программы
                $manHoursCount = $studentsCount * $capacity;

                // Строго под getManHoursProgramReportHeaders()
                $data[] = [
                    (string)$group->number,        // 'Группа'
                    (string)$capacity,            // 'Объем программы (ак. ч.)'
                    (string)$lessonsScheduleCount,// 'Кол-во занятий в расписании'
                    (string)$studentsCount,       // 'Кол-во учеников'
                    (string)$manHoursCount,       // 'Кол-во ч/ч (прогноз)'
                ];
            } else {
                // Старый формат (MAN_HOURS_FAIR / MAN_HOURS_ALL)
                $allThemes = !empty($lessonIds) ? $this->lessonThemeRepository->getByLessonIds($lessonIds) : [];
                $allLessonsCount = count($allThemes);

                $teacherThemesCount = 0;
                $teacherLessonIds = [];
                if (!empty($teacherIds)) {
                    $teacherThemes = $this->lessonThemeRepository->getByTeacherIds($teacherIds);
                    $teacherLessonIds = ArrayHelper::getColumn($teacherThemes, 'training_group_lesson_id');
                    $teacherThemesCount = count(array_intersect($lessonIds, $teacherLessonIds));
                }

                $visits = $this->visitRepository->getByTrainingGroup($group->id);
                $visitsCount = 0;
                foreach ($visits as $visit) {
                    /** @var VisitWork $visit */
                    $lessonsList = VisitLesson::fromString($visit->lessons, $this->lessonRepository);
                    foreach ($lessonsList as $l) {
                        $visitsCount += ReportHelper::checkVisitLesson(
                            $l,
                            $startDate,
                            $endDate,
                            $calculateType,
                            $teacherLessonIds
                        );
                    }
                }

                // Строго под старые заголовки
                $data[] = [
                    (string)$group->number,
                    (string)(!empty($teacherIds) ? $teacherThemesCount : $allLessonsCount),
                    (string)$allLessonsCount,
                    (string)$studentsCount,
                    (string)$visitsCount,
                ];
            }
        }

        return $data;
    }


    /**
     * @param ForeignEventWork[] $events
     * @return string[][]
     */
    public function createEventDebugData(array $events)
    {
        $data = [];
        foreach ($events as $event) {
            $soloParts = $this->actParticipantRepository->getByForeignEventIds([$event->id], [ActParticipantWork::TYPE_SOLO]);
            $teamParts = $this->actParticipantRepository->getByForeignEventIds([$event->id], [ActParticipantWork::TYPE_TEAM]);

            $soloPrizes = $this->participantAchievementRepository->getByForeignEvent($event->id, [ParticipantAchievementWork::TYPE_PRIZE], [ActParticipantWork::TYPE_SOLO]);
            $teamPrizes = $this->participantAchievementRepository->getByForeignEvent($event->id, [ParticipantAchievementWork::TYPE_PRIZE], [ActParticipantWork::TYPE_TEAM]);

            $soloWinners = $this->participantAchievementRepository->getByForeignEvent($event->id, [ParticipantAchievementWork::TYPE_WINNER], [ActParticipantWork::TYPE_SOLO]);
            $teamWinners = $this->participantAchievementRepository->getByForeignEvent($event->id, [ParticipantAchievementWork::TYPE_WINNER], [ActParticipantWork::TYPE_TEAM]);

            $data[] = [
                $event->name,
                $event->organizerWork->name,
                Yii::$app->eventLevel->get($event->level),
                DateFormatter::format($event->begin_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot),
                DateFormatter::format($event->end_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot),
                (string)count($soloParts),
                (string)count($teamParts),
                (string)count($soloPrizes),
                (string)count($teamPrizes),
                (string)count($soloWinners),
                (string)count($teamWinners),
            ];
        }

        return $data;
    }
}