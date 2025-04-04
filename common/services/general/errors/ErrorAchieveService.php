<?php

namespace common\services\general\errors;

use common\components\dictionaries\base\ErrorDictionary;
use common\helpers\files\FilesHelper;
use common\models\work\ErrorsWork;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\event\EventRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\event\ParticipantAchievementRepository;
use common\repositories\general\ErrorsRepository;
use frontend\models\work\event\EventBranchWork;
use frontend\models\work\event\EventWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\team\ActParticipantBranchWork;
use frontend\models\work\team\ActParticipantWork;
use Yii;
use yii\helpers\ArrayHelper;

class ErrorAchieveService
{
    private ErrorsRepository $errorsRepository;
    private ForeignEventRepository $foreignEventRepository;
    private ActParticipantRepository $actParticipantRepository;
    private TrainingGroupParticipantRepository $groupParticipantRepository;
    private ParticipantAchievementRepository $achievementRepository;
    private EventRepository $eventRepository;

    public function __construct(
        ErrorsRepository $errorsRepository,
        ForeignEventRepository $foreignEventRepository,
        ActParticipantRepository $actParticipantRepository,
        TrainingGroupParticipantRepository $groupParticipantRepository,
        ParticipantAchievementRepository $achievementRepository,
        EventRepository $eventRepository
    )
    {
        $this->errorsRepository = $errorsRepository;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->groupParticipantRepository = $groupParticipantRepository;
        $this->achievementRepository = $achievementRepository;
        $this->eventRepository = $eventRepository;
    }

    // Проверяем даты начала и окончания на валидность
    public function makeAchieve_001($rowId)
    {
        /** @var ForeignEventWork $event */
        $event = $this->foreignEventRepository->get($rowId);
        if ($event->begin_date > $event->end_date) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_001,
                    ForeignEventWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_001)->getErrorState()
                )
            );
        }
    }

    public function fixAchieve_001($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var ForeignEventWork $event */
        $error = $this->errorsRepository->get($errorId);
        $event = $this->foreignEventRepository->get($error->table_row_id);
        if (!($event->begin_date > $event->end_date)) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие города проведения
    public function makeAchieve_002($rowId)
    {
        /** @var ForeignEventWork $event */
        $event = $this->foreignEventRepository->get($rowId);
        if (is_null($event->city) || strlen($event->city) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_002,
                    ForeignEventWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_002)->getErrorState()
                )
            );
        }
    }

    public function fixAchieve_002($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var ForeignEventWork $event */
        $error = $this->errorsRepository->get($errorId);
        $event = $this->foreignEventRepository->get($error->table_row_id);
        if (!(is_null($event->city) || strlen($event->city) == 0)) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие участников мероприятия
    public function makeAchieve_003($rowId)
    {
        /** @var ActParticipantWork[] $acts */
        $acts = $this->actParticipantRepository->getByForeignEventIds([$rowId]);
        if (count($acts) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_003,
                    ForeignEventWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_003)->getErrorState()
                )
            );
        }
    }

    public function fixAchieve_003($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var ForeignEventWork $event */
        $error = $this->errorsRepository->get($errorId);
        $acts = $this->actParticipantRepository->getByForeignEventIds([$error->table_row_id]);
        if (count($acts) != 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отдел обучения участника мероприятия (act_participant)
    public function makeAchieve_004($rowId)
    {
        /** @var ActParticipantWork $act */
        $act = $this->actParticipantRepository->get($rowId);
        $branchesEvent = ArrayHelper::getColumn($this->actParticipantRepository->getParticipantBranches($rowId), 'branch');
        $groupParticipants = $this->groupParticipantRepository->getByParticipantIds([$act->squadParticipantsWork[0]->participant_id]);
        $branchesStudy = ArrayHelper::getColumn($groupParticipants, 'trainingGroupWork.branch');

        if (empty(array_intersect($branchesEvent, $branchesStudy))) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_004,
                    ActParticipantWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_004)->getErrorState(),
                    $branchesEvent[0]
                )
            );
        }
    }

    public function fixAchieve_004($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var ActParticipantWork $act */
        $error = $this->errorsRepository->get($errorId);
        $act = $this->actParticipantRepository->get($error->table_row_id);
        $branchesEvent = ArrayHelper::getColumn($this->actParticipantRepository->getParticipantBranches($error->table_row_id), 'branch');
        $groupParticipants = $this->groupParticipantRepository->getByParticipantIds([$act->squadParticipantsWork[0]->participant_id]);
        $branchesStudy = ArrayHelper::getColumn($groupParticipants, 'trainingGroupWork.branch');

        if (!empty(array_intersect($branchesEvent, $branchesStudy))) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие хотя бы одного указанного достижения участника
    public function makeAchieve_005($rowId)
    {
        /** @var ParticipantAchievementWork[] $achieves */
        $achieves = $this->achievementRepository->getByForeignEvent($rowId);

        if (count($achieves) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_005,
                    ForeignEventWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_005)->getErrorState()
                )
            );
        }
    }

    public function fixAchieve_005($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var ForeignEventWork $event */
        $error = $this->errorsRepository->get($errorId);
        $achieves = $this->achievementRepository->getByForeignEvent($error->table_row_id);
        if (count($achieves) != 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие файла достижений
    public function makeAchieve_006($rowId)
    {
        /** @var ForeignEventWork $event */
        $event = $this->foreignEventRepository->get($rowId);
        if (count($event->getFileLinks(FilesHelper::TYPE_DOC)) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_006,
                    ForeignEventWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_006)->getErrorState()
                )
            );
        }
    }

    public function fixAchieve_006($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var ForeignEventWork $event */
        $error = $this->errorsRepository->get($errorId);
        $event = $this->foreignEventRepository->get($error->table_row_id);
        if (count($event->getFileLinks(FilesHelper::TYPE_DOC)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие организатора
    public function makeAchieve_007($rowId)
    {
        /** @var ForeignEventWork $event */
        $event = $this->foreignEventRepository->get($rowId);
        if (is_null($event->organizer_id)) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_007,
                    ForeignEventWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_007)->getErrorState()
                )
            );
        }
    }

    public function fixAchieve_007($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var ForeignEventWork $event */
        $error = $this->errorsRepository->get($errorId);
        $event = $this->foreignEventRepository->get($error->table_row_id);
        if ($event->organizer_id) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие формата проведения
    public function makeAchieve_008($rowId)
    {
        /** @var EventWork $event */
        $event = $this->eventRepository->get($rowId);
        if (is_null($event->event_way)) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_008,
                    EventWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_008)->getErrorState(),
                    $event->eventBranchWorks[0] ? $event->eventBranchWorks[0]->branch : null
                )
            );
        }
    }

    public function fixAchieve_008($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var EventWork $event */
        $error = $this->errorsRepository->get($errorId);
        $event = $this->eventRepository->get($error->table_row_id);
        if ($event->event_way) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие отдела
    public function makeAchieve_009($rowId)
    {
        /** @var EventBranchWork[] $branches */
        $branches = $this->eventRepository->getBranches($rowId);
        if (count($branches) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_009,
                    EventWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_009)->getErrorState()
                )
            );
        }
    }

    public function fixAchieve_009($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var EventBranchWork[] $branches */
        $error = $this->errorsRepository->get($errorId);
        $branches = $this->eventRepository->getBranches($error->table_row_id);
        if (count($branches) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие приказа
    public function makeAchieve_010($rowId)
    {
        /** @var EventWork $event */
        $event = $this->eventRepository->get($rowId);
        if (is_null($event->order_id)) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_010,
                    EventWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_010)->getErrorState(),
                    $event->eventBranchWorks[0] ? $event->eventBranchWorks[0]->branch : null
                )
            );
        }
    }

    public function fixAchieve_010($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var EventWork $event */
        $error = $this->errorsRepository->get($errorId);
        $event = $this->eventRepository->get($error->table_row_id);
        if ($event->order_id) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие фотоматериалов
    public function makeAchieve_011($rowId)
    {
        /** @var EventWork $event */
        $event = $this->eventRepository->get($rowId);
        if (count($event->getFileLinks(FilesHelper::TYPE_PHOTO)) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_011,
                    EventWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_011)->getErrorState(),
                    $event->eventBranchWorks[0] ? $event->eventBranchWorks[0]->branch : null
                )
            );
        }
    }

    public function fixAchieve_011($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var EventWork $event */
        $error = $this->errorsRepository->get($errorId);
        $event = $this->eventRepository->get($error->table_row_id);
        if (count($event->getFileLinks(FilesHelper::TYPE_PHOTO)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие ключевых слов
    public function makeAchieve_012($rowId)
    {
        /** @var EventWork $event */
        $event = $this->eventRepository->get($rowId);
        if (is_null($event->key_words) || strlen($event->key_words) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_012,
                    EventWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_012)->getErrorState(),
                    $event->eventBranchWorks[0] ? $event->eventBranchWorks[0]->branch : null
                )
            );
        }
    }

    public function fixAchieve_012($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var EventWork $event */
        $error = $this->errorsRepository->get($errorId);
        $event = $this->eventRepository->get($error->table_row_id);
        if (!(is_null($event->key_words) || strlen($event->key_words) == 0)) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие отдела учета для участников мероприятий
    public function makeAchieve_013($rowId)
    {
        $branchesEvent = $this->actParticipantRepository->getParticipantBranches($rowId);
        if (count($branchesEvent) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::ACHIEVE_013,
                    ActParticipantWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::ACHIEVE_013)->getErrorState()
                )
            );
        }
    }

    public function fixAchieve_013($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var ActParticipantBranchWork[] $branchesEvent */
        $error = $this->errorsRepository->get($errorId);
        $branchesEvent = $this->actParticipantRepository->getParticipantBranches($error->table_row_id);
        if (count($branchesEvent) > 0) {
            $this->errorsRepository->delete($error);
        }
    }
}