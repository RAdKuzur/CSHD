<?php

namespace frontend\services\order;

use common\helpers\StringFormatter;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use frontend\events\educational\training_group\CreateOrderTrainingGroupParticipantEvent;
use frontend\events\educational\training_group\DeleteOrderTrainingGroupParticipantEvent;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\general\OrderPeopleWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderTrainingWork;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\services\general\files\FileService;
use frontend\events\educational\training_group\DeleteTrainingGroupParticipantEvent;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

class OrderTrainingService
{
    private FileService $fileService;
    private OrderMainFileNameGenerator $filenameGenerator;

    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    private TrainingGroupRepository $trainingGroupRepository;
    private OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository;
    private ForeignEventParticipantsRepository $foreignEventParticipantsRepository;
    public function __construct(
        FileService $fileService,
        OrderMainFileNameGenerator $filenameGenerator,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        TrainingGroupRepository $trainingGroupRepository,
        OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository,
        ForeignEventParticipantsRepository $foreignEventParticipantsRepository

    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
        $this->trainingGroupParticipantRepository = $trainingGroupParticipantRepository;
        $this->trainingGroupRepository = $trainingGroupRepository;
        $this->orderTrainingGroupParticipantRepository = $orderTrainingGroupParticipantRepository;
        $this->foreignEventParticipantsRepository = $foreignEventParticipantsRepository;

    }
    public function setBranch(OrderTrainingWork $model)
    {
        $number = $model->order_number;
        $parts = explode("/", $number);
        $nomenclature = $parts[0];
        $model->setBranch(NomenclatureDictionary::getBranchByNomenclature($nomenclature));
    }
    public function getStatus(OrderTrainingWork $model)
    {
        $number = $model->order_number;
        $parts = explode("/", $number);
        $nomenclature = $parts[0];
        return NomenclatureDictionary::getStatus($nomenclature);
    }

    public function getGroupTable(OrderTrainingWork $model)
    {
        /* @var $order OrderTrainingGroupParticipantWork */
        /* @var $group TrainingGroupWork */
        $inId = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($model->id), 'training_group_participant_in_id');
        $outId = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($model->id), 'training_group_participant_out_id');
        $groupIds = array_unique(ArrayHelper::getColumn($this->trainingGroupParticipantRepository->getAll(ArrayHelper::merge($inId, $outId)), 'training_group_id'));
        $groups = $this->trainingGroupRepository->getQueryById($groupIds)->all();
        $links = [];
        foreach ($groups as $group) {
            $links[] = Html::a($group->number, ['educational/training-group/view', 'id' => $group->id]) . "<br>";
        }
        return implode("\n", $links);
    }

    public function getGroupParticipantTable(OrderTrainingWork $model, $status)
    {
        /* @var $order OrderTrainingGroupParticipantWork */
        /* @var $participant ForeignEventParticipantsWork */
        $inId = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($model->id), 'training_group_participant_in_id');
        $outId = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($model->id), 'training_group_participant_out_id');

        $participants = $this->trainingGroupParticipantRepository->getAll(ArrayHelper::merge($inId, $outId));

        $links = [];
        foreach ($participants as $participant) {
            /** @var TrainingGroupParticipantWork $participant */
            $links[] =
                StringFormatter::stringAsLink($participant->participantWork->getFullFio(), Url::to([Yii::$app->frontUrls::PARTICIPANT_VIEW, 'id' => $participant->participant_id])) .
                ' - группа ' .
                StringFormatter::stringAsLink($participant->trainingGroupWork->number, Url::to([Yii::$app->frontUrls::TRAINING_GROUP_VIEW, 'id' => $participant->training_group_id]))
                . "<br>";

        }
        return implode("\n", $links);
    }

    public function getGroupsEmptyDataProvider()
    {
       return new ActiveDataProvider([
           'query' => $this->trainingGroupRepository->empty()
       ]);
    }

    public function getParticipantEmptyDataProvider()
    {
        return new ActiveDataProvider([
            'query' => $this->trainingGroupParticipantRepository->empty()
        ]);
    }

    public function getGroupsDataProvider(OrderTrainingWork $model)
    {
        return new ActiveDataProvider([
            'query' => $this->trainingGroupRepository->getByBranchQuery($model->branch)
        ]);
    }

    public function getParticipantsDataProvider(OrderTrainingWork $model)
    {
        $status = $this->getStatus($model);
        if($status == NomenclatureDictionary::ORDER_ENROLL) {
            $orderParticipantId = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($model->id),
                'training_group_participant_in_id');
            $groupId = ArrayHelper::getColumn($this->trainingGroupParticipantRepository->getAll($orderParticipantId),
                'training_group_id'
            );
            $query = TrainingGroupParticipantWork::find()
                ->orWhere(['id' => $orderParticipantId])
                ->orWhere(['and', ['training_group_id' => $groupId], ['status' => $status - 1]]);
        }
        if($status == NomenclatureDictionary::ORDER_DEDUCT) {
            $orderParticipantId = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($model->id),
                'training_group_participant_out_id');
            $groupId = ArrayHelper::getColumn($this->trainingGroupParticipantRepository->getAll($orderParticipantId),
                'training_group_id'
            );
            $query = TrainingGroupParticipantWork::find()
                ->orWhere(['id' => $orderParticipantId])
                ->orWhere(['and', ['training_group_id' => $groupId], ['status' => $status - 1]]);
        }
        if($status == NomenclatureDictionary::ORDER_TRANSFER){
            $orderParticipantId = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($model->id),
                'training_group_participant_out_id');
            $groupId = ArrayHelper::getColumn($this->trainingGroupParticipantRepository->getAll($orderParticipantId),
                'training_group_id'
            );
            $exceptParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
                ->andWhere(['order_id' => $model->id])->andWhere(['IS NOT', 'training_group_participant_out_id', NULL])
                ->andWhere(['IS NOT', 'training_group_participant_in_id', NULL])
                ->all(),
                'training_group_participant_in_id');

            $query = TrainingGroupParticipantWork::find()
                ->orWhere(['id' => $orderParticipantId])
                ->orWhere(['and', ['training_group_id' => $groupId], ['status' => NomenclatureDictionary::ORDER_ENROLL]]);
            $query = $query->andWhere(['not in', 'id', $exceptParticipantId]);
        }
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
    public function createOrderTrainingGroupParticipantEvent(OrderTrainingWork $model, $status, $post){
        $participantIds = $post['group-participant-selection'];
        $error = false;
        if($status == NomenclatureDictionary::ORDER_ENROLL) {
            if ($participantIds != NULL) {
                foreach ($participantIds as $participantId) {
                    if ($this->compareDate($model, $status, $participantId)) {
                        $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent(NULL, $participantId, $model->id),
                            OrderTrainingWork::class);
                        $this->trainingGroupParticipantRepository->setStatus($participantId, $status);
                    }
                    else {
                        $error = DocumentOrderWork::ERROR_DATE_PARTICIPANT;
                    }
                }
            }
        }
        if($status == NomenclatureDictionary::ORDER_DEDUCT) {
            if ($participantIds != NULL) {
                foreach ($participantIds as $participantId) {
                    if ($this->compareDate($model, $status, $participantId)) {
                        $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent($participantId, NULL, $model->id),
                            OrderTrainingWork::class);
                        $this->trainingGroupParticipantRepository->setStatus($participantId, $status);
                    }
                    else {
                        $error = DocumentOrderWork::ERROR_DATE_PARTICIPANT;
                    }
                }
            }
        }
        if($status == NomenclatureDictionary::ORDER_TRANSFER) {
            $transferGroupIds = $post['transfer-group'];
            if($participantIds != NULL){
                foreach ($participantIds as $participantId) {
                    if($transferGroupIds[$participantId] != NULL && $this->compareDate($model, $status, $participantId)) {
                        if (!$this->isPossibleToInsertTrainingGroupParticipant($transferGroupIds[$participantId], ($this->trainingGroupParticipantRepository->get($participantId))->participant_id)) {
                            $newTrainingGroupParticipant = TrainingGroupParticipantWork::fill(
                                $transferGroupIds[$participantId],
                                ($this->trainingGroupParticipantRepository->get($participantId))->participant_id,
                                NULL
                            );
                            $newTrainingGroupParticipant->setStatus($status - 2);
                            $this->trainingGroupParticipantRepository->save($newTrainingGroupParticipant);
                            $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent($participantId, $newTrainingGroupParticipant->id, $model->id),
                                OrderTrainingWork::class);
                            //update old TrainingGroupParticipant
                            $this->trainingGroupParticipantRepository->setStatus($participantId, $status - 1);
                        }
                        else {
                            $error = DocumentOrderWork::ERROR_RELATION;
                        }
                    }
                    else {
                        $error = DocumentOrderWork::ERROR_DATE_PARTICIPANT;
                    }
                }
            }
        }
        return $error;
    }
    public function compareDate(OrderTrainingWork $model, $status, $trainingGroupParticipantId)
    {
        /* @var TrainingGroupWork $group */
        $group = $this->trainingGroupRepository->get(($this->trainingGroupParticipantRepository->get($trainingGroupParticipantId))->training_group_id);
        if ($status == NomenclatureDictionary::ORDER_ENROLL) {
            return $group->start_date >= $model->order_date;
        }
        if ($status == NomenclatureDictionary::ORDER_DEDUCT) {
            return $group->start_date <= $model->order_date;
        }
        if($status == NomenclatureDictionary::ORDER_TRANSFER) {
            return $group->start_date <= $model->order_date && $group->finish_date >= $model->order_date;
        }
        return false;
    }
    public function isPossibleToInsertTrainingGroupParticipant($groupId, $participantId){
        return $this->trainingGroupParticipantRepository->isExist($groupId, $participantId);
    }
    public function isPossibleToDeleteOrderTrainingGroupParticipant($participantId)
    {
        if($this->orderTrainingGroupParticipantRepository->countByTrainingGroupParticipantOutId($participantId) > 0){
            return false;
        } else {
            return true;
        }
    }
    public function isPossibleToDeleteOrder($id)
    {
        $modelIds = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getExceptById($id),'training_group_participant_out_id');
        foreach ($modelIds as $modelId) {
            if (!is_null($modelId)) {
                if (!$this->isPossibleToDeleteOrderTrainingGroupParticipant($modelId)) {
                    return false;
                }
            }
        }
        return true;
    }
    public function updateOrderTrainingGroupParticipantEvent(OrderTrainingWork $model, $status, $post){
        $trainingGroupParticipants = $post['group-participant-selection'];
        $error = false;
        if ($status == NomenclatureDictionary::ORDER_ENROLL) {
            $formParticipants = $trainingGroupParticipants;
            $existsParticipants = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
                ->andWhere(['order_id' => $model->id])
                ->andWhere(['training_group_participant_out_id' => NULL])
                ->all(),
                'training_group_participant_in_id');
            if($formParticipants == NULL && $existsParticipants == NULL){
                $deleteParticipants = NULL;
                $createParticipants = NULL;
            }
            else if($formParticipants != NULL && $existsParticipants == NULL) {
                $deleteParticipants = NULL;
                $createParticipants = $formParticipants;
            }
            else if($formParticipants == NULL && $existsParticipants != NULL) {
                $deleteParticipants = $existsParticipants;
                $createParticipants = NULL;
            }
            else if($formParticipants != NULL && $formParticipants != NULL){
                $deleteParticipants = array_diff($existsParticipants, $formParticipants);
                $createParticipants = array_diff($formParticipants, $existsParticipants);
            }
            if ($deleteParticipants != NULL) {
                foreach ($deleteParticipants as $deleteParticipant) {
                    if ($this->isPossibleToDeleteOrderTrainingGroupParticipant($deleteParticipant)) {
                        $model->recordEvent(new DeleteOrderTrainingGroupParticipantEvent(NULL, $deleteParticipant, $model->id), OrderTrainingGroupParticipantWork::class);
                        $this->trainingGroupParticipantRepository->setStatus($deleteParticipant, NomenclatureDictionary::ORDER_INIT);
                    }
                    else {
                        $error = DocumentOrderWork::ERROR_RELATION;
                    }
                }
            }
            if ($createParticipants != NULL) {
                foreach ($createParticipants as $createParticipant) {
                    if ($this->compareDate($model, $status, $createParticipant)) {
                        $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent(NULL, $createParticipant, $model->id), OrderTrainingGroupParticipantWork::class);
                        $this->trainingGroupParticipantRepository->setStatus($createParticipant, NomenclatureDictionary::ORDER_ENROLL);
                    }
                    else {
                        $error = DocumentOrderWork::ERROR_DATE_PARTICIPANT;
                    }
                }
            }
        }
        if ($status == NomenclatureDictionary::ORDER_DEDUCT){
            $formParticipants = $trainingGroupParticipants;
            $existsParticipants = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
                ->andWhere(['order_id' => $model->id])
                ->andWhere(['training_group_participant_in_id' => NULL])
                ->all(),
                'training_group_participant_out_id');
            if($formParticipants == NULL && $existsParticipants == NULL){
                $deleteParticipants = NULL;
                $createParticipants = NULL;
            }
            else if($formParticipants != NULL && $existsParticipants == NULL) {
                $deleteParticipants = NULL;
                $createParticipants = $formParticipants;
            }
            else if($formParticipants == NULL && $existsParticipants != NULL) {
                $deleteParticipants = $existsParticipants;
                $createParticipants = NULL;
            }
            else if($formParticipants != NULL && $formParticipants != NULL){
                $deleteParticipants = array_diff($existsParticipants, $formParticipants);
                $createParticipants = array_diff($formParticipants, $existsParticipants);
            }
            if ($deleteParticipants != NULL) {
                foreach ($deleteParticipants as $deleteParticipant) {
                    $model->recordEvent(new DeleteOrderTrainingGroupParticipantEvent($deleteParticipant, NULL, $model->id), OrderTrainingGroupParticipantWork::class);
                    //old status
                    $this->trainingGroupParticipantRepository->setStatus($deleteParticipant, NomenclatureDictionary::ORDER_ENROLL);
                }
            }
            if ($createParticipants != NULL) {
                foreach ($createParticipants as $createParticipant) {
                    if ($this->compareDate($model, $status, $createParticipant)) {
                        $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent($createParticipant, NULL, $model->id), OrderTrainingGroupParticipantWork::class);
                        $this->trainingGroupParticipantRepository->setStatus($createParticipant, NomenclatureDictionary::ORDER_DEDUCT);
                    }
                    else {
                        $error = DocumentOrderWork::ERROR_DATE_PARTICIPANT;
                    }
                }
            }
        }
        if ($status == NomenclatureDictionary::ORDER_TRANSFER) {
            $formParticipants = $post['group-participant-selection'];
            $groups = $post['transfer-group'];
            $existsParticipants = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
                ->andWhere(['order_id' => $model->id])
                ->andWhere(['IS NOT','training_group_participant_in_id' , NULL])
                ->andWhere(['IS NOT','training_group_participant_out_id' , NULL])
                ->all(),
                'training_group_participant_out_id');
            if($formParticipants == NULL && $existsParticipants == NULL){
                $deleteParticipants = NULL;
                $createParticipants = NULL;
            }
            else if($formParticipants != NULL && $existsParticipants == NULL) {
                $deleteParticipants = NULL;
                $createParticipants = $formParticipants;
            }
            else if($formParticipants == NULL && $existsParticipants != NULL) {
                $deleteParticipants = $existsParticipants;
                $createParticipants = NULL;
            }
            else if($formParticipants != NULL && $formParticipants != NULL){
                $deleteParticipants = array_diff($existsParticipants, $formParticipants);
                $createParticipants = array_diff($formParticipants, $existsParticipants);
            }
            if ($deleteParticipants != NULL) {
                foreach ($deleteParticipants as $deleteParticipant) {
                    $deleteOrderParticipant = $this->orderTrainingGroupParticipantRepository->getUnique($deleteParticipant, $model->id);
                    if ($this->isPossibleToDeleteOrderTrainingGroupParticipant($deleteOrderParticipant->training_group_participant_in_id)) {
                        $newTrainingGroupParticipant = $this->trainingGroupParticipantRepository->get($deleteOrderParticipant->training_group_participant_in_id);
                        $model->recordEvent(new DeleteOrderTrainingGroupParticipantEvent($deleteParticipant, $deleteOrderParticipant->training_group_participant_in_id, $model->id), OrderTrainingGroupParticipantWork::class);
                        $model->recordEvent(new DeleteTrainingGroupParticipantEvent($newTrainingGroupParticipant->id), TrainingGroupParticipantWork::class);
                        $this->trainingGroupParticipantRepository->setStatus($deleteOrderParticipant->training_group_participant_out_id, NomenclatureDictionary::ORDER_ENROLL);
                    }
                    else {
                        $error = DocumentOrderWork::ERROR_RELATION;
                    }
                }
            }
            if ($createParticipants != NULL) {
                foreach ($createParticipants as $createParticipant) {
                    if($groups[$createParticipant] != NULL && $this->compareDate($model, $status, $createParticipant)) {
                        if (!$this->isPossibleToInsertTrainingGroupParticipant($groups[$createParticipant], ($this->trainingGroupParticipantRepository->get($createParticipant))->participant_id)) {
                            $newTrainingGroupParticipant = TrainingGroupParticipantWork::fill(
                                $groups[$createParticipant],
                                ($this->trainingGroupParticipantRepository->get($createParticipant))->participant_id,
                                NULL
                            );
                            $newTrainingGroupParticipant->setStatus($status - 2);
                            $this->trainingGroupParticipantRepository->save($newTrainingGroupParticipant);
                            $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent($createParticipant, $newTrainingGroupParticipant->id, $model->id), OrderTrainingGroupParticipantWork::class);
                            $this->trainingGroupParticipantRepository->setStatus($createParticipant, $status - 1);
                        }
                        else {
                            $error = DocumentOrderWork::ERROR_RELATION;
                        }
                    }
                }
            }
        }
        return $error;
    }
}
