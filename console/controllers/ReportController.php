<?php

namespace console\controllers;

use common\components\dictionaries\base\BranchDictionary;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\event\EventTrainingGroupWork;
use Yii;
use yii\helpers\ArrayHelper;

class ReportController extends \yii\console\Controller
{
    public const START_DATE = '2024-01-01';
    public const END_DATE = '2025-01-01';
    public const BRANCH = BranchDictionary::TECHNOPARK;
    public const BRANCHES = [
        BranchDictionary::TECHNOPARK,
        BranchDictionary::QUANTORIUM,
        BranchDictionary::CDNTT,
        BranchDictionary::MOBILE_QUANTUM ,
        BranchDictionary::COD,
    ];
    public function actionReportParticipantRepeat(){
        foreach(self::BRANCHES as $branch) {
            $allGroups = TrainingGroupWork::find()
                ->joinWith('trainingProgram') // Убедитесь, что связь `trainingProgram` определена в модели
                ->where([
                    'and',
                    ['<=', 'start_date', '2024-12-31'],
                    ['>=', 'finish_date', '2024-01-01'],
                    ['branch' => $branch],
                    ['budget' => TrainingGroupWork::IS_BUDGET],
                    ['training_program.focus' => 5] // Теперь `trainingProgram` доступна благодаря joinWith
                ])
                ->all();
            $participantsAll = TrainingGroupParticipantWork::find()
                ->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])
                ->select('participant_id')
                ->distinct()  // учитываем только уникальные participant_id
                ->count();
            $counter = TrainingGroupParticipantWork::find()
                ->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])
                ->select(['participant_id'])
                ->groupBy('participant_id')
                ->having('COUNT(*) = 1')  // только те participant_id, которые встречаются 1 раз
                ->count();
            if($participantsAll!= 0){
                var_dump(Yii::$app->branches->get($branch),  100 - ($counter / $participantsAll) * 100);
            }
        }
    }
    public function actionReportProjectParticipant(){
        foreach(self::BRANCHES as $branch){
            $allGroups = TrainingGroupWork::find()
                ->joinWith('trainingProgram')
                ->where(['and',
                    ['<=', 'start_date', '2024-12-31'],
                    ['>=', 'finish_date', '2024-01-01'],
                    ['branch' => $branch],
                    ['budget' => TrainingGroupWork::IS_BUDGET],
                    ['training_program.focus' => 1]

                ])
                ->all();
            $participantsAll = TrainingGroupParticipantWork::find()
                ->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])
                ->select('participant_id')
                ->distinct()  // учитываем только уникальные participant_id
                ->all();
            $counterGroups = array_unique(ArrayHelper::getColumn(GroupProjectThemesWork::find()
                ->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])
                ->all(), 'training_group_id'));
            $participantsCounter = TrainingGroupParticipantWork::find()
                ->where(['IN', 'training_group_id',$counterGroups])
                ->andWhere(['IS NOT', 'group_project_themes_id', NULL])
                ->select('participant_id')
                ->distinct()  // учитываем только уникальные participant_id
                ->all();
            if (count($participantsAll) != 0) {
                var_dump(Yii::$app->branches->get($branch), count($participantsCounter) / count($participantsAll) * 100);
            }
        }

    }
    public function actionTest(){
        $counter = 0;
        $allGroups = TrainingGroupWork::find()
            ->where(['and',
                ['<=', 'start_date', '2024-12-31'],
                ['>=', 'finish_date', '2024-01-01'],
                ['branch' => self::BRANCH],
                ['budget' => TrainingGroupWork::IS_BUDGET]
            ])
            ->all();
        $data = [];
        foreach ($allGroups as $group) {
            /* @var $participant TrainingGroupParticipantWork*/
            /* @var $project GroupProjectThemesWork*/
            $participants = TrainingGroupParticipantWork::find()->where(['training_group_id' => $group->id])->all();
            foreach ($participants as $participant) {
                $project = NULL;
                if ($participant->groupProjectThemesWork){
                    $project = $participant->groupProjectThemesWork->projectThemeWork->name;
                    $counter++;
                }
                $data[] = [$participant->getFullFio(), $project];
            }

        }
        $data = array_filter($data, function($item) {
            return $item[1] !== null;
        });
        $fullFios = array_column($data, 0); // Получаем массив ФИО
        $uniqueFullFios = array_unique($fullFios);   // Оставляем только уникальные
        $countUniqueParticipants = count($uniqueFullFios); // Считаем количество
        var_dump($countUniqueParticipants);var_dump($counter);
    }
    public function actionReportWinners(){

    }
}