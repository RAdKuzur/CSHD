<?php

namespace common\repositories\educational;

use frontend\models\work\order\OrderTrainingWork;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\repositories\providers\group_participant\TrainingGroupParticipantProvider;
use common\repositories\providers\group_participant\TrainingGroupParticipantProviderInterface;
use DomainException;
use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Mpdf\Tag\Tr;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class TrainingGroupParticipantRepository
{
    private $provider;
    private OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository;
    public function __construct(
        TrainingGroupParticipantProviderInterface $provider = null,
        OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository = null
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(TrainingGroupParticipantProvider::class);
        }

        $this->provider = $provider;
        $this->orderTrainingGroupParticipantRepository = $orderTrainingGroupParticipantRepository;
    }

    public function findAll(ActiveQuery $query)
    {
        return $query->all();
    }

    public function findOne(ActiveQuery $query)
    {
        return $query->one();
    }

    public function count(ActiveQuery $query)
    {
        return $query->count();
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getByIds(array $ids)
    {
        return $this->provider->getByIds($ids);
    }

    /**
     * @param int[] $ids
     */
    public function getByParticipantIds(array $ids)
    {
        return $this->provider->getByParticipantIds($ids);
    }

    public function getParticipantsFromGroups(array $groupId)
    {
        return $this->provider->getParticipantsFromGroups($groupId);
    }

    public function getSuccessParticipantsFromGroup(int $groupId)
    {
        return $this->provider->getSuccessParticipantsFromGroup($groupId);
    }

    public function getByParticipantIdAndGroupId(int $participantId, int $groupId)
    {
        return $this->provider->getByParticipantIdAndGroupId($participantId, $groupId);
    }

    public function getParticipantsWithoutCertificates(array $groupIds)
    {
        return TrainingGroupParticipantWork::find()
            ->joinWith(['certificatesWork'])
            ->where(['IN', 'training_group_id', $groupIds])
            ->andWhere(['IS', 'certificate.certificate_number', null]);
    }

    public function getQueryCertificateAllowed(int $groupId)
    {
        return TrainingGroupParticipantWork::find()
            ->joinWith(['trainingGroupWork'])
            ->joinWith(['certificateWork'])
            ->where(['training_group.id' => $groupId])
            ->andWhere(['success' => 1])
            ->andWhere(['IS', 'certificate.training_group_participant_id', null]);
    }

    public function prepareCreate($groupId, $participantId, $sendMethod)
    {
        if (get_class($this->provider) == TrainingGroupParticipantProvider::class) {
            return $this->provider->prepareCreate($groupId, $participantId, $sendMethod);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareCreate');
        }
    }

    public function prepareDelete($id)
    {
        if (get_class($this->provider) == TrainingGroupParticipantProvider::class) {
            return $this->provider->prepareDelete($id);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareDelete');
        }
    }

    public function prepareUpdate($id, $participantId, $sendMethod)
    {
        if (get_class($this->provider) == TrainingGroupParticipantProvider::class) {
            return $this->provider->prepareUpdate($id, $participantId, $sendMethod);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareUpdate');
        }
    }

    public function save(TrainingGroupParticipantWork $model)
    {
        return $this->provider->save($model);
    }

    public function delete(TrainingGroupParticipantWork $model)
    {
        return $this->provider->delete($model);
    }

    public function getAll(array $ids = [])
    {
        return count($ids) > 0 ? TrainingGroupParticipantWork::find()->where(['IN', 'id', $ids])->all() : TrainingGroupParticipantWork::find()->all();
    }

    public function empty()
    {
        return TrainingGroupParticipantWork::find()->where(['id' => 0]);
    }

    public function getParticipantsToEnrollCreate($groupIds)
    {
        return TrainingGroupParticipantWork::find()->andWhere(['training_group_id' => $groupIds])->andWhere(['status' => NomenclatureDictionary::ORDER_INIT]);
    }

    public function getParticipantsToDeductCreate($groupIds)
    {
        return TrainingGroupParticipantWork::find()->andWhere(['training_group_id' => $groupIds])->andWhere(['status' => NomenclatureDictionary::ORDER_ENROLL]);
    }

    public function getParticipantsToTransferCreate($groupIds)
    {
        return TrainingGroupParticipantWork::find()->andWhere(['training_group_id' => $groupIds])->andWhere(['status' => NomenclatureDictionary::ORDER_ENROLL]);
    }

    public function getParticipantToEnrollUpdate($groupId, $orderId){
        $orderParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
            ->andWhere(['order_id' => $orderId])->andWhere(['training_group_participant_out_id' => NULL])
            ->all(),
            'training_group_participant_in_id');
        $query = TrainingGroupParticipantWork::find()
            ->orWhere(['id' => $orderParticipantId])
            ->orWhere(['and', ['training_group_id' => $groupId], ['status' => NomenclatureDictionary::ORDER_INIT]]);
        return $query;
    }

    public function getParticipantToDeductUpdate($groupId, $orderId){
        $orderParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
            ->andWhere(['order_id' => $orderId])->andWhere(['training_group_participant_in_id' => NULL])
            ->all(),
            'training_group_participant_out_id');
        $query = TrainingGroupParticipantWork::find()
            ->orWhere(['id' => $orderParticipantId])
            ->orWhere(['and', ['training_group_id' => $groupId], ['status' => NomenclatureDictionary::ORDER_ENROLL]]);
        return $query;
    }

    public function getParticipantToTransferUpdate($groupId, $orderId)
    {
        $orderParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
            ->andWhere(['order_id' => $orderId])->andWhere(['IS NOT', 'training_group_participant_out_id', NULL])
            ->andWhere(['IS NOT', 'training_group_participant_in_id', NULL])
            ->all(),
            'training_group_participant_out_id');
        $exceptParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
            ->andWhere(['order_id' => $orderId])->andWhere(['IS NOT', 'training_group_participant_out_id', NULL])
            ->andWhere(['IS NOT', 'training_group_participant_in_id', NULL])
            ->all(),
            'training_group_participant_in_id');

        $query = TrainingGroupParticipantWork::find()
            ->orWhere(['id' => $orderParticipantId])
            ->orWhere(['and', ['training_group_id' => $groupId], ['status' => 1]]);
        $query = $query->andWhere(['not in', 'id', $exceptParticipantId]);
        return $query;
    }

    public function setStatus($id, $status){
        $model = TrainingGroupParticipantWork::findOne($id);
        $model->setStatus($status);
        return $this->save($model);
    }

    public function isExist($groupId, $participantId)
    {
        return TrainingGroupParticipantWork::find()
            ->andWhere(['participant_id' => $participantId])
            ->andWhere(['training_group_id' => $groupId])
            ->andWhere(['status' => NomenclatureDictionary::ORDER_ENROLL])
            ->exists();
    }

    public function getAttachedParticipantByOrder($orderId, $status){
        if ($status == NomenclatureDictionary::ORDER_ENROLL){
            $participants = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($orderId), 'training_group_participant_in_id');
        }
        else if ($status == NomenclatureDictionary::ORDER_DEDUCT) {
            $participants = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($orderId), 'training_group_participant_out_id');
        }
        else if ($status == NomenclatureDictionary::ORDER_TRANSFER) {
            $participants = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($orderId), 'training_group_participant_out_id');
        }
        return $participants;
    }

    public function prepareUpdateByStatus($id, $status)
    {
        $command = Yii::$app->db->createCommand();
        $command->update(TrainingGroupParticipantWork::tableName(), ['status' => $status], ['id' => $id]);
        return $command->getRawSql();
    }

    public function getEnrolledParticipantsFromGroup(int $groupId)
    {
        return TrainingGroupParticipantWork::find()
            ->where(['training_group_id' => $groupId])
            ->andWhere(['status' => NomenclatureDictionary::ORDER_ENROLL])
            ->all();
    }
}