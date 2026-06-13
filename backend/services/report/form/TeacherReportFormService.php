<?php

namespace backend\services\report\form;

use backend\builders\TrainingGroupReportBuilder;
use backend\forms\report\ManHoursReportForm;
use backend\forms\report\TeacherReportForm;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use yii\helpers\ArrayHelper;

class TeacherReportFormService
{
    const CALCULATE_TYPES = [
        ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN,
        ManHoursReportForm::PARTICIPANT_START_IN_FINISH_AFTER,
        ManHoursReportForm::PARTICIPANT_START_IN_FINISH_IN,
        ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_AFTER
    ];
    public TrainingGroupReportBuilder $trainingGroupReportBuilder;
    public function __construct(
        TrainingGroupReportBuilder $trainingGroupReportBuilder
    )
    {
        $this->trainingGroupReportBuilder = $trainingGroupReportBuilder;
    }

    public function createTeacherReportForm(TeacherReportForm $model){
        /**
         * @var TeacherGroupWork $teacherGroup
         */
        //данные об уроках по месяцам

        $dataLessons = [];

        $dataHours = [];
        //фильтрация по дате и отделу
        $groupsQuery = $this->trainingGroupReportBuilder->query();
        $groupsQuery = $this->trainingGroupReportBuilder->filterGroupsByDates($groupsQuery, $model->getYear().'-01-01', ($model->getYear() + 1).'-01-01', self::CALCULATE_TYPES);
        $groupsQuery = $this->trainingGroupReportBuilder->filterGroupsByBranches($groupsQuery, [$model->getBranch()]);
        $groupIds = ArrayHelper::getColumn($groupsQuery->all(), 'id');

        //teacher_groups

        $teacherGroupQuery = $this->trainingGroupReportBuilder->teacherGroupQuery();
        $teacherGroupQuery = $this->trainingGroupReportBuilder->filterTrainingGroupIDs($teacherGroupQuery, $groupIds);
        $teacherGroupQuery = $this->trainingGroupReportBuilder->orderByTeacherGroup($teacherGroupQuery);

        $teacherGroups = $teacherGroupQuery->all();

        foreach ($teacherGroups as $teacherGroup){

            // $teacherGroup->teacherWork->getFioPosition(); ---  ФИО и должность
            $fioPosition = $teacherGroup->teacherWork->getFioPosition();

            //добавление помесячных данных о занятиях
            foreach ($teacherGroup->trainingGroup->trainingGroupLessons as $lesson){
                $monthOfLesson = (new \DateTime($lesson->lesson_date))->format('n');
                $dataLessons[$lesson->id] = $monthOfLesson;
            }

            foreach ($teacherGroup->trainingGroup->trainingGroupParticipants as $participant){
                $lessons = json_decode($participant->visit->lessons); // это ячейка lessons в таблице  visits
                foreach ($lessons as $lesson){
                    if($lesson->status !== 555555) {
                        $month = $dataLessons[$lesson->lesson_id];
                        $dataHours[$teacherGroup->trainingGroup->id][$month]++;
                    }
                }
            }
        }
        foreach ($dataHours as $group => $item){
            var_dump($group , ' : ' , $item);
        }

    }
}