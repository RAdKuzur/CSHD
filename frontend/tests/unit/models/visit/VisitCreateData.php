<?php


namespace frontend\tests\unit\models\visit;


use common\components\dictionaries\base\BranchDictionary;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\general\UserRepository;
use common\repositories\providers\group_lesson\TrainingGroupLessonMockProvider;
use common\repositories\providers\group_participant\TrainingGroupParticipantMockProvider;
use common\repositories\providers\user\UserMockProvider;
use frontend\models\work\educational\journal\ParticipantLessons;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use Yii;

class VisitCreateData
{
    /**
     * @var ParticipantLessons[] $baseParticipantLessons
     * @var ParticipantLessons[] $advancedParticipantLessons
     */
    public array $baseParticipantLessons;
    public array $advancedParticipantLessons;

    public $groupId;
    public $lessons;
    public $participants;

    public function __construct($params = [])
    {
        $this->groupId = 1;
        $this->fillLessonsAndParticipants();
        $this->fillBaseParticipantLessons();
        $this->fillAdvancedParticipantLessons();
    }

    private function fillLessonsAndParticipants()
    {
        $this->lessons = [
            [
                'lesson_date' => '2025-01-09',
                'lesson_start_time' => '08:00',
                'lesson_end_time' => '08:40',
                'duration' => 40,
                'branch' => BranchDictionary::TECHNOPARK,
            ],
            [
                'lesson_date' => '2025-01-16',
                'lesson_start_time' => '08:00',
                'lesson_end_time' => '08:40',
                'duration' => 40,
                'branch' => BranchDictionary::TECHNOPARK,
            ],
            [
                'lesson_date' => '2025-01-23',
                'lesson_start_time' => '08:00',
                'lesson_end_time' => '08:40',
                'duration' => 40,
                'branch' => BranchDictionary::TECHNOPARK,
            ],
            [
                'lesson_date' => '2025-01-30',
                'lesson_start_time' => '09:00',
                'lesson_end_time' => '09:40',
                'duration' => 40,
                'branch' => BranchDictionary::TECHNOPARK,
            ],
            [
                'lesson_date' => '2025-02-06',
                'lesson_start_time' => '09:00',
                'lesson_end_time' => '09:50',
                'duration' => 40,
                'branch' => BranchDictionary::TECHNOPARK,
            ],
        ];

        $this->participants = [
            [
                'firstname' => 'Ученик 1 Имя',
                'surname' => 'Ученик 1 Фамилия',
                'patronymic' => 'Ученик 1 Отчество',
                'birthdate' => '2010-01-01',
                'email' => 'test@test.ru',
                'sex' => 0,
            ],
            [
                'firstname' => 'Ученик 2 Имя',
                'surname' => 'Ученик 2 Фамилия',
                'patronymic' => 'Ученик 2 Отчество',
                'birthdate' => '2010-02-02',
                'email' => 'test@test.ru',
                'sex' => 0,
            ],
            [
                'firstname' => 'Ученица 1 Имя',
                'surname' => 'Ученица 1 Фамилия',
                'patronymic' => 'Ученица 1 Отчество',
                'birthdate' => '2015-01-01',
                'email' => 'test@test.ru',
                'sex' => 1,
            ],
        ];
    }

    private function fillBaseParticipantLessons()
    {
        $repositoryLesson = Yii::createObject(
            TrainingGroupLessonRepository::class,
            ['provider' => Yii::createObject(TrainingGroupLessonMockProvider::class)]
        );

        $repositoryParticipant = Yii::createObject(
            TrainingGroupParticipantRepository::class,
            ['provider' => Yii::createObject(TrainingGroupParticipantMockProvider::class)]
        );

        $this->baseParticipantLessons = [
            new ParticipantLessons(
                0,
                [
                    new VisitLesson(0, VisitWork::NONE),
                    new VisitLesson(1, VisitWork::NONE),
                    new VisitLesson(2, VisitWork::NONE),
                    new VisitLesson(3, VisitWork::NONE),
                    new VisitLesson(4, VisitWork::NONE),
                ],
                null,
                null,
                null,
                $repositoryParticipant
            ),
            new ParticipantLessons(
                1,
                [
                    new VisitLesson(0, VisitWork::NONE),
                    new VisitLesson(1, VisitWork::NONE),
                    new VisitLesson(2, VisitWork::NONE),
                    new VisitLesson(3, VisitWork::NONE),
                    new VisitLesson(4, VisitWork::NONE),
                ],
                null,
                null,
                null,
                $repositoryParticipant
            ),
            new ParticipantLessons(
                2,
                [
                    new VisitLesson(0, VisitWork::NONE),
                    new VisitLesson(1, VisitWork::NONE),
                    new VisitLesson(2, VisitWork::NONE),
                    new VisitLesson(3, VisitWork::NONE),
                    new VisitLesson(4, VisitWork::NONE),
                ],
                null,
                null,
                null,
                $repositoryParticipant
            )
        ];
    }

    private function fillAdvancedParticipantLessons()
    {
        $repositoryLesson = Yii::createObject(
            TrainingGroupLessonRepository::class,
            ['provider' => Yii::createObject(TrainingGroupLessonMockProvider::class)]
        );

        $repositoryParticipant = Yii::createObject(
            TrainingGroupParticipantRepository::class,
            ['provider' => Yii::createObject(TrainingGroupParticipantMockProvider::class)]
        );

        $this->advancedParticipantLessons = [
            new ParticipantLessons(
                0,
                [
                    new VisitLesson(0, VisitWork::ATTENDANCE),
                    new VisitLesson(3, VisitWork::ATTENDANCE),
                    new VisitLesson(2, VisitWork::ATTENDANCE),
                    new VisitLesson(1, VisitWork::NO_ATTENDANCE),
                    new VisitLesson(4, VisitWork::DISTANCE),
                ],
                null,
                null,
                null,
                $repositoryParticipant
            ),
            new ParticipantLessons(
                1,
                [
                    new VisitLesson(4, VisitWork::ATTENDANCE),
                    new VisitLesson(1, VisitWork::ATTENDANCE),
                    new VisitLesson(2, VisitWork::ATTENDANCE),
                    new VisitLesson(0, VisitWork::ATTENDANCE),
                    new VisitLesson(3, VisitWork::ATTENDANCE),
                ],
                null,
                null,
                null,
                $repositoryParticipant
            ),
            new ParticipantLessons(
                2,
                [
                    new VisitLesson(0, VisitWork::DISTANCE),
                    new VisitLesson(2, VisitWork::DISTANCE),
                    new VisitLesson(1, VisitWork::DISTANCE),
                    new VisitLesson(4, VisitWork::NO_ATTENDANCE),
                    new VisitLesson(3, VisitWork::ATTENDANCE),
                ],
                null,
                null,
                null,
                $repositoryParticipant
            )
        ];
    }
}