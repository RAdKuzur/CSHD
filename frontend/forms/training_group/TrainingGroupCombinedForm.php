<?php

namespace frontend\forms\training_group;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\educational\VisitRepository;
use common\repositories\order\OrderTrainingRepository;
use frontend\invokables\CalculateAttendance;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\CertificateWork;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\order\OrderTrainingWork;
use frontend\services\educational\TrainingGroupService;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @property TrainingProgramWork $trainingProgram
 * @property TrainingGroupWork $trainingGroup
 * @property OrderTrainingWork $orders
 * @property array $teachers
 */

class TrainingGroupCombinedForm extends Model
{
    use EventTrait;

    private TrainingGroupRepository $groupRepository;
    private TrainingProgramRepository $programRepository;
    private OrderTrainingRepository $orderRepository;
    private TeacherGroupRepository $teacherRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private TrainingGroupLessonRepository $lessonRepository;
    private GroupProjectThemesRepository $groupProjectRepository;
    private TrainingGroupExpertRepository $groupExpertRepository;
    private VisitRepository $visitRepository;

    private TrainingGroupService $service;


    public int $id;                 // учебной группы
    public string $number;             // номер группы
    public TrainingGroupWork $trainingGroup;      // учебная группа

    public TrainingProgramWork $trainingProgram;    // образовательная программа
    public array $teachers;           // педагоги
    public array $orders;             // приказы группы


    public $photoFiles;
    public $presentationFiles;
    public $workMaterialFiles;


    // ----------------------------

    // Информация об учениках группы
    public array $participants;
    // -----------------------------

    // Информация о занятиях группы
    public array $lessons;
    // -----------------------------

    // Информация о защитах
    public $protectionDate;
    public $themes;
    public $experts;

    // -----------------------------

    //Информация о существовании файлов
    public $photoExist;
    public $presentationExist;
    public $workExist;

    public function __construct(
        $id = -1,
        TrainingGroupRepository $groupRepository = null,
        TrainingProgramRepository $programRepository = null,
        OrderTrainingRepository $orderRepository = null,
        TeacherGroupRepository $teacherRepository = null,
        TrainingGroupParticipantRepository $participantRepository = null,
        TrainingGroupLessonRepository $lessonRepository = null,
        GroupProjectThemesRepository $groupProjectRepository = null,
        TrainingGroupExpertRepository $groupExpertRepository = null,
        VisitRepository $visitRepository = null,
        $config = []
    )
    {
        parent::__construct($config);
        if (is_null($groupRepository)) {
            $groupRepository = Yii::createObject(TrainingGroupRepository::class);
        }
        if (is_null($programRepository)) {
            $programRepository = Yii::createObject(TrainingProgramRepository::class);
        }
        if (is_null($orderRepository)) {
            $orderRepository = Yii::createObject(OrderTrainingRepository::class);
        }
        if (is_null($teacherRepository)) {
            $teacherRepository = Yii::createObject(TeacherGroupRepository::class);
        }
        if (is_null($participantRepository)) {
            $participantRepository = Yii::createObject(TrainingGroupParticipantRepository::class);
        }
        if (is_null($lessonRepository)) {
            $lessonRepository = Yii::createObject(TrainingGroupLessonRepository::class);
        }
        if (is_null($groupProjectRepository)) {
            $groupProjectRepository = Yii::createObject(GroupProjectThemesRepository::class);
        }
        if (is_null($groupExpertRepository)) {
            $groupExpertRepository = Yii::createObject(TrainingGroupExpertRepository::class);
        }
        if (is_null($visitRepository)) {
            $visitRepository = Yii::createObject(VisitRepository::class);
        }

        $this->groupRepository = $groupRepository;
        $this->programRepository = $programRepository;
        $this->orderRepository = $orderRepository;
        $this->teacherRepository = $teacherRepository;
        $this->participantRepository = $participantRepository;
        $this->lessonRepository = $lessonRepository;
        $this->groupProjectRepository = $groupProjectRepository;
        $this->groupExpertRepository = $groupExpertRepository;
        $this->visitRepository = $visitRepository;

        if ($id !== -1) {
            /** @var TrainingGroupWork $model */
            $model = $this->groupRepository->get($id);
            $this->fillBaseInfo($model);
            $this->fillParticipantsInfo($model);
            $this->fillLessonsInfo($model);
            $this->fillPitchInfo($model);
            $this->fillOrderInfo($model);
            $this->checkFilesExist();
        }
    }

    /**
     * Заполнение базовой информации об учебной группе и её файлах
     * @param TrainingGroupWork $model
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    private function fillBaseInfo(TrainingGroupWork $model)
    {
        $this->id = $model->id;
        $this->number = $model->number;
        $this->trainingGroup = $model;
        $this->trainingProgram = $this->programRepository->get($model->training_program_id);

        $this->teachers = $this->teacherRepository->getAllTeachersFromGroup($model->id);

        $this->photoFiles = $model->getFileLinks(FilesHelper::TYPE_PHOTO);
        $this->presentationFiles = $model->getFileLinks(FilesHelper::TYPE_PRESENTATION);
        $this->workMaterialFiles = $model->getFileLinks(FilesHelper::TYPE_WORK);
    }

    /**
     * Заполнение поля состав группы
     * @param TrainingGroupWork $model
     * @return void
     */
    private function fillParticipantsInfo(TrainingGroupWork $model)
    {
        $this->participants = $this->participantRepository->getParticipantsFromGroups([$model->id]);
    }

    /**
     * Заполнение поля расписания
     * @param TrainingGroupWork $model
     * @return void
     */
    private function fillLessonsInfo(TrainingGroupWork $model)
    {
        $this->lessons = $this->lessonRepository->getLessonsFromGroup($model->id);
    }

    /**
     * Заполнение полей дата защиты, темы и эксперты
     * @param TrainingGroupWork $model
     * @return void
     */
    private function fillPitchInfo(TrainingGroupWork $model)
    {
        $this->protectionDate = $model->protection_date;
        $this->themes = $this->groupProjectRepository->getProjectThemesFromGroup($model->id);
        $this->experts = $this->groupExpertRepository->getExpertsFromGroup($model->id);
    }

    /**
     * Заполнение поля приказы
     * @param TrainingGroupWork $model
     * @return void
     */
    private function fillOrderInfo(TrainingGroupWork $model)
    {
        $this->orders = $this->orderRepository->getAllByGroup($model->id);
    }

    /**
     * Вывод иконки архива у учебной группы
     * @return string
     */
    public function getRawArchive()
    {
        return $this->trainingGroup->getRawArchive();
    }

    /**
     * Выводит красивый список учеников
     * @return string
     */
    public function getPrettyParticipants()
    {
        $result = [];
        foreach ($this->participants as $participant) {
            /** @var TrainingGroupParticipantWork $participant */
            $partLink = StringFormatter::stringAsLink($participant->participantWork->getFIO(PersonInterface::FIO_FULL),
                Url::to([Yii::$app->frontUrls::PARTICIPANT_VIEW, 'id' => $participant->participant_id]));
            $certificateLink = StringFormatter::stringAsLink($participant->getRawCertificate(),
                Url::to([Yii::$app->frontUrls::CERTIFICATE_VIEW, 'id' => $participant->certificateWork->id]));

            $result[] = HtmlBuilder::createSubtitleAndClarification(
                $partLink,
                ' ' . $participant->participantWork->createRawDisclosurePDProhibited()
                . ' ' . $participant->getRawStatus()
                . ' ' . $certificateLink,
                '');
        }

        return HtmlBuilder::arrayToAccordion($result);
    }

    /**
     * Выводит красивое расписание
     * @return string
     */
    public function getPrettyLessons()
    {
        $result = [];
        if (is_array($this->lessons)) {
            foreach ($this->lessons as $lesson) {
                /** @var TrainingGroupLessonWork $lesson */
                $result[] = $lesson->getLessonPrettyString();
            }
        }
        return HtmlBuilder::arrayToAccordion($result);
    }

    /**
     * Информация о готовности учебной группы к итоговому контролю
     * @return string
     */
    public function getProtectionConfirm()
    {
        return $this->trainingGroup->protection_confirm == 1 ? 'Да' : 'Нет';
    }

    /**
     * Выводит сведения о защите работ: дату и тип контроля,
     * а также темы и экспертов при их наличии
     * @return string
     */
    public function getPrettyFinalControl()
    {
        $date = $this->protectionDate ? DateFormatter::format($this->protectionDate, DateFormatter::Ymd_dash, DateFormatter::dmY_dot): '---';
        $result[] = HtmlBuilder::createSubtitleAndClarification('Выдача сертификатов: ', $date . ' (' . $this->trainingProgram->getCertificateType() . ')');
        if ($this->themes) {
            $result[] = HtmlBuilder::createSubtitleAndClarification('Темы проектов: ', implode(', ', $this->getPrettyThemes()));
        }
        if ($this->experts) {
            $result[] = HtmlBuilder::createSubtitleAndClarification('Эксперты: ', implode(', ', $this->getPrettyExperts()));
        }

        return HtmlBuilder::arrayToAccordion($result);
    }

    /**
     * Текстовый массив тем проектов в формате
     * 'Название-проекта' ('Тип-проекта' проект)
     * @return array
     */
    public function getPrettyThemes()
    {
        $result = [];
        if (is_array($this->themes)) {
            foreach ($this->themes as $theme) {
                /** @var GroupProjectThemesWork $theme */
                $type = Yii::$app->projectType->get($theme->projectThemeWork->project_type);
                $result[] = "{$theme->projectThemeWork->name} ($type проект)";
            }
        }
        return $result;
    }

    /**
     * Текстовый массив экспертов в формате
     * (Внешний/Внутренний) ФИО
     * @return array
     */
    public function getPrettyExperts()
    {
        $result = [];
        if (is_array($this->experts)) {
            foreach ($this->experts as $expert) {
                /** @var TrainingGroupExpertWork $expert */
                $result[] = "({$expert->getExpertTypeString()}) {$expert->expertWork->getFIO(PersonInterface::FIO_WITH_POSITION)}";
            }
        }
        return $result;
    }

    /**
     * Текстовое представление отдела учебной группы
     * @return mixed|string|null
     */
    public function getBranch()
    {
        return $this->trainingGroup->branch ? Yii::$app->branches->get($this->trainingGroup->branch) : '---';
    }

    /**
     * Ссылки на преподов учебной группы
     * @return mixed
     */
    public function getTeachersRaw()
    {
        return $this->trainingGroup->getTeachersList(StringFormatter::FORMAT_LINK);
    }

    /**
     * Период обучения группы
     * @return string
     */
    public function getTrainingPeriod()
    {
        return DateFormatter::format($this->trainingGroup->start_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot)
            . ' - '
            . DateFormatter::format($this->trainingGroup->finish_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
    }

    /**
     * Ссылка на образовательную программу
     * @return string
     */
    public function getTrainingProgramRaw()
    {
        return $this->trainingProgram ?
            StringFormatter::stringAsLink(
                $this->trainingProgram->name,
                Url::to([Yii::$app->frontUrls::PROGRAM_VIEW, 'id' => $this->trainingProgram->id])
            ) : '---';
    }

    /**
     * Форма обучения бюджет/внебюджет и сетевая/несетевая форма
     * @return string
     */
    public function getFormStudy()
    {
        $budget = $this->trainingGroup->budget == 1 ? 'Бюджет' : 'Внебюджет';
        $network = $this->trainingGroup->is_network == 1 ? 'сетевая' : 'несетевая';
        return $budget . ' ' . $network;
    }

    /**
     * Информация о загрузке приказов
     * @return string
     */
    public function getConsentOrders()
    {
        if ($this->trainingGroup->archive == 0 && $this->trainingGroup->order_stop == 0) {
            return 'Разрешена';
        }
        return 'Запрещена';
    }

    /**
     * Ссылки на все приказы в которых фигурируют дети из учебной группы
     * @return false|string
     */
    public function getOrdersRaw()
    {
        $result = '';
        if (count($this->orders) == 0) {
            return '---';
        }

        foreach ($this->orders as $order)
        {
            $result .= StringFormatter::stringAsLink(
                'Приказ № '.$order->getFullName(),
                Url::to([Yii::$app->frontUrls::ORDER_TRAINING_VIEW, 'id' => $order->id])
            ) . '<br>';
        }

        return substr($result, 0, -4);
    }

    /**
     * Подсчет данных для человеко-часов группы
     * @return array
     */
    public function getManHoursPercent()
    {
        $max =
            count($this->lessonRepository->getLessonsFromGroup($this->id)) *
            count($this->participantRepository->getParticipantsFromGroups([$this->id]));

        $currentCalc = new CalculateAttendance(
            $this->visitRepository->getByTrainingGroup($this->id),
            $this->lessonRepository
        );

        return [$currentCalc(), $max];
    }

    /**
     * Красивый вывод выработки человеко часов
     * @return string
     */
    public function getPrettyManHoursPercent()
    {
        $manHours = $this->getManHoursPercent();
        return "{$manHours[0]} / {$manHours[1]}";
    }

    /**
     * Количество детей в группе
     * @return string
     */
    public function getCountParticipants()
    {
        return count($this->participants) . ' (включая отчисленных и переведенных)';
    }

    /**
     * Количество занятий в расписании
     * @return string
     */
    public function getCountLessons()
    {
        return count($this->lessons) . '  академ.часа';
    }

    /**
     * Проверка существования файлов для скачивания
     * @return void
     */
    public function checkFilesExist()
    {
        $this->photoExist = count($this->photoFiles) > 0;
        $this->presentationExist = count($this->presentationFiles) > 0;
        $this->workExist = count($this->workMaterialFiles) > 0;
    }

    /**
     * Вывод всех рабочих материалов учебной группы
     * @return string
     */
    public function getFullWorkFiles()
    {
        $link = '#';
        if ($this->workExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_WORK, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }

    /**
     * Вывод всех презентационных материалов учебной группы
     * @return string
     */
    public function getFullPresentationFiles()
    {
        $link = '#';
        if ($this->presentationExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_PRESENTATION, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }

    /**
     * Вывод всех фотоматериалов учебной группы
     * @return string
     */
    public function getFullPhotoFiles()
    {
        $link = '#';
        if ($this->photoExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_PHOTO, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }

    /**
     * Создатель учебной группы
     * @return string
     */
    public function getCreatorName()
    {
        $creator = $this->trainingGroup->creatorWork;
        return $creator ? $creator->getFullName() : '---';
    }

    /**
     * Последний редактор учебной группы
     * @return string
     */
    public function getLastEditorName()
    {
        $editor = $this->trainingGroup->lastEditorWork;
        return $editor ? $editor->getFullName() : '---';
    }
}