<?php

namespace frontend\tests\unit\models\visit;

use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;
use common\repositories\providers\group_lesson\TrainingGroupLessonMockProvider;
use common\repositories\providers\group_participant\TrainingGroupParticipantMockProvider;
use common\repositories\providers\participant\ParticipantProvider;
use common\repositories\providers\visit\VisitMockProvider;
use Exception;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\journal\ParticipantLessons;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\services\educational\JournalService;
use Yii;

class VisitCreateTest extends \Codeception\Test\Unit
{
    protected JournalService $journalService;
    protected TrainingGroupParticipantRepository $groupParticipantRepository;
    protected ForeignEventParticipantsRepository $participantRepository;
    protected TrainingGroupLessonRepository $lessonRepository;
    protected VisitRepository $visitRepository;

    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        $this->lessonRepository = Yii::createObject(TrainingGroupLessonRepository::class, ['provider' => Yii::createObject(TrainingGroupLessonMockProvider::class)]);
        $this->groupParticipantRepository = Yii::createObject(TrainingGroupParticipantRepository::class, ['provider' => Yii::createObject(TrainingGroupParticipantMockProvider::class)]);
        $this->participantRepository = Yii::createObject(ForeignEventParticipantsRepository::class, ['provider' => Yii::createObject(ParticipantProvider::class)]);
        $this->visitRepository = Yii::createObject(VisitRepository::class, ['provider' => Yii::createObject(VisitMockProvider::class)]);

        $this->journalService = Yii::createObject(
            JournalService::class,
            [
                'visitRepository' => $this->visitRepository,
                'lessonRepository' => $this->lessonRepository,
                'participantRepository' => $this->groupParticipantRepository,
            ]
        );
    }

    protected function _after()
    {
    }

    /**
     * @dataProvider getCreateVisitData
     */
    public function testBaseCreateVisit(VisitCreateData $data)
    {
        $base = $data->baseParticipantLessons;

        try {
            // Подготовка данных: сохранение учеников и занятий в группе
            foreach ($data->lessons as $lesson) {
                $this->lessonRepository->save(
                    TrainingGroupLessonWork::fill(
                        $data->groupId,
                        $lesson['lesson_date'],
                        $lesson['lesson_start_time'],
                        $lesson['branch'],
                        null,
                        $lesson['lesson_end_time'],
                        $lesson['duration']
                    )
                );
            }

            foreach ($data->participants as $participant) {
                $participantId = $this->participantRepository->save(
                    ForeignEventParticipantsWork::fill(
                        $participant['firstname'],
                        $participant['surname'],
                        $participant['birthdate'],
                        $participant['email'],
                        $participant['sex'],
                        $participant['patronymic']
                    )
                );

                $this->groupParticipantRepository->save(
                    TrainingGroupParticipantWork::fill(
                        $data->groupId,
                        $participantId,
                        null
                    )
                );
            }

            $this->journalService->createJournal($data->groupId);
            $visitIds = [];
            foreach ($base as $one) {
                /** @var ParticipantLessons $one */
                $visitIds[] = $this->journalService->setVisitStatusParticipant($one->trainingGroupParticipantId, $one->lessonIds);
            }

            $this->assertNotContains(null, $visitIds, 'Найден null в массиве visitIds');
        }
        catch (Exception $exception) {
            $this->fail('Ошибка сохранения явок: ' . $exception->getMessage());
        }
    }

    /**
     * @dataProvider getCreateVisitData
     */
    public function testAdvancedCreateVisit(VisitCreateData $data)
    {
        $base = $data->advancedParticipantLessons;

        try {
            // Подготовка данных: сохранение учеников и занятий в группе
            foreach ($data->lessons as $lesson) {
                $this->lessonRepository->save(
                    TrainingGroupLessonWork::fill(
                        $data->groupId,
                        $lesson['lesson_date'],
                        $lesson['lesson_start_time'],
                        $lesson['branch'],
                        null,
                        $lesson['lesson_end_time'],
                        $lesson['duration']
                    )
                );
            }

            foreach ($data->participants as $participant) {
                $participantId = $this->participantRepository->save(
                    ForeignEventParticipantsWork::fill(
                        $participant['firstname'],
                        $participant['surname'],
                        $participant['birthdate'],
                        $participant['email'],
                        $participant['sex'],
                        $participant['patronymic']
                    )
                );

                $this->groupParticipantRepository->save(
                    TrainingGroupParticipantWork::fill(
                        $data->groupId,
                        $participantId,
                        null
                    )
                );
            }

            $this->journalService->createJournal($data->groupId);
            $visitIds = [];
            foreach ($base as $one) {
                /** @var ParticipantLessons $one */
                $visitIds[] = $this->journalService->setVisitStatusParticipant($one->trainingGroupParticipantId, $one->lessonIds);
            }

            $this->assertNotContains(null, $visitIds, 'Найден null в массиве visitIds');
        }
        catch (Exception $exception) {
            $this->fail('Ошибка сохранения явок: ' . $exception->getMessage());
        }
    }

    public function getCreateVisitData()
    {
        $data = new VisitCreateData();

        return [
            [
                $data
            ],
        ];
    }
}