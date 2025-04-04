<?php


namespace frontend\tests\unit\models\training_group;


use common\components\dictionaries\base\BranchDictionary;
use common\repositories\general\UserRepository;
use common\repositories\providers\user\UserMockProvider;
use Yii;

class TrainingGroupCreateData
{
    protected UserRepository $userRepository;

    public $groups;

    public function __construct($params = [])
    {
        $this->userRepository = Yii::createObject(
            UserRepository::class,
            ['provider' => Yii::createObject(UserMockProvider::class)]
        );

        $this->fillGroups();
    }

    private function fillGroups()
    {
        $testUserId = null;
        if (count($this->userRepository->getAll()) > 0) {
            $testUserId = $this->userRepository->getAll()[0]->id;
        }

        $this->groups = [
            [
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
            ],
            [
                'start_date' => '2010-01-01',
                'finish_date' => '2010-04-01',
                'open' => 1,
                'budget' => 1,
                'branch' => BranchDictionary::QUANTORIUM,
                'order_stop' => 1,
                'archive' => 0,
                'protection_date' => '2010-04-01',
                'protection_confirm' => 1,
                'is_network' => 0,
                'state' => 0,
                'created_at' => $testUserId,
                'updated_at' => $testUserId,
            ]
        ];
    }
}