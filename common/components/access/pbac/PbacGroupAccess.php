<?php

namespace common\components\access\pbac;

use common\components\access\pbac\data\PbacGroupData;
use common\models\work\UserWork;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\rubac\UserPermissionFunctionRepository;
use frontend\models\work\rubac\PermissionFunctionWork;
use Yii;

class PbacGroupAccess implements PbacComponentInterface
{
    private PbacGroupData $data;
    private TrainingGroupRepository $groupRepository;
    private UserPermissionFunctionRepository $permissionFunctionRepository;

    public function __construct(
        PbacGroupData $data
    )
    {
        $this->data = $data;
        $this->groupRepository = Yii::createObject(TrainingGroupRepository::class);
        $this->permissionFunctionRepository = Yii::createObject(UserPermissionFunctionRepository::class);
    }

    public function getAllowedGroups()
    {
        $accessTheirGroups = $this->permissionFunctionRepository->getByUserPermissionBranch($this->data->user->id, PermissionFunctionWork::PERMISSION_THEIR_GROUPS_ID);
        $accessBranchGroups = $this->permissionFunctionRepository->getByUserPermissionBranch($this->data->user->id, PermissionFunctionWork::PERMISSION_BRANCH_GROUPS_ID);
        $accessAllGroups = $this->permissionFunctionRepository->getByUserPermissionBranch($this->data->user->id, PermissionFunctionWork::PERMISSION_ALL_GROUPS_ID);

        $allowedGroups = [];
        if ($accessTheirGroups) {
            $allowedGroups = array_merge($allowedGroups, $this->getGroupsByTeacher($this->data->user));
        }
        if ($accessBranchGroups) {
            $allowedGroups = array_merge($allowedGroups, $this->getGroupsByBranch($this->data->branches));
        }
        if ($accessAllGroups) {
            $allowedGroups = array_merge($allowedGroups, $this->getAllGroups());
        }

        return $allowedGroups;
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