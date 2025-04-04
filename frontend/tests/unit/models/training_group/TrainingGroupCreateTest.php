<?php

namespace frontend\tests\unit\models\training_group;

use common\models\scaffold\People;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\VisitRepository;
use common\repositories\providers\group_expert\TrainingGroupExpertMockProvider;
use common\repositories\providers\group_lesson\TrainingGroupLessonMockProvider;
use common\repositories\providers\group_participant\TrainingGroupParticipantMockProvider;
use common\repositories\providers\group_project_themes\GroupProjectThemesMockProvider;
use common\repositories\providers\participant\ParticipantMockProvider;
use common\repositories\providers\people\PeopleMockProvider;
use common\repositories\providers\teacher_group\TeacherGroupMockProvider;
use common\repositories\providers\training_group\TrainingGroupMockProvider;
use common\repositories\providers\visit\VisitMockProvider;
use Exception;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\general\PeopleWork;
use Yii;

class TrainingGroupCreateTest extends \Codeception\Test\Unit
{
    protected TrainingGroupRepository $groupRepository;
    protected GroupProjectThemesRepository $groupProjectThemesRepository;
    protected TeacherGroupRepository $teacherGroupRepository;
    protected TrainingGroupExpertRepository $groupExpertRepository;
    protected TrainingGroupLessonRepository $groupLessonRepository;
    protected TrainingGroupParticipantRepository $groupParticipantRepository;
    protected PeopleRepository $peopleRepository;
    protected ForeignEventParticipantsRepository $participantsRepository;
    protected VisitRepository $visitRepository;

    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;

    protected $groupId;
    
    protected function _before()
    {
        $this->groupRepository = Yii::createObject(
            TrainingGroupRepository::class,
            ['groupProvider' => Yii::createObject(TrainingGroupMockProvider::class)]
        );

        $this->groupProjectThemesRepository = Yii::createObject(
            GroupProjectThemesRepository::class,
            ['provider' => Yii::createObject(GroupProjectThemesMockProvider::class)]
        );

        $this->teacherGroupRepository = Yii::createObject(
            TeacherGroupRepository::class,
            ['provider' => Yii::createObject(TeacherGroupMockProvider::class)]
        );

        $this->groupExpertRepository = Yii::createObject(
            TrainingGroupExpertRepository::class,
            ['provider' => Yii::createObject(TrainingGroupExpertMockProvider::class)]
        );

        $this->groupLessonRepository = Yii::createObject(
            TrainingGroupLessonRepository::class,
            ['provider' => Yii::createObject(TrainingGroupLessonMockProvider::class)]
        );

        $this->groupParticipantRepository = Yii::createObject(
            TrainingGroupParticipantRepository::class,
            ['provider' => Yii::createObject(TrainingGroupParticipantMockProvider::class)]
        );

        $this->peopleRepository = Yii::createObject(
            PeopleRepository::class,
            ['provider' => Yii::createObject(PeopleMockProvider::class)]
        );

        $this->participantsRepository = Yii::createObject(
            ForeignEventParticipantsRepository::class,
            ['provider' => Yii::createObject(ParticipantMockProvider::class)]
        );

        $this->visitRepository = Yii::createObject(
            VisitRepository::class,
            ['provider' => Yii::createObject(VisitMockProvider::class)]
        );
    }

    protected function _after()
    {
    }

    // Тестируем создание базовых учебных групп

    /**
     * @dataProvider getCreateGroupData
     */
    public function testCreateGroup(TrainingGroupCreateData $data)
    {
        $groups = $data->groups;

        if (is_array($groups)) {
            foreach ($groups as $item) {
                try {
                    $group = TrainingGroupWork::fill(
                        $item['start_date'],
                        $item['finish_date'],
                        $item['open'],
                        $item['budget'],
                        $item['branch'],
                        $item['order_stop'],
                        $item['archive'],
                        $item['protection_date'],
                        $item['protection_confirm'],
                        $item['is_network'],
                        $item['state'],
                        $item['created_at'],
                        $item['updated_at']
                    );

                    $this->groupId = $this->groupRepository->save($group);
                    $this->assertNotNull($this->groupId, 'Group ID не может быть NULL');
                }
                catch (Exception $exception) {
                    $this->fail('Ошибка сохранения группы: ' . $exception->getMessage());
                }
            }
        }
        else {
            $this->fail('Ошибка провайдера данных');
        }
    }

    public function getCreateGroupData()
    {
        $data = new TrainingGroupCreateData();

        return [
            [
                $data
            ],
        ];
    }

    // Тестируем создание одной учебной группы вместе со всеми связанными данными

    /**
     * @dataProvider getFullGroupData
     */
    public function testFullGroup(TrainingGroupFullData $data)
    {
        try {
            $teacherIds = [];
            foreach ($data->teachers as $people) {
                $teacherIds[] = $this->peopleRepository->save(
                    PeopleWork::fill(
                        $people['firstname'],
                        $people['surname'],
                        $people['patronymic']
                    )
                );
            }

            $groupId = $this->groupRepository->save(
                TrainingGroupWork::fill(
                    $data->group['start_date'],
                    $data->group['finish_date'],
                    $data->group['open'],
                    $data->group['budget'],
                    $data->group['branch'],
                    $data->group['order_stop'],
                    $data->group['archive'],
                    $data->group['protection_date'],
                    $data->group['protection_confirm'],
                    $data->group['is_network'],
                    $data->group['state'],
                    $data->group['created_at'],
                    $data->group['updated_at']
                )
            );

            $lessonIds = [];
            foreach ($data->lessons as $lesson) {
                $lessonIds[] = $this->groupLessonRepository->save(
                    TrainingGroupLessonWork::fill(
                        $groupId,
                        $lesson['lesson_date'],
                        $lesson['lesson_start_time'],
                        $lesson['branch'],
                        null,
                        $lesson['lesson_end_time'],
                        $lesson['duration']
                    )
                );
            }

            $participantIds = [];
            foreach ($data->participants as $participant) {
                $participantId = $this->participantsRepository->save(
                    ForeignEventParticipantsWork::fill(
                        $participant['firstname'],
                        $participant['surname'],
                        $participant['birthdate'],
                        $participant['email'],
                        $participant['sex'],
                        $participant['patronymic']
                    )
                );

                $participantIds[] = $this->groupParticipantRepository->save(
                    TrainingGroupParticipantWork::fill(
                        $groupId,
                        $participantId,
                        null
                    )
                );
            }

            $expertIds = [];
            foreach ($data->experts as $expert) {
                $expertId = $this->peopleRepository->save(
                    PeopleWork::fill(
                        $expert['firstname'],
                        $expert['surname'],
                        $expert['patronymic']
                    )
                );

                $expertIds[] = $this->groupExpertRepository->save(
                    TrainingGroupExpertWork::fill(
                        $groupId,
                        $expertId,
                        $expert['type']
                    )
                );
            }

            $this->assertNotNull($groupId, 'Ошибка сохранения группы');
            $this->assertNotContains(null, $teacherIds, 'Найден null в массиве teacherIds');
            $this->assertNotContains(null, $lessonIds, 'Найден null в массиве lessonIds');
            $this->assertNotContains(null, $participantIds, 'Найден null в массиве participantIds');
            $this->assertNotContains(null, $expertIds, 'Найден null в массиве expertIds');
        }
        catch (Exception $exception) {
            $this->fail('Ошибка сохранения группы: ' . $exception->getMessage());
        }
    }

    public function getFullGroupData()
    {
        $data = new TrainingGroupFullData();

        return [
            [
                $data
            ],
        ];
    }
}