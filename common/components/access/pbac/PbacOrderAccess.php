<?php

namespace common\components\access\pbac;

use common\components\access\pbac\data\PbacEventData;
use common\components\access\pbac\data\PbacOrderData;
use common\repositories\act_participant\ActParticipantBranchRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\order\DocumentOrderRepository;
use common\repositories\rubac\PermissionFunctionRepository;
use common\repositories\rubac\UserPermissionFunctionRepository;
use frontend\models\work\rubac\PermissionFunctionWork;
use Yii;
use yii\db\ActiveQuery;

class PbacOrderAccess implements PbacComponentInterface
{
    private PbacOrderData $data;
    private DocumentOrderRepository $orderRepository;
    private UserPermissionFunctionRepository $permissionFunctionRepository;

    public function __construct(
        PbacOrderData $data
    )
    {
        $this->data = $data;
        $this->orderRepository = Yii::createObject(DocumentOrderRepository::class);
        $this->permissionFunctionRepository = Yii::createObject(UserPermissionFunctionRepository::class);
    }

    public function getAllowedOrdersQuery(ActiveQuery $query)
    {
        $accessBranchOrders = $this->permissionFunctionRepository->getByUserPermissionBranch($this->data->user->id, PermissionFunctionWork::PERMISSION_BRANCH_ORDER);

        if ($accessBranchOrders) {
            $query = $this->orderRepository->filterByBranch($query, $this->data->branch);
        }

        return $query;
    }
}