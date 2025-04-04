<?php

namespace backend\tests\unit\report\query;

use common\components\dictionaries\base\AllowRemoteDictionary;
use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\EventLevelDictionary;
use common\components\dictionaries\base\FocusDictionary;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\general\UserRepository;
use common\repositories\providers\training_group\TrainingGroupMockProvider;
use common\repositories\providers\user\UserMockProvider;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\team\ActParticipantWork;
use Yii;

class ForeignEventReportData
{
    public array $events = [];
    public array $acts = [];
    public array $actsBranch = [];
    public array $achieves = [];

    public array $expectedValues = [];

    public function __construct()
    {
        $this->fillData();
        $this->fillExpectedValues();
    }

    private function fillData()
    {
        $this->fillEvents();
        $this->fillActs();
        $this->fillActsBranch();
        $this->fillAchieves();
    }

    private function fillExpectedValues()
    {
        $this->expectedValues = [
            [
                'levels' => [
                    EventLevelDictionary::INTERIOR => [
                        'participant' => 3,
                        'winners' => 0,
                        'prizes' => 1
                    ],
                    EventLevelDictionary::DISTRICT => [
                        'participant' => 4,
                        'winners' => 1,
                        'prizes' => 1
                    ],
                    EventLevelDictionary::URBAN => [
                        'participant' => 0,
                        'winners' => 0,
                        'prizes' => 0
                    ],
                    EventLevelDictionary::REGIONAL => [
                        'participant' => 10,
                        'winners' => 3,
                        'prizes' => 3
                    ],
                    EventLevelDictionary::FEDERAL => [
                        'participant' => 2,
                        'winners' => 0,
                        'prizes' => 1
                    ],
                    EventLevelDictionary::INTERNATIONAL => [
                        'participant' => 3,
                        'winners' => 2,
                        'prizes' => 0
                    ]
                ],
                'percent' => 0.6
            ],
            [
                'levels' => [
                    EventLevelDictionary::INTERIOR => [
                        'participant' => 1,
                        'winners' => 0,
                        'prizes' => 0
                    ],
                    EventLevelDictionary::DISTRICT => [
                        'participant' => 3,
                        'winners' => 1,
                        'prizes' => 1
                    ],
                    EventLevelDictionary::URBAN => [
                        'participant' => 0,
                        'winners' => 0,
                        'prizes' => 0
                    ],
                    EventLevelDictionary::REGIONAL => [
                        'participant' => 4,
                        'winners' => 2,
                        'prizes' => 0
                    ],
                    EventLevelDictionary::FEDERAL => [
                        'participant' => 0,
                        'winners' => 0,
                        'prizes' => 0
                    ],
                    EventLevelDictionary::INTERNATIONAL => [
                        'participant' => 3,
                        'winners' => 2,
                        'prizes' => 0
                    ]
                ],
                'percent' => 0.57
            ],
            [
                'levels' => [
                    EventLevelDictionary::INTERIOR => [
                        'participant' => 0,
                        'winners' => 0,
                        'prizes' => 0
                    ],
                    EventLevelDictionary::DISTRICT => [
                        'participant' => 4,
                        'winners' => 1,
                        'prizes' => 1
                    ],
                    EventLevelDictionary::URBAN => [
                        'participant' => 0,
                        'winners' => 0,
                        'prizes' => 0
                    ],
                    EventLevelDictionary::REGIONAL => [
                        'participant' => 6,
                        'winners' => 2,
                        'prizes' => 2
                    ],
                    EventLevelDictionary::FEDERAL => [
                        'participant' => 0,
                        'winners' => 0,
                        'prizes' => 0
                    ],
                    EventLevelDictionary::INTERNATIONAL => [
                        'participant' => 0,
                        'winners' => 0,
                        'prizes' => 0
                    ]
                ],
                'percent' => 0.67
            ],
        ];
    }

    /**
     * Создаем mock-модели для мероприятий
     */
    private function fillEvents()
    {
        $this->events = [
            [
                'id' => 1,
                'start_date' => '2010-01-01',
                'finish_date' => '2010-01-15',
                'level' => EventLevelDictionary::DISTRICT
            ],
            [
                'id' => 2,
                'start_date' => '2010-02-01',
                'finish_date' => '2010-02-15',
                'level' => EventLevelDictionary::INTERIOR
            ],
            [
                'id' => 3,
                'start_date' => '2010-01-01',
                'finish_date' => '2010-01-15',
                'level' => EventLevelDictionary::REGIONAL
            ],
            [
                'id' => 4,
                'start_date' => '2010-02-01',
                'finish_date' => '2010-02-15',
                'level' => EventLevelDictionary::FEDERAL
            ],
            [
                'id' => 5,
                'start_date' => '2010-02-01',
                'finish_date' => '2010-02-15',
                'level' => EventLevelDictionary::REGIONAL
            ],
            [
                'id' => 6,
                'start_date' => '2010-01-01',
                'finish_date' => '2010-01-15',
                'level' => EventLevelDictionary::INTERNATIONAL
            ],
        ];
    }

    /**
     * Создаем mock-модели актов участия
     */
    private function fillActs()
    {
        $this->acts = [
            // Мероприятие 1
            ['id' => 1, 'foreign_event_id' => 1, 'focus' => FocusDictionary::TECHNICAL, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 2, 'foreign_event_id' => 1, 'focus' => FocusDictionary::TECHNICAL, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 3, 'foreign_event_id' => 1, 'focus' => FocusDictionary::TECHNICAL, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 4, 'foreign_event_id' => 1, 'focus' => FocusDictionary::TECHNICAL, 'allow_remote' => AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
            // --------
            // Мероприятие 2
            ['id' => 5, 'foreign_event_id' => 2, 'focus' => FocusDictionary::TECHNICAL, 'allow_remote' => AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
            ['id' => 6, 'foreign_event_id' => 2, 'focus' => FocusDictionary::TECHNICAL, 'allow_remote' => AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
            ['id' => 7, 'foreign_event_id' => 2, 'focus' => FocusDictionary::TECHNICAL, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            // --------
            // Мероприятие 3
            ['id' => 8, 'foreign_event_id' => 3, 'focus' => FocusDictionary::SCIENCE, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 9, 'foreign_event_id' => 3, 'focus' => FocusDictionary::SCIENCE, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 10, 'foreign_event_id' => 3, 'focus' => FocusDictionary::SCIENCE, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 11, 'foreign_event_id' => 3, 'focus' => FocusDictionary::SCIENCE, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 12, 'foreign_event_id' => 3, 'focus' => FocusDictionary::TECHNICAL, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 13, 'foreign_event_id' => 3, 'focus' => FocusDictionary::TECHNICAL, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            // --------
            // Мероприятие 4
            ['id' => 14, 'foreign_event_id' => 4, 'focus' => FocusDictionary::ART, 'allow_remote' => AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
            ['id' => 15, 'foreign_event_id' => 4, 'focus' => FocusDictionary::ART, 'allow_remote' => AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
            // --------
            // Мероприятие 5
            ['id' => 16, 'foreign_event_id' => 5, 'focus' => FocusDictionary::SPORT, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 17, 'foreign_event_id' => 5, 'focus' => FocusDictionary::SPORT, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 18, 'foreign_event_id' => 5, 'focus' => FocusDictionary::SPORT, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 19, 'foreign_event_id' => 5, 'focus' => FocusDictionary::SPORT, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            // --------
            // Мероприятие 6
            ['id' => 20, 'foreign_event_id' => 6, 'focus' => FocusDictionary::TECHNICAL, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 21, 'foreign_event_id' => 6, 'focus' => FocusDictionary::TECHNICAL, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            ['id' => 22, 'foreign_event_id' => 6, 'focus' => FocusDictionary::TECHNICAL, 'allow_remote' => AllowRemoteDictionary::ONLY_PERSONAL],
            // --------
        ];
    }

    /**
     * Создаем mock-модели акт-отдел
     */
    private function fillActsBranch()
    {
        $this->actsBranch = [
            ['id' => 1, 'act_participant_id' => 1, 'branch' => BranchDictionary::TECHNOPARK],
            ['id' => 2, 'act_participant_id' => 2, 'branch' => BranchDictionary::TECHNOPARK],
            ['id' => 3, 'act_participant_id' => 3, 'branch' => BranchDictionary::TECHNOPARK],
            ['id' => 4, 'act_participant_id' => 4, 'branch' => BranchDictionary::TECHNOPARK],
            ['id' => 5, 'act_participant_id' => 5, 'branch' => BranchDictionary::TECHNOPARK],
            ['id' => 6, 'act_participant_id' => 6, 'branch' => BranchDictionary::QUANTORIUM],
            ['id' => 7, 'act_participant_id' => 7, 'branch' => BranchDictionary::QUANTORIUM],
            ['id' => 8, 'act_participant_id' => 8, 'branch' => BranchDictionary::QUANTORIUM],
            ['id' => 9, 'act_participant_id' => 9, 'branch' => BranchDictionary::QUANTORIUM],
            ['id' => 10, 'act_participant_id' => 10, 'branch' => BranchDictionary::QUANTORIUM],
            ['id' => 11, 'act_participant_id' => 11, 'branch' => BranchDictionary::QUANTORIUM],
            ['id' => 12, 'act_participant_id' => 12, 'branch' => BranchDictionary::COD],
            ['id' => 13, 'act_participant_id' => 13, 'branch' => BranchDictionary::COD],
            ['id' => 14, 'act_participant_id' => 14, 'branch' => BranchDictionary::COD],
            ['id' => 15, 'act_participant_id' => 15, 'branch' => BranchDictionary::CDNTT],
            ['id' => 16, 'act_participant_id' => 16, 'branch' => BranchDictionary::CDNTT],
            ['id' => 17, 'act_participant_id' => 17, 'branch' => BranchDictionary::CDNTT],
            ['id' => 18, 'act_participant_id' => 18, 'branch' => BranchDictionary::CDNTT],
            ['id' => 19, 'act_participant_id' => 19, 'branch' => BranchDictionary::MOBILE_QUANTUM],
            ['id' => 20, 'act_participant_id' => 20, 'branch' => BranchDictionary::MOBILE_QUANTUM],
            ['id' => 21, 'act_participant_id' => 21, 'branch' => BranchDictionary::MOBILE_QUANTUM],
            ['id' => 22, 'act_participant_id' => 22, 'branch' => BranchDictionary::MOBILE_QUANTUM],
        ];
    }

    /**
     * Создаем mock-модели достижений
     */
    private function fillAchieves()
    {
        $this->achieves = [
            // Мероприятие 1
            ['id' => 1, 'act_participant_id' => 1, 'type' => ParticipantAchievementWork::TYPE_WINNER],
            ['id' => 2, 'act_participant_id' => 3, 'type' => ParticipantAchievementWork::TYPE_PRIZE],
            // --------
            // Мероприятие 2
            ['id' => 3, 'act_participant_id' => 6, 'type' => ParticipantAchievementWork::TYPE_PRIZE],
            // --------
            // Мероприятие 3
            ['id' => 4, 'act_participant_id' => 8, 'type' => ParticipantAchievementWork::TYPE_WINNER],
            ['id' => 5, 'act_participant_id' => 10, 'type' => ParticipantAchievementWork::TYPE_WINNER],
            ['id' => 6, 'act_participant_id' => 12, 'type' => ParticipantAchievementWork::TYPE_PRIZE],
            ['id' => 7, 'act_participant_id' => 13, 'type' => ParticipantAchievementWork::TYPE_PRIZE],
            // --------
            // Мероприятие 4
            ['id' => 8, 'act_participant_id' => 14, 'type' => ParticipantAchievementWork::TYPE_PRIZE],
            // --------
            // Мероприятие 5
            ['id' => 9, 'act_participant_id' => 16, 'type' => ParticipantAchievementWork::TYPE_WINNER],
            ['id' => 10, 'act_participant_id' => 19, 'type' => ParticipantAchievementWork::TYPE_PRIZE],
            // --------
            // Мероприятие 6
            ['id' => 11, 'act_participant_id' => 21, 'type' => ParticipantAchievementWork::TYPE_WINNER],
            ['id' => 12, 'act_participant_id' => 22, 'type' => ParticipantAchievementWork::TYPE_WINNER],
            // --------
        ];
    }
}