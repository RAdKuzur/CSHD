<?php

namespace backend\services\report;

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
        $data = [];
        foreach ($participants as $participant) {
            $data[] = DebugReportHelper::createParticipantsDataCsv($participant);
        }

        return $data;
    }

    /**
     * @param TrainingGroupWork[] $groups
     * @param string $startDate
     * @param string $endDate
     * @param int $calculateType
     * @param int[] $teacherIds
     * @return string[][]
     */
    public function createManHoursDebugData(array $groups, string $startDate, string $endDate, int $calculateType, array $teacherIds = []) : array
    {
        $data = [];
        foreach ($groups as $group) {
            $lessons = $this->groupRepository->getLessons($group->id);
            $allLessons = $this->lessonThemeRepository->getByLessonIds(ArrayHelper::getColumn($lessons, 'id'));
            $teacherLesson = $this->lessonThemeRepository->getByTeacherIds($teacherIds);

            $visits = $this->visitRepository->getByTrainingGroup($group->id);
            $visitsCount = 0;
            foreach ($visits as $visit) {
                /** @var VisitWork $visit */
                $lessons = VisitLesson::fromString($visit->lessons, $this->lessonRepository);
                foreach ($lessons as $lesson) {
                    $visitsCount += ReportHelper::checkVisitLesson($lesson, $startDate, $endDate, $calculateType, ArrayHelper::getColumn($teacherLesson, 'id'));
                }
            }

            $data[] = [
                addslashes($group->number),
                count($teacherLesson) > 0 ?: count($allLessons),
                count($allLessons),
                count($this->participantRepository->getParticipantsFromGroups([$group->id])),
                addslashes((string)$visitsCount),
            ];
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