<?php

namespace console\controllers;

use common\components\dictionaries\base\BranchDictionary;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\helpers\ArrayHelper;

class ReportController extends \yii\console\Controller
{
    public const START_DATE = '2024-01-01';
    public const END_DATE = '2025-01-01';
    public const BRANCH = BranchDictionary::TECHNOPARK;
    public function actionReport(){
        $allGroups = TrainingGroupWork::find()
            ->andWhere(['>=','start_date', self::START_DATE])
            ->andWhere(['<=', 'finish_date', self::END_DATE])
            ->andWhere(['branch' => self::BRANCH])
            ->all();
        $tgpAll = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])->all();
        $counter = TrainingGroupParticipantWork::find()
            ->select(['participant_id'])
            ->groupBy('participant_id')
            ->having('COUNT(*) = 1')
            ->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])
            ->count();
        var_dump(count($allGroups), $counter , count($tgpAll));
        var_dump( 100 - ($counter / count($tgpAll)) * 100);
    }
}