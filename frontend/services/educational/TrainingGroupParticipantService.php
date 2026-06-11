<?php


namespace frontend\services\educational;


use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;
use common\services\DatabaseServiceInterface;
use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Yii;

class TrainingGroupParticipantService implements DatabaseServiceInterface
{
    private TrainingGroupParticipantRepository $repository;
    private VisitRepository $visitRepository;

    public function __construct(
        TrainingGroupParticipantRepository $repository,
        VisitRepository $visitRepository
    )
    {
        $this->repository = $repository;
        $this->visitRepository = $visitRepository;
    }

    public function isAvailableDelete($entityId)
    {
        $orders = $this->repository->checkDeleteAvailable(OrderTrainingGroupParticipantWork::tableName(), TrainingGroupParticipantWork::tableName(), $entityId);
        return $orders;
    }

    public function delete($entityId)
    {
        if (count($this->isAvailableDelete($entityId)) > 0) {
            Yii::$app->session->setFlash('danger', 'Невозможно удалить обучающегося. Обучающихся фигурирует в связанных с группой приказах');
            return -1;
        }

        // На тот случай, если ученик в группу был добавлен, но не зачислен. Тогда и visit у него не будет
        $result = $this->visitRepository->getByTrainingGroupParticipant($entityId);
        if ($result != null) {
            $this->visitRepository->delete($result);
        }

        return $this->repository->delete(
            $this->repository->get($entityId)
        );
    }
}