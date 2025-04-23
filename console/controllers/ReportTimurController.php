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

class ReportTimurController extends \yii\console\Controller
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
    public function actionTest()
    {
        foreach (self::BRANCHES as $branch )
        {
            // Выбираем все группы, которые хотя бы частично были в 2024 году
            $allGroups = TrainingGroupWork::find()
                ->joinWith('trainingProgram')
                ->where([
                    'and',
                    ['<=', 'start_date', '2024-12-31'],  // начались не позже конца 2024
                    ['>=', 'finish_date', '2024-01-01'],  // закончились не раньше начала 2024
                    ['branch' => $branch],
                    ['budget' => TrainingGroupWork::IS_BUDGET],
                    ['training_program.focus' => 5]
                ])
                ->all();

            if (empty($allGroups)) {
                echo "Нет групп, которые проходили в 2024 году.";
            }

        // Считаем общее количество уникальных участников
            $totalParticipants = (int)TrainingGroupParticipantWork::find()
                ->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])
                ->select('participant_id')
                ->distinct()
                ->count();
        // Считаем участников с >1 заявки
            $multipleApplicants = (int)TrainingGroupParticipantWork::find()
                ->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])
                ->select('participant_id')
                ->groupBy('participant_id')
                ->having('COUNT(*) > 1')
                ->count();
            var_dump($multipleApplicants);
            if ($totalParticipants > 0) {
                $percentage = ($multipleApplicants / $totalParticipants) * 100;
                var_dump(Yii::$app->branches->get($branch), round($percentage, 5) . "%");
            } else {
                echo "Нет данных об участниках.";
            }
        }

    }

    public function actionWinner()
    {
        $allEvents = ForeignEventWork::find()
            ->where([
                'and',
                ['<=', 'begin_date', '2024-12-31'],
                ['>=', 'end_date', '2024-01-01'],
                ['level' => EventLevelDictionary::REGIONAL],
            ])
            ->all();

        if (empty($allEvents)) {
            echo "Нет событий, которые проходили в 2024 году.";
        }

        $actIds = ArrayHelper::getColumn($allEvents, 'id');
        $acts = ActParticipantWork::find()
            ->where(['IN', 'foreign_event_id', $actIds])
            ->andWhere(['focus' => FocusDictionary::TECHNICAL])
            ->all();

        foreach (self::BRANCHES as $branch) {
            $branchActIds = ActParticipantBranchWork::find()
                ->select('act_participant_id')
                ->where(['IN', 'act_participant_id', ArrayHelper::getColumn($acts, 'id')])
                ->andWhere(['branch' => $branch])
                ->column();

            $BranchActs = array_filter($acts, fn($item) => in_array($item->id, $branchActIds));
            if (empty($BranchActs)) continue;

            $participants = SquadParticipantWork::find()
                ->select('participant_id')
                ->distinct()
                ->where(['IN', 'act_participant_id', ArrayHelper::getColumn($BranchActs, 'id')])
                ->column();

            if (empty($participants)) {
                var_dump(Yii::$app->branches->get($branch), "Нет участников");
                continue;
            }

            $winnerParticipants = SquadParticipantWork::find()
                ->select('participant_id')
                ->distinct()
                ->where(['IN', 'act_participant_id', ParticipantAchievementWork::find()
                    ->select('act_participant_id')
                    ->where(['IN', 'act_participant_id', ArrayHelper::getColumn($BranchActs, 'id')])
                ])
                ->column();

            $percentage = count($winnerParticipants) / count($participants) * 100;
            var_dump(Yii::$app->branches->get($branch), count($BranchActs), $percentage);
        }

    }


}