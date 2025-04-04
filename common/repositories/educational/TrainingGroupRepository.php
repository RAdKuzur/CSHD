<?php

namespace common\repositories\educational;

use common\components\dictionaries\base\NomenclatureDictionary;
use common\components\traits\CommonDatabaseFunctions;
use common\repositories\providers\training_group\TrainingGroupProvider;
use common\repositories\providers\training_group\TrainingGroupProviderInterface;
use DomainException;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class TrainingGroupRepository
{
    use CommonDatabaseFunctions;

    private TrainingGroupProviderInterface $provider;
    private OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository;
    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    public function __construct(
        OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        TrainingGroupProviderInterface $provider = null
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(TrainingGroupProvider::class);
        }

        $this->provider = $provider;
        $this->orderTrainingGroupParticipantRepository = $orderTrainingGroupParticipantRepository;
        $this->trainingGroupParticipantRepository = $trainingGroupParticipantRepository;
    }

    /**
     * @param ActiveQuery $query
     * @return array|ActiveRecord[]
     */
    public function findAll(ActiveQuery $query) : array
    {
        return $query->all();
    }

    /**
     * @param ActiveQuery $query
     * @return array|ActiveRecord|null
     */
    public function findOne(ActiveQuery $query) : array
    {
        return $query->one();
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getAll()
    {
        return $this->provider->getAll();
    }

    public function getByTeacher($teacherId)
    {
        if (get_class($this->provider) == TrainingGroupProvider::class) {
            return $this->provider->getByTeacher($teacherId);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getByTeacher');
        }
    }

    /**
     * Возвращает все группы, попадающие любым периодом в указанный промежуток
     *
     * @param string $date1
     * @param string $date2
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getBetweenDates(string $date1, string $date2, array $teacherIds = [])
    {
        return $this->provider->getBetweenDates($date1, $date2, $teacherIds);
    }

    /**
     * Возвращает все группы, начавшие обучение до промежутка и закончившие в промежуток
     *
     * @param string $date1
     * @param string $date2
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getStartBeforeFinishInDates(string $date1, string $date2, array $teacherIds = [])
    {
        return $this->provider->getStartBeforeFinishInDates($date1, $date2, $teacherIds);
    }

    /**
     * Возвращает все группы, начавшие обучение в промежуток и закончившие после промежутка
     *
     * @param string $date1
     * @param string $date2
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getStartInFinishAfterDates(string $date1, string $date2, array $teacherIds = [])
    {
        return $this->provider->getStartInFinishAfterDates($date1, $date2, $teacherIds);
    }

    /**
     * Возвращает все группы, начавшие занятия в промежуток и закончившие в промежуток
     *
     * @param string $date1
     * @param string $date2
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getStartInFinishInDates(string $date1, string $date2, array $teacherIds = [])
    {
        return $this->provider->getStartInFinishInDates($date1, $date2, $teacherIds);
    }

    /**
     * Возвращает все группы, начавшие занятия до промежутка и закончившие после промежутка
     *
     * @param string $date1
     * @param string $date2
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getStartBeforeFinishAfterDates(string $date1, string $date2, array $teacherIds = [])
    {
        return $this->provider->getStartBeforeFinishAfterDates($date1, $date2, $teacherIds);
    }

    public function getGroupsForCertificates()
    {
        return $this->provider->getGroupsForCertificates();
    }

    public function getParticipants($id)
    {
        return $this->provider->getParticipants($id);
    }

    public function getLessons($id)
    {
        return $this->provider->getLessons($id);
    }

    public function getExperts($id)
    {
        return $this->provider->getExperts($id);
    }

    public function getThemes($id)
    {
        return $this->provider->getThemes($id);
    }

    public function save(TrainingGroupWork $group)
    {
        return $this->provider->save($group);
    }

    public function delete(TrainingGroupWork $model)
    {
        return $this->provider->delete($model);
    }

    public function getByBranchQuery($branch)
    {
        if (get_class($this->provider) == TrainingGroupProvider::class) {
            return $this->provider->getByBranchQuery($branch);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getByBranchQuery');
        }
    }

    public function getByBranches(array $branches)
    {
        if (get_class($this->provider) == TrainingGroupProvider::class) {
            return $this->provider->getByBranch($branches);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getByBranches');
        }
    }

    public function getQueryById($id)
    {
        return TrainingGroupWork::find()->where(['id' => $id]);
    }

    public function empty()
    {
        return TrainingGroupWork::find()->where(['id' => 0]);
    }

    public function getByProgramId(int $programId)
    {
        return TrainingGroupWork::find()
            ->where(['training_program_id' => $programId])
            ->all();
    }

    public function getGroupPitchInPeriod(string $leftDate, string $rightDate)
    {
        return TrainingGroupWork::find()
            ->where(['>', 'protection_date', $leftDate])
            ->andWhere(['<', 'protection_date', $rightDate])
            ->all();
    }

    public function getAttachedGroupsByOrder($orderId, $status){
        if ($status == NomenclatureDictionary::ORDER_ENROLL){
            $participants = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($orderId), 'training_group_participant_in_id');
            $groups = array_unique(ArrayHelper::getColumn($this->trainingGroupParticipantRepository->getAll($participants), 'training_group_id'));
        }
        else if ($status == NomenclatureDictionary::ORDER_DEDUCT) {
            $participants = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($orderId), 'training_group_participant_out_id');
            $groups = array_unique(ArrayHelper::getColumn($this->trainingGroupParticipantRepository->getAll($participants), 'training_group_id'));
        }
        else if ($status == NomenclatureDictionary::ORDER_TRANSFER) {
            $participants = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($orderId), 'training_group_participant_in_id');
            $groups = array_unique(ArrayHelper::getColumn($this->trainingGroupParticipantRepository->getAll($participants), 'training_group_id'));
        }
        return $groups;
    }
}