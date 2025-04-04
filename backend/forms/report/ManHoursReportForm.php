<?php

namespace backend\forms\report;

use backend\services\report\ReportFacade;
use common\Model;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\general\PeopleStampRepository;
use frontend\models\work\general\PeopleWork;
use Yii;
use yii\helpers\ArrayHelper;

class ManHoursReportForm extends Model
{
    // Тип отчета
    const MAN_HOURS_REPORT = 1;
    // Типы отчетов по обучающимся
    const PARTICIPANT_START_BEFORE_FINISH_IN = 2;
    const PARTICIPANT_START_IN_FINISH_AFTER = 3;
    const PARTICIPANT_START_IN_FINISH_IN = 4;
    const PARTICIPANT_START_BEFORE_FINISH_AFTER = 5;

    // Подтип отчета по обучающимся
    const PARTICIPANTS_ALL = 1;
    const PARTICIPANTS_UNIQUE = 2;

    // Подтип отчета по человеко-часам
    const MAN_HOURS_FAIR = 1; // учитываем неявки
    const MAN_HOURS_ALL = 2; // игнорируем неявки


    private TeacherGroupRepository $teacherGroupRepository;
    private PeopleStampRepository $peopleStampRepository;
    private PeopleRepository $peopleRepository;

    public $startDate;
    public $endDate;
    public $type;
    public $unic;
    /*
     * 0 - человеко-часы
     * 1 - всего уникальных людей
     * 2 - всего людей
     */
    public $branch;
    public $budget;
    public $teacher;
    public $focus;
    public $allowRemote;
    public $method;
    public $mode;

    /**
     * @var PeopleWork[] $teachers
     */
    public array $teachers;

    public function __construct(
        TeacherGroupRepository $teacherGroupRepository,
        PeopleStampRepository $peopleStampRepository,
        PeopleRepository $peopleRepository,
        $config = []
    )
    {
        $this->teacherGroupRepository = $teacherGroupRepository;
        $this->peopleStampRepository = $peopleStampRepository;
        $this->peopleRepository = $peopleRepository;

        $teacherGroups = $this->teacherGroupRepository->getAll();
        $peopleStamps = $this->peopleStampRepository->getStamps(
            ArrayHelper::getColumn($teacherGroups, 'teacher_id')
        );

        $this->teachers = $this->peopleRepository->getByIds(
            ArrayHelper::getColumn($peopleStamps, 'people_id')
        );

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['startDate', 'endDate'], 'string'],
            [['type', 'branch', 'budget', 'focus', 'allowRemote'], 'safe'],
            [['method', 'teacher', 'unic', 'mode'], 'integer']
        ];
    }

    public static function fill(
        string $startDate,
        string $endDate,
        array $branch,
        array $focus,
        array $allowRemote,
        array $budget,
        array $method,
        array $teacherIds = [],
        int $mode = ReportFacade::MODE_PURE
    )
    {
        $entity = Yii::createObject(ManHoursReportForm::class);
        $entity->startDate = $startDate;
        $entity->endDate = $endDate;
        $entity->branch = $branch;
        $entity->focus = $focus;
        $entity->allowRemote = $allowRemote;
        $entity->budget = $budget;
        $entity->method = $method;
        $entity->teacherIds = $teacherIds;
        $entity->mode = $mode;

        return $entity;

    }

    public function isManHours()
    {
        return in_array(self::MAN_HOURS_REPORT, $this->type);
    }

    public function isParticipants()
    {
        return
            in_array(self::PARTICIPANT_START_BEFORE_FINISH_IN, $this->type) ||
            in_array(self::PARTICIPANT_START_IN_FINISH_AFTER, $this->type) ||
            in_array(self::PARTICIPANT_START_IN_FINISH_IN, $this->type) ||
            in_array(self::PARTICIPANT_START_BEFORE_FINISH_AFTER, $this->type);
    }

    public function save()
    {
        return true;
    }
}