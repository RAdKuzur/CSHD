<?php

namespace console\controllers;

use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\EventLevelDictionary;
use common\components\dictionaries\base\FocusDictionary;
use common\models\scaffold\ForeignEvent;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\event\EventTrainingGroupWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\team\ActParticipantBranchWork;
use frontend\models\work\team\ActParticipantWork;
use frontend\models\work\team\SquadParticipantWork;
use Yii;
use yii\helpers\ArrayHelper;
class ReportRufatController extends \yii\console\Controller
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

    public function actionFindPercent() {
        for ($i = 1; $i <= 5; $i++)
        {
            foreach (self::BRANCHES as $branch) {
                $branchGroups = TrainingGroupWork::find()
                    ->joinWith('trainingProgram')
                    ->andWhere(['and',
                        ['<=', 'start_date', '2024-12-31'],
                        ['>=', 'finish_date', '2024-01-01'],
                        ['branch' => $branch],
                        ['budget' => TrainingGroupWork::IS_BUDGET],
                        ['training_program.focus' => $i]
                    ])->all();

                $groupsId = ArrayHelper::getColumn($branchGroups, 'id');

                $participantsAll = TrainingGroupParticipantWork::find()
                    ->andWhere(['IN', 'training_group_id', $groupsId])
                    ->select('participant_id')
                    ->distinct()
                    ->count();

                $participantsMulti = TrainingGroupParticipantWork::find()
                    ->andWhere(['IN', 'training_group_id', $groupsId])
                    ->select('participant_id')
                    ->groupBy('participant_id')
                    ->having('COUNT(*) > 1')
                    ->count();

                if ($participantsAll != 0) {
                    $percent = ($participantsMulti / $participantsAll) * 100;
                    var_dump(Yii::$app->branches->get($branch), $percent);
                }
            }
        }
    }





}