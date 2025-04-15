<?php

namespace common\components\access\pbac;

use backend\builders\TrainingGroupReportBuilder;
use common\components\access\pbac\data\PbacGroupData;
use common\models\work\UserWork;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\general\PeopleStampRepository;
use common\repositories\rubac\UserPermissionFunctionRepository;
use frontend\models\work\rubac\PermissionFunctionWork;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class PbacGroupAccess implements PbacComponentInterface
{
    private PbacGroupData $data;
    private TrainingGroupRepository $groupRepository;
    private PeopleStampRepository $peopleStampRepository;
    private UserPermissionFunctionRepository $permissionFunctionRepository;
    private TrainingGroupReportBuilder $groupBuilder;

    public function __construct(
        PbacGroupData $data
    )
    {
        $this->data = $data;
        $this->groupRepository = Yii::createObject(TrainingGroupRepository::class);
        $this->peopleStampRepository = Yii::createObject(PeopleStampRepository::class);
        $this->permissionFunctionRepository = Yii::createObject(UserPermissionFunctionRepository::class);
        $this->groupBuilder = Yii::createObject(TrainingGroupReportBuilder::class);
    }

    public function getAllowedGroupsQuery(ActiveQuery $query)
    {
        $accessTheirGroups = $this->permissionFunctionRepository->getByUserPermissionBranch($this->data->user->id, PermissionFunctionWork::PERMISSION_THEIR_GROUPS_ID);
        $accessBranchGroups = $this->permissionFunctionRepository->getByUserPermissionBranch($this->data->user->id, PermissionFunctionWork::PERMISSION_BRANCH_GROUPS_ID);
        $accessAllGroups = $this->permissionFunctionRepository->getByUserPermissionBranch($this->data->user->id, PermissionFunctionWork::PERMISSION_ALL_GROUPS_ID);

        if (!$accessAllGroups) {
            if ($accessBranchGroups) {
                $query1 = $this->groupBuilder->filterGroupsByBranches($query, $this->data->branches);
            }
            if ($accessTheirGroups) {
                $stampsId = ArrayHelper::getColumn($this->peopleStampRepository->getByPeopleId($this->data->user->aka), 'id');
                $query2 = $this->groupBuilder->filterGroupsByTeachers($query, $stampsId);
            }

            if (!empty($query1) && !empty($query2)) {
                $query = $query1->union($query2, true);
            } elseif (!empty($query1)) {
                $query = $query1;
            } elseif (!empty($query2)) {
                $query = $query2;
            }
        }

        return $query;
    }

    private function getGroupsByTeacher(UserWork $user)
    {
        if ($user->aka) {
            return $this->groupRepository->getByTeacher($user->aka);
        }

        return [];
    }

    private function getGroupsByBranch(array $branches)
    {
        return $this->groupRepository->getByBranches($branches);
    }

    private function getAllGroups()
    {
        return $this->groupRepository->getAll();
    }
}