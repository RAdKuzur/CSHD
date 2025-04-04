<?php


namespace frontend\tests\unit\models\training_group;

use common\components\dictionaries\base\BranchDictionary;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;
use common\repositories\general\UserRepository;
use common\repositories\providers\group_expert\TrainingGroupExpertMockProvider;
use common\repositories\providers\group_lesson\TrainingGroupLessonMockProvider;
use common\repositories\providers\group_participant\TrainingGroupParticipantMockProvider;
use common\repositories\providers\group_project_themes\GroupProjectThemesMockProvider;
use common\repositories\providers\teacher_group\TeacherGroupMockProvider;
use common\repositories\providers\user\UserMockProvider;
use common\repositories\providers\visit\VisitMockProvider;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use Yii;

class TrainingGroupFullData
{
    protected UserRepository $userRepository;

    public $group;
    public $teachers;
    public $participants;
    public $lessons;
    public $experts;

    public function __construct($params = [])
    {
        $this->userRepository = Yii::createObject(
            UserRepository::class,
            ['provider' => Yii::createObject(UserMockProvider::class)]
        );

        $this->fillGroup();
    }

    private function fillGroup()
    {
        $testUserId = null;
        if (count($this->userRepository->getAll()) > 0) {
            $testUserId = $this->userRepository->getAll()[0]->id;
        }

        $this->group = [
            'start_date' => '2010-01-01',
            'finish_date' => '2010-04-01',
            'open' => 1,
            'budget' => 1,
            'branch' => BranchDictionary::TECHNOPARK,
            'order_stop' => 1,
            'archive' => 0,
            'protection_date' => '2010-04-01',
            'protection_confirm' => 1,
            'is_network' => 0,
            'state' => 0,
            'created_at' => $testUserId,
            'updated_at' => $testUserId,
        ];

        $this->teachers = [
            [
                'firstname' => 'Иван',
                'surname' => 'Иванов',
                'patronymic' => 'Иванович',
                'company_id' => 1,
                'short' => 'ИИИ1',
                'branch' => BranchDictionary::TECHNOPARK,
                'birthdate' => '2000-01-01',
                'sex' => 0,
            ],
            [
                'firstname' => 'Петр',
                'surname' => 'Петров',
                'patronymic' => 'Петрович',
                'company_id' => 1,
                'short' => 'ППП1',
                'branch' => BranchDictionary::QUANTORIUM,
                'birthdate' => '2001-01-01',
                'sex' => 0,
            ],
            [
                'firstname' => 'Андрей',
                'surname' => 'Андреев',
                'patronymic' => 'Андреевич',
                'company_id' => 1,
                'short' => 'ААА1',
                'branch' => BranchDictionary::CDNTT,
                'birthdate' => '2002-01-01',
                'sex' => 0,
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
            [
                'firstname' => 'Ученица 2 Имя',
                'surname' => 'Ученица 2 Фамилия',
                'patronymic' => 'Ученица 2 Отчество',
                'birthdate' => '2015-02-02',
                'email' => 'test@test.ru',
                'sex' => 1,
            ],
        ];

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
                'branch' => BranchDictionary::QUANTORIUM,
            ],
            [
                'lesson_date' => '2025-02-06',
                'lesson_start_time' => '09:00',
                'lesson_end_time' => '09:50',
                'duration' => 50,
                'branch' => BranchDictionary::QUANTORIUM,
            ],
            [
                'lesson_date' => '2025-02-13',
                'lesson_start_time' => '09:00',
                'lesson_end_time' => '09:50',
                'duration' => 50,
                'branch' => BranchDictionary::QUANTORIUM,
            ],
        ];

        $this->experts = [
            [
                'firstname' => 'Эксперт 1 Имя',
                'surname' => 'Эксперт 1 Фамилия',
                'patronymic' => 'Эксперт 1 Отчество',
                'type' => TrainingGroupExpertWork::TYPE_EXTERNAL,
            ],
            [
                'firstname' => 'Эксперт 2 Имя',
                'surname' => 'Эксперт 2 Фамилия',
                'patronymic' => 'Эксперт 2 Отчество',
                'type' => TrainingGroupExpertWork::TYPE_INTERNAL,
            ],
        ];
    }

}