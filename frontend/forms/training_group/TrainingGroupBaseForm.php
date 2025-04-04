<?php

namespace frontend\forms\training_group;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupRepository;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class TrainingGroupBaseForm extends Model
{
    use EventTrait;

    public $branch;
    public $budget;
    public $trainingProgramId;
    public $network;
    public $teachers;
    public $endLoadOrders;
    public $startDate;
    public $endDate;
    public $photos;
    public $presentations;
    public $workMaterials;
    public $photosStr;
    public $presentationsStr;
    public $workMaterialsStr;

    public $id;
    public $number;
    public $prevTeachers; // хранит текущее состояние динамической формы педагогов для редактирования текущей формы

    public static function tableName()
    {
        return 'training_group';
    }

    public function __construct($id = -1, $config = [])
    {
        parent::__construct($config);
        if ($id != -1) {
            /** @var TrainingGroupWork $model */
            $model = (Yii::createObject(TrainingGroupRepository::class))->get($id);
            $this->id = $model->id;
            $this->number = $model->number;
            $this->branch = $model->branch;
            $this->budget = $model->budget;
            $this->trainingProgramId = $model->training_program_id;
            $this->network = $model->is_network;
            $this->teachers = (Yii::createObject(TeacherGroupRepository::class))->getAllTeachersFromGroup($id);
            $this->prevTeachers = (Yii::createObject(TeacherGroupRepository::class))->getAllTeachersFromGroup($id);
            $this->endLoadOrders = $model->order_stop;
            $this->startDate = $model->start_date;
            $this->endDate = $model->finish_date;
            $this->photosStr = implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_PHOTO), 'link'));
            $this->presentationsStr = implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_PRESENTATION), 'link'));
            $this->workMaterialsStr = implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_WORK), 'link'));
        }
        else {
            $this->prevTeachers = [];
        }
    }

    public function rules()
    {
        return [
            [['startDate', 'endDate'], 'required'],
            [['branch', 'budget', 'trainingProgramId', 'network', 'endLoadOrders'], 'integer'],
            [['startDate', 'endDate', 'teachers', 'prevTeachers'], 'safe'],
            [['photos'], 'file',
                'extensions' => 'jpg, jpeg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxSize' => FilesHelper::_MAX_FILE_SIZE, 'maxFiles' => 10],
            [['presentations'], 'file',
                'extensions' => 'jpg, jpeg, png, pdf, ppt, pptx, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxSize' => FilesHelper::_MAX_FILE_SIZE, 'maxFiles' => 10],
            [['workMaterials'], 'file',
                'extensions' => 'jpg, jpeg, png, pdf, doc, docx, zip, rar, 7z, tag', 'maxSize' => FilesHelper::_MAX_FILE_SIZE, 'skipOnEmpty' => true, 'maxFiles' => 10],
        ];
    }

    public function beforeValidate()
    {
        $this->startDate = DateFormatter::format($this->startDate, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->endDate = DateFormatter::format($this->endDate, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate();
    }

    public function attributeLabels()
    {
        return [
            'branch' => 'Отдел, производящий учет',
            'trainingProgramId' => 'Образовательная программа',
            'network' => 'Сетевая форма обучения',
            'peopleId' => 'Педагог(-и)',
            'startDate' => 'Дата начала занятий',
            'endDate' => 'Дата окончания занятий',
            'budget' => 'Бюджет',
            'endLoadOrders' => 'Завершить загрузку приказов о зачислении/отчислении',
        ];
    }
}