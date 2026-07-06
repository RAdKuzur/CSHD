<?php

namespace common\services\general\errors;

use common\components\dictionaries\base\ErrorDictionary;
use common\models\scaffold\ForeignEventParticipants;
use common\models\work\ErrorsWork;
use common\repositories\act_participant\SquadParticipantRepository;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\general\ErrorsRepository;
use common\repositories\general\ErrorsRepositoryInterface;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Yii;

class ErrorForeignEventParticipantService
{
    private ForeignEventParticipantsRepository $foreignEventParticipantsRepository;
    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    private SquadParticipantRepository $squadParticipantRepository;
    private ErrorsRepositoryInterface  $errorsRepository;
    public function __construct(
        ForeignEventParticipantsRepository $foreignEventParticipantsRepository,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        SquadParticipantRepository $squadParticipantRepository,
        ErrorsRepositoryInterface  $errorsRepository
    )
    {
        $this->foreignEventParticipantsRepository = $foreignEventParticipantsRepository;
        $this->trainingGroupParticipantRepository = $trainingGroupParticipantRepository;
        $this->squadParticipantRepository = $squadParticipantRepository;
        $this->errorsRepository = $errorsRepository;
    }
    //не зачислен в к-л. группу
//    public function makeForeignEventParticipant001($rowId)
//    {
//        $participants = $this->trainingGroupParticipantRepository->getByParticipantId($rowId);
//        if (count($participants) > 0) {
//            if (count(array_filter($participants,function (TrainingGroupParticipantWork $item){
//                    return $item->status == 0;
//                })) > 0)
//            {
//                $this->errorsRepository->save(
//                    ErrorsWork::fill(
//                        ErrorDictionary::FOREIGN_EVENT_PARTICIPANT_001,
//                        ForeignEventParticipantsWork::tableName(),
//                        $rowId,
//                        Yii::$app->errors->get(ErrorDictionary::FOREIGN_EVENT_PARTICIPANT_001)->getErrorState()
//                    )
//                );
//
//            }
//        }
//    }
//    public function fixForeignEventParticipant001($errorId){
//        /* @var $error ErrorsWork */
//        $error = $this->errorsRepository->get($errorId);
//        $participants = $this->trainingGroupParticipantRepository->getByParticipantId($error->table_row_id);
//        if (count($participants) == 0 || count(array_filter($participants,function (TrainingGroupParticipantWork $item){
//                return $item->status == 0;
//            })) > 0) {
//            $this->errorsRepository->delete($error);
//        }
//    }
    //не фигурирует в обр.деятельности

    public function setErrorsRepository(ErrorsRepositoryInterface $repository): void
    {
        $this->errorsRepository = $repository;
    }

    /**
     * DataFetch функция - собирает данные для всех участников одним запросом
     *
     * @param array $participantIds - массив ID всех участников для проверки
     * @return array - возвращает подготовленные данные
     */
    public function fetchDataForForeignEventParticipant002(array $participantIds): array
    {
        // Один запрос для получения всех training group participants
        $allTrainingParticipants = $this->trainingGroupParticipantRepository
            ->getByParticipantIds($participantIds); // Нужен batch-метод

        // Один запрос для получения всех squad participants
        $allSquadParticipants = $this->squadParticipantRepository
            ->getAllByParticipantIds($participantIds); // Нужен batch-метод

        // Группируем данные по participant_id для быстрого доступа
        $trainingByParticipant = [];
        foreach ($allTrainingParticipants as $tp) {
            $trainingByParticipant[$tp->participant_id][] = $tp;
        }

        $squadByParticipant = [];
        foreach ($allSquadParticipants as $sp) {
            $squadByParticipant[$sp->participant_id][] = $sp;
        }

        return [
            'trainingByParticipant' => $trainingByParticipant,
            'squadByParticipant' => $squadByParticipant,
        ];
    }

    /**
     * Make функция - теперь принимает предзагруженные данные
     *
     * @param int $rowId
     * @param array|null $preloadedData - предзагруженные данные (опционально для обратной совместимости)
     */
    public function makeForeignEventParticipant002($rowId, ?array $preloadedData = null)
    {
        if ($preloadedData !== null) {
            // Используем предзагруженные данные
            $participants = $preloadedData['trainingByParticipant'][$rowId] ?? [];
            $squadParticipants = $preloadedData['squadByParticipant'][$rowId] ?? [];
        } else {
            // Старый способ для одиночных проверок
            $participants = $this->trainingGroupParticipantRepository->getByParticipantId($rowId);
            $squadParticipants = $this->squadParticipantRepository->getAllByParticipantId($rowId);
        }

        if (count($participants) + count($squadParticipants) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::FOREIGN_EVENT_PARTICIPANT_002,
                    ForeignEventParticipantsWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::FOREIGN_EVENT_PARTICIPANT_002)->getErrorState()
                )
            );
        }
    }

    /**
     * Fix функция - теперь принимает предзагруженные данные
     *
     * @param int $errorId
     * @param array|null $preloadedData - предзагруженные данные
     */
    public function fixForeignEventParticipant002($errorId, ?array $preloadedData = null)
    {
        /* @var $error ErrorsWork */
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            // Используем предзагруженные данные
            $participants = $preloadedData['trainingByParticipant'][$error->table_row_id] ?? [];
            $squadParticipants = $preloadedData['squadByParticipant'][$error->table_row_id] ?? [];
        } else {
            // Старый способ для одиночных проверок
            $participants = $this->trainingGroupParticipantRepository->getByParticipantId($error->table_row_id);
            $squadParticipants = $this->squadParticipantRepository->getAllByParticipantId($error->table_row_id);
        }

        if (count($participants) + count($squadParticipants) != 0) {
            $this->errorsRepository->delete($error);
        }
    }

    public function allForeignParticipantCheckOnError002() {
        $errorsMap = $this->errorsRepository->getErrorsIdsByTableName(ForeignEventParticipantsWork::tableName());
        $participantIdsWithError = array_unique($errorsMap);
        $participantIds = $this->foreignEventParticipantsRepository->getAllIds();

        $countGroupParticipantMap = $this->trainingGroupParticipantRepository->getCountsByParticipantIds($participantIds);
        $countSquadParticipantMap = $this->squadParticipantRepository->getCountByParticipantId($participantIds);

        $arrayDelete = [];
        foreach ($errorsMap as $id => $participantId) {
            if ($countGroupParticipantMap[$participantId] + $countSquadParticipantMap[$participantId] != 0) {
                $arrayDelete[] = $id;
            }
        }
        $this->errorsRepository->deleteByListId($arrayDelete);
        $participantNotChecked = array_diff($participantIds, $participantIdsWithError);

        foreach ($participantNotChecked as $participantId) {
            if ($countGroupParticipantMap[$participantId] + $countSquadParticipantMap[$participantId] == 0) {
                $this->errorsRepository->save(
                    ErrorsWork::fill(
                        ErrorDictionary::FOREIGN_EVENT_PARTICIPANT_002,
                        ForeignEventParticipantsWork::tableName(),
                        $participantId,
                        Yii::$app->errors->get(ErrorDictionary::FOREIGN_EVENT_PARTICIPANT_002)->getErrorState()
                    )
                );
            }
        }



    }

}