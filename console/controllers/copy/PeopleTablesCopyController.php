<?php

namespace console\controllers\copy;

use common\repositories\dictionaries\PeopleRepository;
use Yii;
use yii\console\Controller;

class PeopleTablesCopyController extends Controller
{
    private PeopleRepository $peopleRepository;
    public function __construct(
        $id,
        $module,
        PeopleRepository $peopleRepository,
        $config = []
    )
    {
        $this->peopleRepository = $peopleRepository;
        parent::__construct($id, $module, $config);
    }
    public function actionCopyPeoplePositionCompanyBranch(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM people_position_branch");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('people_position_company_branch',
                [
                    'id' => $record['id'],
                    'people_id' => $record['people_id'],
                    'company_id' => ($this->peopleRepository->get($record['people_id']))->company_id,
                    'branch' => $record['branch_id'],
                    'position_id' => $record['position_id'],
                ]

            );
            $command->execute();
        }
    }
    public function actionDeletePeoplePositionCompanyBranch(){
        Yii::$app->db->createCommand()->delete('people_position_company_branch')->execute();
    }
    public function actionDeleteAll(){
        $this->actionDeletePeoplePositionCompanyBranch();
    }
    public function actionCopyAll(){
        $this->actionCopyPeoplePositionCompanyBranch();
    }
}