<?php

namespace common\repositories\providers\visit;

use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use DomainException;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use Yii;
use yii\helpers\ArrayHelper;

class VisitProvider implements VisitProviderInterface
{
    public function get($id)
    {
        return VisitWork::find()->where(['id' => $id])->one();
    }

    public function getByTrainingGroup($groupId)
    {
        return VisitWork::find()
            ->joinWith(['trainingGroupParticipantWork trainingGroupParticipantWork'])
            ->joinWith(['trainingGroupParticipantWork.participantWork participantWork'])
            ->where(['trainingGroupParticipantWork.training_group_id' => $groupId])
            ->orderBy(
                [
                    'participantWork.surname' => SORT_ASC,
                    'participantWork.firstname' => SORT_ASC
                ]
            )
            ->all();
    }

    public function delete(VisitWork $visit)
    {
        return $visit->delete();
    }

    public function save(VisitWork $visit)
    {
        if (!$visit->save()) {
            throw new DomainException('Ошибка сохранения явок. Проблемы: '.json_encode($visit->getErrors()));
        }
        return $visit->id;
    }

    public function getParticipantsFromGroup($groupId)
    {
        $visits = $this->getByTrainingGroup($groupId);
        return (Yii::createObject(TrainingGroupParticipantRepository::class))->getByParticipantIds(ArrayHelper::getColumn($visits, 'participant_id'));
    }

    public function getLessonsFromGroup($groupId)
    {
        /** @var VisitWork $visit */
        $visit = VisitWork::find()->where(['training_group_id' => $groupId])->one();
        $lessonIds = VisitLesson::getLessonIds(VisitLesson::fromString($visit->lessons));
        return (Yii::createObject(TrainingGroupLessonRepository::class))->getByIds($lessonIds);
    }

    public function getByTrainingGroupParticipant(int $trainingGroupParticipantId)
    {
        return VisitWork::find()->where(['training_group_participant_id' => $trainingGroupParticipantId])->one();
    }

    public function getByTrainingGroupParticipants(array $trainingGroupParticipantId)
    {
        return VisitWork::find()->where(['IN', 'training_group_participant_id', $trainingGroupParticipantId])->all();
    }
}