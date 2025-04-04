<?php

namespace frontend\services\dictionaries;

use common\helpers\CompareHelper;
use common\models\scaffold\ForeignEventParticipants;
use common\models\scaffold\PeoplePositionCompanyBranch;
use common\models\scaffold\PeopleStamp;
use common\models\scaffold\PersonalDataParticipant;
use common\models\scaffold\Position;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use common\repositories\general\PeoplePositionCompanyBranchRepository;
use common\repositories\general\PeopleStampRepository;
use common\services\DatabaseServiceInterface;
use DateTime;
use frontend\events\foreign_event_participants\DropCorrectEvent;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Compare;

class ForeignEventParticipantsService implements DatabaseServiceInterface
{
    private int $levenshteinAccuracy = 2;
    private string $thresholdYear1996 = '1996-01-01';
    private string $thresholdYear1930 = '1930-01-01';

    private ForeignEventParticipantsRepository $repository;
    private PersonalDataParticipantRepository $personalDataRepository;

    public function __construct(
        ForeignEventParticipantsRepository $repository,
        PersonalDataParticipantRepository $personalDataRepository
    )
    {
        $this->repository = $repository;
        $this->personalDataRepository = $personalDataRepository;
    }

    /**
     * Метод проверки всех участников деятельности на корректность
     * @return void
     * @throws \yii\db\Exception
     */
    public function checkCorrectAll()
    {
        /**
         * НЕОБХОДИМО ИЗМЕНИТЬ ПРИНЦИП ПРОВЕРКИ ПОСЛЕ ДОБАВЛЕНИЯ УЧЕБНЫХ ГРУПП
         */

        $parts = $this->repository->getSortedList(ForeignEventParticipantsRepository::SORT_FIO);

        foreach ($parts as $part) {
            /** @var ForeignEventParticipantsWork $part */
            $this->checkCorrectOne($part);
        }
    }

    /**
     * Метод проверки одного участника $participant на корректность
     * @param ForeignEventParticipantsWork $participant
     * @return void
     */
    public function checkCorrectOne(ForeignEventParticipantsWork $participant)
    {
        if (CompareHelper::compareParticipantBirthdate($participant->birthdate, $this->thresholdYear1996, $this->thresholdYear1930)
            == CompareHelper::RESULT_INCORRECT) {
            $participant->recordEvent(
                new DropCorrectEvent($participant->id, ForeignEventParticipantsWork::DROP_CORRECT_HARD),
                ForeignEventParticipantsWork::class
            );
        }

        $potentialParticipants = ForeignEventParticipantsWork::find()
            ->where(['!=', 'id', $participant->id])
            ->andWhere(['between', 'LENGTH(surname)', strlen($participant->surname) - $this->levenshteinAccuracy, strlen($participant->surname) + $this->levenshteinAccuracy])
            ->andWhere(['between', 'LENGTH(firstname)', strlen($participant->firstname) - $this->levenshteinAccuracy, strlen($participant->firstname) + $this->levenshteinAccuracy]);

        if (!is_null($participant->patronymic)) {
            $potentialParticipants =
                $potentialParticipants
                    ->andWhere(['between', 'LENGTH(patronymic)', strlen($participant->patronymic) - $this->levenshteinAccuracy, strlen($participant->patronymic) + $this->levenshteinAccuracy]);
        }

        $potentialParticipants = $potentialParticipants->all();

        foreach ($potentialParticipants as $potentialParticipant) {
            /** @var ForeignEventParticipantsWork $potentialParticipant */
            if (CompareHelper::compareStrings(
                    [$participant->surname, $participant->firstname, $participant->patronymic],
                    [$potentialParticipant->surname, $potentialParticipant->firstname, $potentialParticipant->patronymic],
                    $this->levenshteinAccuracy
                ) == CompareHelper::RESULT_CORRECT) {
                $participant->recordEvent(
                    new DropCorrectEvent($participant->id, ForeignEventParticipantsWork::DROP_CORRECT_SOFT),
                    ForeignEventParticipantsWork::class
                );
                $participant->recordEvent(
                    new DropCorrectEvent($potentialParticipant->id, ForeignEventParticipantsWork::DROP_CORRECT_SOFT),
                    ForeignEventParticipantsWork::class
                );
            }
        }

        $participant->releaseEvents();
    }

    /**
     * Возвращает список ошибок, если список пуст - проблем нет
     * @param $entityId
     * @return array
     */
    public function isAvailableDelete($entityId)
    {
        return [];
    }
}