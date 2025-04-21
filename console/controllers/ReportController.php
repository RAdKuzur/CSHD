<?php

namespace console\controllers;

use common\components\dictionaries\base\BranchDictionary;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\event\EventTrainingGroupWork;
use yii\helpers\ArrayHelper;

class ReportController extends \yii\console\Controller
{
    public const START_DATE = '2024-01-01';
    public const END_DATE = '2025-01-01';
    public const BRANCH = BranchDictionary::TECHNOPARK;
    public function actionReportParticipantRepeat(){
        $allGroups = TrainingGroupWork::find()
            ->where(['and',
                ['<=', 'start_date', '2024-12-31'],
                ['>=', 'finish_date', '2024-01-01'],
                ['branch' => self::BRANCH],
                ['budget' => TrainingGroupWork::IS_BUDGET]
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
        var_dump( 100 - ($counter / $participantsAll) * 100);
    }
    public function actionReportProjectParticipant(){
        $allGroups = TrainingGroupWork::find()
            ->where(['and',
                ['<=', 'start_date', '2024-12-31'],
                ['>=', 'finish_date', '2024-01-01'],
                ['branch' => self::BRANCH],
                ['budget' => TrainingGroupWork::IS_BUDGET]
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
        var_dump(count($allGroups), count($counterGroups));
        $participantsCounter = TrainingGroupParticipantWork::find()
            ->where(['IN', 'training_group_id',$counterGroups])
            ->select('participant_id')
            ->distinct()  // учитываем только уникальные participant_id
            ->all();
        var_dump(count($participantsCounter));
        var_dump(count($participantsCounter) / count($participantsAll) * 100);
    }
    public function actionReportWinners(){

    }
}