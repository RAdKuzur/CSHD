<?php

namespace frontend\models\work\educational\training_group;

use common\components\traits\ErrorTrait;
use common\helpers\html\HtmlCreator;
use common\helpers\StringFormatter;
use common\models\work\UserWork;
use frontend\models\work\dictionaries\PersonInterface;
use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\models\scaffold\TrainingGroup;
use common\repositories\dictionaries\PeopleRepository;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\general\PeopleStampWork;
use InvalidArgumentException;
use Yii;
use yii\helpers\Url;

/**
 * @property TrainingProgramWork $trainingProgramWork
 * @property PeopleStampWork $teacherWork
 * @property TeacherGroupWork[] $teachersWork
 * @property TrainingGroupExpertWork[] $expertsWork
 * @property GroupProjectThemesWork[] $groupProjectThemesWorks
 */

class TrainingGroupWork extends TrainingGroup
{
    use EventTrait, ErrorTrait;

    const ERROR_NO_PROGRAM = 1;
    const ERROR_THEMES_MISMATCH = 2;

    const NO_NETWORK = 0;
    const IS_NETWORK = 1;

    const NO_BUDGET = 0;
    const IS_BUDGET = 1;

    const NO_ARCHIVE = 0;
    const IS_ARCHIVE = 1;

    public static function fill(
        $startDate,
        $endDate,
        $open,
        $budget,
        $branch,
        $orderStop,
        $archive,
        $protectionDate,
        $protectionConfirm,
        $isNetwork,
        $state,
        $creatorId,
        $lastEditId
    )
    {
        $entity = new static();
        $entity->start_date = $startDate;
        $entity->finish_date = $endDate;
        $entity->open = $open;
        $entity->budget = $budget;
        $entity->branch = $branch;
        $entity->order_stop = $orderStop;
        $entity->archive = $archive;
        $entity->protection_date = $protectionDate;
        $entity->protection_confirm = $protectionConfirm;
        $entity->is_network = $isNetwork;
        $entity->state = $state;
        $entity->creator_id = $creatorId;
        $entity->last_edit_id = $lastEditId;

        return $entity;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'numberPretty' => 'Номер',
            'programName' => 'Образовательная программа',
            'branchString' => 'Отдел',
            'teachersList' => 'Педагог(-и)',
            'start_date' => 'Дата начала занятий',
            'finish_date' => 'Дата окончания занятий',
            'budgetString' => 'Бюджет',
            'key_words' => 'Ключевые слова',
        ]);
    }

    /**
     * Изменяем значение архивности/актуальности учебной группы
     * @param int $archive
     * @return void
     */
    public function setArchive(int $archive)
    {
        $this->archive = $archive;
    }

    /**
     * Проверяем является ли группа архивной
     * @return bool
     */
    public function isArchive()
    {
        return $this->archive == self::IS_ARCHIVE;
    }

    /**
     * Иконка архивного статуса
     * @return string
     */
    public function getRawArchive()
    {
        if ($this->isArchive()) {
            return HtmlCreator::archiveTooltip();
        }
        return '';
    }

    /**
     * Вывод названия учебной группы и иконки архива (если группа в архиве)
     * @return string
     */
    public function getNumberPretty()
    {
        return '<div class=flexx>' . $this->number . ' ' . $this->getRawArchive() . '</div>';
    }

    public function generateNumber($teacherId)
    {
        $level = $this->trainingProgramWork->level;
        $level++;
        $thematicDirection = $this->trainingProgramWork->thematic_direction ? Yii::$app->thematicDirection->getAbbreviation($this->trainingProgramWork->thematic_direction) : '';
        $date = DateFormatter::format($this->start_date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $teacherCode = (Yii::createObject(PeopleRepository::class)->get($teacherId))->short;
        $addCode = 1;

        $sameNameGroups = TrainingGroupWork::find()->where(['like', 'number', $this->number.'%', false])->andWhere(['!=', 'id', $this->id])->all();
        $pattern = '/\.(d+)$/';
        for ($i = 0; $i < count($sameNameGroups) - 1; $i++) {
            preg_match($pattern, $sameNameGroups[$i]->number, $matches);
            $number1 = $matches[1];
            preg_match($pattern, $sameNameGroups[$i + 1]->number, $matches);
            $number2 = $matches[1];
            if ($number2 - $number1 > 1) {
                $addCode = (string)((int)$number1 + 1);
                break;
            }
            $addCode = (string)((int)$number2 + 1);
        }

        $this->number = "$thematicDirection.$level.$teacherCode.$date.$addCode";

        return $this->number;
    }

    /**
     * Номер учебной группы
     * @return string|null
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Наименование образовательной программы
     * @return string|null
     */
    public function getProgramName()
    {
        $program = $this->trainingProgramWork;
        return $program ? $program->name : '---';
    }

    /**
     * Возвращает список преподавателей в формате текста или ссылки
     * @param int|null $formatter
     * @return string
     */
    public function getTeachersList(int $formatter = null)
    {
        $newTeachers = [];
        foreach ($this->teachersWork as $teacher) {
            /** @var TeacherGroupWork $teacher */
            if ($formatter == StringFormatter::FORMAT_LINK)
            {
                $newTeachers[] = StringFormatter::stringAsLink(
                    $teacher->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS),
                    Url::to([Yii::$app->frontUrls::PEOPLE_VIEW, 'id' => $teacher->teacherWork->people_id])
                );
            }
            else {
                $newTeachers[] = $teacher->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS);
            }
        }
        return implode('<br>', $newTeachers);
    }

    /**
     * Возвращает массив
     * link => форматированная ссылка на документ
     * id => ID записи в таблице files
     * @param $filetype
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_PHOTO:
                $addPath = FilesHelper::createAdditionalPath(TrainingGroupWork::tableName(), FilesHelper::TYPE_PHOTO);
                break;
            case FilesHelper::TYPE_PRESENTATION:
                $addPath = FilesHelper::createAdditionalPath(TrainingGroupWork::tableName(), FilesHelper::TYPE_PRESENTATION);
                break;
            case FilesHelper::TYPE_WORK:
                $addPath = FilesHelper::createAdditionalPath(TrainingGroupWork::tableName(), FilesHelper::TYPE_WORK);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }

    public function getTrainingProgramWork()
    {
        return $this->hasOne(TrainingProgramWork::class, ['id' => 'training_program_id']);
    }

    public function getActivity($orderId){
        if ($orderId != NULL) {
            $participants = TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all();
            foreach ($participants as $participant) {
                if (
                    OrderTrainingGroupParticipantWork::find()
                        ->andWhere(['training_group_participant_in_id' => $participant->id])
                        ->andWhere(['order_id' => $orderId])
                        ->count() +
                    OrderTrainingGroupParticipantWork::find()
                        ->andWhere(['training_group_participant_out_id' => $participant->id])
                        ->andWhere(['order_id' => $orderId])
                        ->count() > 0
                ) {
                    return 1;
                }
            }
        }
        return 0;
    }

    public function getBudgetString()
    {
        return $this->budget ?
            'Бюджет' :
            'Внебюджет';
    }

    public function haveProgram()
    {
        return !is_null($this->training_program_id);
    }

    public function getBranchString()
    {
        return Yii::$app->branches->get($this->branch);
    }

    public function setProtectionDate(string $protectionDate)
    {
        $this->protection_date = $protectionDate;
    }

    public function getThemesProjectPretty()
    {
        $result = '';
        foreach ($this->groupProjectThemesWorks as $groupProjectThemesWork) {
            $color = $groupProjectThemesWork->confirm ? 'green' : 'red';
            $result .= '<span style="color: ' . $color . '">' . $groupProjectThemesWork->projectThemeWork->name . '</span><br>';
        }

        return $result;
    }

    public function beforeSave($insert)
    {
        if (!(Yii::$app instanceof yii\console\Application)) {
            if ($this->creator_id == null) {
                $this->creator_id = Yii::$app->user->identity->getId();
            }
            $this->last_edit_id = Yii::$app->user->identity->getId();
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function getTeachersWork()
    {
        return $this->hasMany(TeacherGroupWork::className(), ['training_group_id' => 'id']);
    }

    public function getExpertsWork()
    {
        return $this->hasMany(TrainingGroupExpertWork::className(), ['training_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastEditorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'last_edit_id']);
    }

    public function getTrainingGroupExpertsWork()
    {
        return $this->hasMany(TrainingGroupExpertWork::class, ['training_group_id' => 'id']);
    }

    public function getGroupProjectThemesWorks()
    {
        return $this->hasMany(GroupProjectThemesWork::class, ['training_group_id' => 'id']);
    }
}