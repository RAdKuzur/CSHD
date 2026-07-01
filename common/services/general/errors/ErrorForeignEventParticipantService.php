<?php

namespace common\services\general\errors;

use common\components\dictionaries\base\ErrorDictionary;
use common\models\scaffold\ForeignEventParticipants;
use common\models\work\ErrorsWork;
use common\repositories\act_participant\SquadParticipantRepository;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\general\ErrorsRepository;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Yii;

class ErrorForeignEventParticipantService
{
    private ForeignEventParticipantsRepository $foreignEventParticipantsRepository;
    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    private SquadParticipantRepository $squadParticipantRepository;
    private ErrorsRepository $errorsRepository;
    public function __construct(
        ForeignEventParticipantsRepository $foreignEventParticipantsRepository,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        SquadParticipantRepository $squadParticipantRepository,
        ErrorsRepository $errorsRepository
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
    public function makeForeignEventParticipant002($rowId)
    {
        $participants = $this->trainingGroupParticipantRepository->getByParticipantId($rowId);
        $squadParticipants = $this->squadParticipantRepository->getAllByParticipantId($rowId);
        if (count($participants) + count($squadParticipants) == 0){
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

    public function fixForeignEventParticipant002($errorId){
        /* @var $error ErrorsWork */
        $error = $this->errorsRepository->get($errorId);
        $participants = $this->trainingGroupParticipantRepository->getByParticipantId($error->table_row_id);
        $squadParticipants = $this->squadParticipantRepository->getAllByParticipantId($error->table_row_id);
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