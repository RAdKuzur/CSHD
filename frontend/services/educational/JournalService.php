<?php

namespace frontend\services\educational;

use common\components\compare\LessonGroupCompare;
use common\components\compare\ParticipantGroupCompare;
use common\components\traits\Math;
use common\repositories\educational\LessonThemeRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;
use common\services\general\PeopleStampService;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use frontend\models\work\educational\training_group\LessonThemeWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use yii\helpers\ArrayHelper;

class JournalService
{
    const JOURNAL_EMPTY = 0;
    const JOURNAL_EXIST = 1;

    private VisitRepository $visitRepository;
    private TrainingGroupLessonRepository $lessonRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private LessonThemeRepository $lessonThemeRepository;
    private PeopleStampService $peopleStampService;

    public function __construct(
        VisitRepository $visitRepository,
        TrainingGroupLessonRepository $lessonRepository,
        TrainingGroupParticipantRepository $participantRepository,
        LessonThemeRepository $lessonThemeRepository,
        PeopleStampService $peopleStampService
    )
    {
        $this->visitRepository = $visitRepository;
        $this->lessonRepository = $lessonRepository;
        $this->participantRepository = $participantRepository;
        $this->lessonThemeRepository = $lessonThemeRepository;
        $this->peopleStampService = $peopleStampService;
    }

    public function checkJournalStatus($groupId)
    {
        $visits = $this->visitRepository->getByTrainingGroup($groupId);
        if (count($visits) > 0) {
            return self::JOURNAL_EXIST;
        }
        else {
            return self::JOURNAL_EMPTY;
        }
    }

    /**
     * @param $groupId
     */
    public function createJournal($groupId)
    {
        $lessons = $this->lessonRepository->getLessonsFromGroup($groupId);
        $participants = $this->participantRepository->getParticipantsFromGroups([$groupId]);

        // Конвертируем занятия
        $newLessons = [];
        foreach ($lessons as $lesson) {
            $newLessons[] = new VisitLesson(
                $lesson->id,
                VisitWork::NONE,
                $this->lessonRepository->get($lesson->id)
            );
        }

        //$this->deleteJournal($groupId);

        // Создаем новый журнал
        foreach ($participants as $participant) {
            $visit = VisitWork::fill(
                $participant->id,
                TrainingGroupLessonWork::convertLessonsToJson($newLessons) ? : ''
            );
            $this->visitRepository->save($visit);
        }

        return true;
    }

    public function deleteJournal($groupId)
    {
        // Удаляем существующий журнал
        $visits = $this->visitRepository->getByTrainingGroup($groupId);
        foreach ($visits as $visit) {
            $this->visitRepository->delete($visit);
        }

        return true;
    }

    /**
     * @param $groupId
     * @param TrainingGroupLessonWork[] $addLessons
     * @param TrainingGroupLessonWork[] $delLessons
     * @return false|string
     */
    public function createLessonString($groupId, array $addLessons, array $delLessons)
    {
        $curLessonsString = $this->visitRepository->getByTrainingGroup($groupId)[0]->lessons;
        $curLessonsJson = json_decode($curLessonsString);

        $delLessonIds = array_map(function ($lesson) {
            /** @var TrainingGroupLessonWork $lesson */
            return $lesson->id;
        }, $delLessons);

        if (!empty($delLessonIds)) {
            $curLessonsJson = array_filter($curLessonsJson, function ($lesson) use ($delLessonIds) {
                return !in_array($lesson->lesson_id, $delLessonIds);
            });
        }

        foreach ($addLessons as $lesson) {
            $visitLesson = new VisitLesson(
                $lesson->id,
                VisitWork::NONE,
                $this->lessonRepository->get($lesson->id)
            );
            $curLessonsJson[] = json_decode((string)$visitLesson, true);
        }

        return json_encode(array_values($curLessonsJson));
    }

    /**
     * Изменение статуса одного занятия
     * @param $trainingGroupParticipantId
     * @param $lessonId
     * @param $status
     */
    public function setVisitStatusSingle($trainingGroupParticipantId, $lessonId, $status)
    {
        $visit = $this->visitRepository->getByTrainingGroupParticipants([$trainingGroupParticipantId]);
        if ($visit) {
            $allLessons = VisitLesson::fromString($visit->lessons);
            $newLessons = [];
            // здесь не используем VisitLesson::toString, в угоду однопроходности и оптимизации
            foreach ($allLessons as $lesson) {
                /** @var VisitLesson $lesson */
                if ($lesson->lessonId == $lessonId) {
                    $lesson->status = $status;
                }

                $newLessons[] = (string)$lesson;
            }

            $visit->setLessons('['.(implode(",", $newLessons)).']');
            $this->visitRepository->save($visit);
        }
    }

    /**
     * Изменение статуса для всех занятий у одного ученика в группе (основная функция для сохранения журнала)
     * @param $trainingGroupParticipantId
     * @param VisitLesson[] $statuses
     * @return int
     */
    public function setVisitStatusParticipant($trainingGroupParticipantId, array $statuses)
    {
        /** @var VisitWork $visit */
        $visit = $this->visitRepository->getByTrainingGroupParticipant($trainingGroupParticipantId);
        $lessons = VisitLesson::fromString($visit->lessons, $this->lessonRepository);
        $lessonsString = $visit->lessons;
        if (VisitLesson::equalArrays($lessons, $statuses)) {
            $lessonsString = VisitLesson::toString($statuses);
        }

        $visit->lessons = $lessonsString;
        return $this->visitRepository->save($visit);
    }

    /**
     * Удаляет занятие из журнала
     * * note: затратная по памяти функция, использовать осторожно
     * @param $groupId
     * @param $lessonId
     */
    public function deleteLessonFromGroup($groupId, $lessonId)
    {
        $visits = $this->visitRepository->getByTrainingGroup($groupId);
        foreach ($visits as $visit) {
            /** @var VisitWork $visit */
            $lessons = VisitLesson::fromString($visit->lessons, $this->lessonRepository);
            $lessons = array_filter($lessons, function($lesson) use ($lessonId) {
                /** @var VisitLesson $lesson */
                return $lesson->lessonId !== $lessonId;
            });
            $visit->lessons = VisitLesson::toString($lessons);
            $this->visitRepository->save($visit);
        }
    }

    /**
     * @param int $trainingGroupParticipantId
     * @param int|null $groupProjectThemeId
     * @param int|null $points
     * @param int|null $successFinishing
     * @return int
     */
    public function setParticipantFinishData(
        int $trainingGroupParticipantId,
        ?int $groupProjectThemeId,
        ?int $points,
        ?int $successFinishing
    ) : int
    {
        /** @var TrainingGroupParticipantWork $participant */
        $participant = $this->participantRepository->get($trainingGroupParticipantId);
        $participant->group_project_themes_id = $groupProjectThemeId;
        $participant->points = $points;
        $participant->success = $successFinishing;
        return $this->participantRepository->save($participant);
    }

    /**
     * @param LessonThemeWork[] $lessonThemes
     * @return void
     */
    public function saveThematicPlan(array $lessonThemes)
    {
        foreach ($lessonThemes as $lessonTheme) {
            $lessonTheme->teacher_id = $this->peopleStampService->createStampFromPeople($lessonTheme->teacher_id);
            $this->lessonThemeRepository->save($lessonTheme);
        }
    }

    public function deleteThematicPlan(int $groupId)
    {
        $themes = $this->lessonThemeRepository->getByTrainingGroupId($groupId);
        foreach ($themes as $theme) {
            $this->lessonThemeRepository->delete($theme);
        }
    }
}