<?php


namespace common\services\general\errors;

use common\components\dictionaries\base\ErrorDictionary;
use common\models\Error;
use common\models\work\ErrorsWork;
use common\models\work\UserWork;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\event\EventRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\general\ErrorsRepository;
use common\repositories\general\UserRepository;
use common\repositories\order\DocumentOrderRepository;
use common\repositories\rubac\UserPermissionFunctionRepository;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\event\EventWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\rubac\PermissionFunctionWork;
use frontend\models\work\team\ActParticipantWork;
use frontend\services\order\DocumentOrderService;
use Yii;
use yii\helpers\ArrayHelper;

class ErrorService
{
    private ErrorsRepository $errorsRepository;
    private UserRepository $userRepository;
    private EventRepository $eventRepository;
    private ActParticipantRepository $actParticipantRepository;
    private TrainingGroupRepository $groupRepository;
    private TrainingProgramRepository $programRepository;
    private DocumentOrderRepository $orderRepository;
    private DocumentOrderService $orderService;

    public function __construct(
        ErrorsRepository $errorsRepository,
        UserRepository $userRepository,
        EventRepository $eventRepository,
        ActParticipantRepository $actParticipantRepository,
        TrainingGroupRepository $groupRepository,
        TrainingProgramRepository $programRepository,
        DocumentOrderRepository $orderRepository,
        DocumentOrderService $orderService
    )
    {
        $this->errorsRepository = $errorsRepository;
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->groupRepository = $groupRepository;
        $this->programRepository = $programRepository;
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
    }

    /**
     * Возвращает список ошибок для отправки по e-mail (или как-то еще)
     *
     * @param int $userId
     * @return ErrorsWork[]
     */
    public function getErrorsByUser(int $userId, array $types = [Error::TYPE_BASE, Error::TYPE_CRITICAL]) : array
    {
        /** @var UserWork $user */
        $user = $this->userRepository->get($userId);
        $errorsEvent = [];
        $errorsForeignEvent = [];
        $errorsJournal = [];
        $errorsProgram = [];
        $errorsOrder = [];
        // Поиск ошибок по учету достижения и мероприятиям
        if (Yii::$app->rubac->checkPermission($userId, 'get_achieve_errors')) {
            // Находим ID мероприятий в соответствии с отделом пользователя (или все мероприятия)
            if (Yii::$app->rubac->checkPermission($userId, 'get_all_errors')) {
                // Для того кому надо получать вообще все ошибки
                $eventIds = ArrayHelper::getColumn($this->eventRepository->getAll(), 'id');
                $foreignEventIds = ArrayHelper::getColumn($this->actParticipantRepository->getAll(), 'foreign_event_id');
            }
            else {
                // А это для обычных холопов
                $eventIds = ArrayHelper::getColumn(
                    $this->eventRepository->getEventsByBranches([$user->akaWork->branch]),
                    'id'
                );
                $foreignEventIds = ArrayHelper::getColumn(
                    $this->actParticipantRepository->getActsByBranches([$user->akaWork->branch]),
                    'foreign_event_id'
                );
            }

            // Ищем только те ошибки, которые связаны с найденными мероприятиями
            $errorsEvent = $this->errorsRepository->getErrorsByTableRowsBranchTypes(EventWork::tableName(), $eventIds, null, [Error::TYPE_CRITICAL]);
            $errorsForeignEvent = $this->errorsRepository->getErrorsByTableRowsBranchTypes(ForeignEventWork::tableName(), $foreignEventIds, null, [Error::TYPE_CRITICAL]);
        }

        // Поиск ошибок по журналу (учебной деятельности)
        if (Yii::$app->rubac->checkPermission($userId, 'get_journal_errors')) {
            if (Yii::$app->rubac->checkPermission($userId, 'get_all_errors')) {
                // Получаем все учебные группы и программы (для суперконтролера)
                $groupIds = ArrayHelper::getColumn($this->groupRepository->getAll(), 'id');
                $programIds = ArrayHelper::getColumn($this->programRepository->getAll(), 'id');
            }
            else if (Yii::$app->rubac->checkPermission($userId, 'get_branch_errors')) {
                // Получаем группы и программы соответствующего отдела (для контролеров в отделе)
                $groupIds = ArrayHelper::getColumn($this->groupRepository->getByBranches([$user->akaWork->branch]), 'id');
                $programIds = ArrayHelper::getColumn($this->programRepository->getByBranches([$user->akaWork->branch]), 'id');
            }
            else {
                // Получаем личные группы пользователя (для педагогов)
                $groupIds = ArrayHelper::getColumn($this->groupRepository->getByTeacher($userId), 'id');
                $programIds = [];
            }

            $errorsJournal = $this->errorsRepository->getErrorsByTableRowsBranchTypes(TrainingGroupWork::tableName(), $groupIds, null, [Error::TYPE_CRITICAL]);
            $errorsProgram = $this->errorsRepository->getErrorsByTableRowsBranchTypes(TrainingProgramWork::tableName(), $programIds, null, [Error::TYPE_CRITICAL]);
        }

        // Поиск ошибок по документообороту
        if (Yii::$app->rubac->checkPermission($userId, 'get_document_errors')) {
            if (Yii::$app->rubac->checkPermission($userId, 'get_all_errors')) {
                // Получаем все приказы из системы
                $orderIds = ArrayHelper::getColumn($this->orderRepository->getAll(), 'id');
            }
            else {
                // Получаем только приказы по отделу
                $orderIds = $this->orderService->getOrdersByBranch($user->akaWork->branch);
            }

            $errorsOrder = $this->errorsRepository->getErrorsByTableRowsBranchTypes(DocumentOrderWork::tableName(), $orderIds, null, [Error::TYPE_CRITICAL]);
        }

        // Поиск ошибок по мат. ценностям
        if (Yii::$app->rubac->checkPermission($userId, 'get_material_errors')) {
            // deprecated
        }

        return array_merge($errorsEvent, $errorsForeignEvent, $errorsJournal, $errorsProgram, $errorsOrder);
    }

    public function amnestyErrors(string $tableName, int $rowId)
    {
        /** @var ErrorsWork[] $errors */
        $errors = $this->errorsRepository->getErrorsByTableRow($tableName, $rowId);
        foreach ($errors as $error) {
            $error->setAmnesty();
            $this->errorsRepository->save($error);
        }
    }

    public function isGroupRestrict(int $groupId)
    {
        /** @var ErrorsWork[] $errors */
        $errors = $this->errorsRepository->getErrorsByTableRow(TrainingGroupWork::tableName(), $groupId);
        return !((count($errors) == 0 || count($errors) == 1 && $errors[0]->error == ErrorDictionary::JOURNAL_017));
    }

}