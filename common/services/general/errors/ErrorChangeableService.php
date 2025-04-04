<?php

namespace common\services\general\errors;

use common\models\Error;
use common\models\work\ErrorsWork;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\general\ErrorsRepository;
use common\repositories\order\DocumentOrderRepository;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\order\DocumentOrderWork;

class ErrorChangeableService
{
    private ErrorsRepository $errorsRepository;
    private DocumentOrderRepository $orderRepository;
    private TrainingGroupRepository $groupRepository;

    public function __construct(
        ErrorsRepository $errorsRepository,
        DocumentOrderRepository $orderRepository,
        TrainingGroupRepository $groupRepository
    )
    {
        $this->errorsRepository = $errorsRepository;
        $this->orderRepository = $orderRepository;
        $this->groupRepository = $groupRepository;
    }

    // Методы для изменения состояния ошибок с обычных на критические

    // Если прошло больше 3 дней с даты приказа
    public function changeDocument_001($errorId)
    {
        $daysCount = 3;
        /** @var ErrorsWork $error */
        /** @var DocumentOrderWork $order */
        $error = $this->errorsRepository->get($errorId);
        $order = $this->orderRepository->get($error->table_row_id);

        $currentDate = strtotime($order->order_date);
        $upperBound = strtotime("+$daysCount day", $currentDate);
        $targetDate = strtotime('today');

        if ($targetDate > $upperBound) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }
    }

    // Если на момент начала занятий не было прикреплено ни одного педагога к группе
    public function changeJournal_001($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);

        if (strtotime($group->start_date) >= strtotime('today')) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }
    }

    // Ошибка сохранилась до даты окончания обучения
    public function changeJournal_002($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);

        if (strtotime($group->finish_date) >= strtotime('today')) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }
    }

    // Ошибка не была исправлена ранее, чем за 7 дней до даты окончания занятий
    public function changeJournal_003($errorId)
    {
        $daysCount = 7;
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);

        $currentDate = strtotime($group->finish_date);
        $lowerBound = strtotime("-$daysCount day", $currentDate);
        $targetDate = strtotime('today');

        if ($targetDate > $lowerBound) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }
    }

    // Ошибка не была исправлена до окончания занятий (включительно)
    public function changeJournal_004($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);

        if (strtotime('today') > strtotime($group->finish_date)) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }
    }

    // Ошибка не была исправлена до окончания занятий (включительно)
    public function changeJournal_005($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);

        if (strtotime('today') > strtotime($group->finish_date)) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }
    }

    // Ошибка не была исправлена до окончания занятий (не включительно)
    public function changeJournal_006($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);

        if (strtotime('today') >= strtotime($group->finish_date)) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }
    }

    // Ошибка не была исправлена спустя 3 дня после ее возникновения
    public function changeJournal_009($errorId)
    {
        $daysCount = 3;
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);

        $currentDate = strtotime($error->create_datetime);
        $upperBound = strtotime("+$daysCount day", $currentDate);
        $targetDate = strtotime('today');

        if ($targetDate > $upperBound) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }

    }

    // Если программа стала привязанной хотя бы к одной группе
    public function changeJournal_012($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $groups = $this->groupRepository->getByProgramId($error->table_row_id);

        if (count($groups) > 0) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }
    }

    // Если спустя 2 недели после окончания занятий группа еще не помещена в архив
    public function changeJournal_017($errorId)
    {
        $daysCount = 14;
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);

        $currentDate = strtotime($group->finish_date);
        $upperBound = strtotime("+$daysCount day", $currentDate);
        $targetDate = strtotime('today');

        if ($targetDate > $upperBound) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }
    }

    // Ошибка не исправлена за 7 дней до окончания занятий
    public function changeJournal_021($errorId)
    {
        $daysCount = 7;
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);

        $currentDate = strtotime($group->finish_date);
        $lowerBound = strtotime("-$daysCount day", $currentDate);
        $targetDate = strtotime('today');

        if ($targetDate > $lowerBound) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }
    }

    // Ошибка не исправлена за 3 дня до окончания занятий
    public function changeJournal_022($errorId)
    {
        $daysCount = 3;
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);

        $currentDate = strtotime($group->finish_date);
        $lowerBound = strtotime("-$daysCount day", $currentDate);
        $targetDate = strtotime('today');

        if ($targetDate > $lowerBound) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }
    }

    // Ошибка не исправлена за 3 дня до окончания занятий
    public function changeJournal_023($errorId)
    {
        $daysCount = 3;
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);

        $currentDate = strtotime($group->finish_date);
        $lowerBound = strtotime("-$daysCount day", $currentDate);
        $targetDate = strtotime('today');

        if ($targetDate > $lowerBound) {
            $error->setState(Error::TYPE_CRITICAL);
            $this->errorsRepository->save($error);
        }
    }

    public function changeDocument_013($errorId)
    {

    }
}