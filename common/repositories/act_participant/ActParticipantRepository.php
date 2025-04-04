<?php

namespace common\repositories\act_participant;

use common\components\logger\base\LogInterface;
use common\components\logger\LogFactory;
use common\models\work\LogWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\team\ActParticipantBranchWork;
use frontend\models\work\team\ActParticipantWork;
use frontend\models\work\team\SquadParticipantWork;
use common\models\scaffold\ActParticipant;
use common\repositories\event\ForeignEventRepository;
use common\repositories\order\OrderEventRepository;
use DomainException;
use Yii;
use yii\console\Application;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class ActParticipantRepository
{
    public SquadParticipantRepository $squadParticipantRepository;
    public OrderEventRepository $orderEventRepository;
    public ForeignEventRepository $foreignEventRepository;
    public function __construct(
        SquadParticipantRepository $squadParticipantRepository,
        OrderEventRepository $orderEventRepository,
        ForeignEventRepository $foreignEventRepository
    ){
        $this->squadParticipantRepository = $squadParticipantRepository;
        $this->orderEventRepository = $orderEventRepository;
        $this->foreignEventRepository = $foreignEventRepository;
    }

    public function findAll(ActiveQuery $query): array
    {
        return $query->all();
    }

    public function count(ActiveQuery $query)
    {
        return $query->count();
    }

    public function getAll()
    {
        return ActParticipantWork::find()->all();
    }

    public function getByForeignEventIds(array $foreignEventIds, array $types = [ActParticipantWork::TYPE_TEAM, ActParticipantWork::TYPE_SOLO])
    {
        $query = ActParticipantWork::find()
            ->where(['IN', 'foreign_event_id', $foreignEventIds])
            ->andWhere(['IN', 'type', $types]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка актов участия по заданному мероприятию', $query->createCommand()->getRawSql());
        return $query->all();
    }

    public function getByParticipantId($participantId)
    {
        $squads = ArrayHelper::getColumn($this->squadParticipantRepository->getAllByParticipantId($participantId), 'act_participant_id');
        $query = ActParticipantWork::find()->where(['IN', 'id', $squads]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка актов участия по заданному участнику деятельности', $query->createCommand()->getRawSql());
        return $query->all();
    }

    public function getActsByBranches(array $branches)
    {
        $query = ActParticipantWork::find()
            ->joinWith(['actParticipantBranchWork actParticipantBranchWork'])
            ->where(['IN', 'actParticipantBranchWork.branch', $branches]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка актов участия по отделам', $query->createCommand()->getRawSql());
        return $query->all();
    }

    public function getByForeignEventsAndBranches(array $foreignEventIds, array $branches)
    {
        $query = ActParticipantWork::find()
            ->joinWith(['actParticipantBranchWork actParticipantBranchWork'])
            ->where(['IN', 'foreign_event_id', $foreignEventIds])
            ->andWhere(['IN', 'actParticipantBranchWork.branch', $branches]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка актов участия по заданному мероприятию и отделам', $query->createCommand()->getRawSql());
        return $query->all();
    }

    public function prepareCreate($modelAct, $teamNameId, $foreignEventId)
    {
        $modelAct->save();
        return $modelAct->id;
    }

    public function prepareDelete($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(ActParticipantWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }

    public function getOneByUniqueAttributes($teamNameId, $nomination, $foreignEventId)
    {
        $query = ActParticipantWork::find()
            ->andWhere(['foreign_event_id' => $foreignEventId])
            ->andWhere(['team_name_id' => $teamNameId])
            ->andWhere(['nomination' => $nomination]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка акта участия по заданным параметрам', $query->createCommand()->getRawSql());
        return $query->one();
    }

    public function getAllByUniqueAttributes($teamNameId, $nomination, $foreignEventId)
    {
        $query = ActParticipantWork::find()
            ->andWhere(['foreign_event_id' => $foreignEventId])
            ->andWhere(['team_name_id' => $teamNameId])
            ->andWhere(['nomination' => $nomination]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка всех актов участия по заданным параметрам', $query->createCommand()->getRawSql());
        return $query->all();
    }

    public function getParticipantBranches($actId)
    {
        $query = ActParticipantBranchWork::find()->where(['act_participant_id' => $actId]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка связок акт-отдел по заданному ID акта участия', $query->createCommand()->getRawSql());
        return $query->all();
    }

    public function getByTypeAndForeignEventId($foreignEventId, $type)
    {
        $query = ActParticipantWork::find()->andWhere(['foreign_event_id' => $foreignEventId])->andWhere(['type' => $type]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка актов участия по типу и ID мероприятия', $query->createCommand()->getRawSql());
        return $query->all();
    }

    public function checkUniqueAct($foreignEventId, $teamNameId, $focus, $form, $nomination)
    {
        $query = ActParticipantWork::find()
            ->andWhere(['foreign_event_id' => $foreignEventId])
            ->andWhere(['team_name_id' => $teamNameId])
            ->andWhere(['focus' => $focus])
            ->andWhere(['form' => $form])
            ->andWhere(['nomination' => $nomination]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка количества уникальных актов участия по заданным параметрам', $query->createCommand()->getRawSql());
        return count($query->all());
    }

    public function get($id)
    {
        $query = ActParticipantWork::find()->where(['id' => $id]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка акта участия по ID', $query->createCommand()->getRawSql());
        return $query->one();
    }

    public function save(ActParticipantWork $model)
    {
        if ($model->isNewRecord) {
            $sql = Yii::$app->db->createCommand()->insert($model->tableName(), $model->attributes)->getSql();
        } else {
            $sql = Yii::$app->db->createCommand()->update($model->tableName(), $model->attributes, ['id' => $model->id])->getSql();
        }
        if (!$model->save()) {
            LogFactory::createCrudLog(LogInterface::LVL_ERROR, 'Ошибка сохранения акта участия', $sql);
            throw new DomainException('Ошибка сохранения. Проблемы: ' . json_encode($model->getErrors()));

        }
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Сохранение акта участия', $sql);
        return $model->id;
    }

    public function delete(ActParticipantWork $model)
    {
        $squadParticipants = $this->squadParticipantRepository->getAllByActId($model->id);
        foreach ($squadParticipants as $squadParticipant) {
            if (!$squadParticipant->delete()) {
                throw new DomainException('Ошибка удаления. Проблемы: '.json_encode($model->getErrors()));
            }
        }

        $sql = Yii::$app->db->createCommand()->delete($model->tableName(), ['id' => $model->id])->getSql();
        if (!$model->delete()) {
            LogFactory::createCrudLog(LogInterface::LVL_ERROR, 'Ошибка удаления акта участия', $sql);
            throw new DomainException('Ошибка удаления. Проблемы: '.json_encode($model->getErrors()));
        }

        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Удаление акта участия', $sql);
    }
}