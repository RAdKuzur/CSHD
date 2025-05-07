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

    public function actionTheme() {

        foreach (self::BRANCHES as $branch) {
            // 1. Группы по критериям (временные рамки, бюджет, фокус)
            $allGroups = TrainingGroupWork::find()
                ->joinWith('trainingProgram')
                ->where([
                    'and',
                    ['<=', 'start_date', '2024-12-31'],
                    ['>=', 'finish_date', '2024-01-01'],
                    ['branch' => $branch],
                    ['budget' => TrainingGroupWork::IS_BUDGET],
                    ['training_program.focus' => 2],
                ])
                ->all();

            // 2. Все участники этих групп
            $participantAll = ArrayHelper::getColumn(
                TrainingGroupParticipantWork::find()
                    ->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])
                    ->select('participant_id')
                    ->distinct()
                    ->all(),
                'participant_id'
            );

            // 3. Участники с темой проекта (но ещё не факт, что они что-то "представили")
            $allParticipantProject = ArrayHelper::getColumn(
                TrainingGroupParticipantWork::find()
                    ->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])
                    ->andWhere(['IS NOT', 'group_project_themes_id', null])
                    ->select('participant_id')
                    ->distinct()
                    ->all(),
                'participant_id'
            );

            // Вывод результата
            if (count($participantAll) !== 0) {
                $percent = count($allParticipantProject) / count($participantAll) * 100;
                var_dump( Yii::$app->branches->get($branch), round($percent, 2));
            }
        }

    }


    public function actionReportWinners()
    {
        for ($i = 1; $i <= 5; $i++) {
            foreach (self::BRANCHES as $branch) {
                // Получаем id мероприятий не ниже регионального уровня в 2024 году
                $eventIds = ForeignEventWork::find()
                    ->select('id')
                    ->where(['between', 'begin_date', '2024-01-01', '2024-12-31'])
                    ->andWhere(['>=', 'level', EventLevelDictionary::REGIONAL])
                    ->column();

                if (empty($eventIds)) {
                    continue;
                }

                // Получаем id актов с нужным focus, входящих в события и относящихся к текущему отделу
                $actIds = ActParticipantWork::find()
                    ->select('id')
                    ->where(['foreign_event_id' => $eventIds])
                    ->andWhere(['focus' => 4])
                    ->andWhere(['in', 'id', ActParticipantBranchWork::find()
                        ->select('act_participant_id')
                        ->where(['branch' => $branch])
                    ])
                    ->column();

                if (empty($actIds)) {
                    continue;
                }

                // Уникальные участники (все)
                $participantIds = SquadParticipantWork::find()
                    ->select('participant_id')
                    ->distinct()
                    ->where(['act_participant_id' => $actIds])
                    ->count();

                if (empty($participantIds)) {
                    continue;
                }

                // Получаем act_ids победителей
                $achievementActIds = ParticipantAchievementWork::find()
                    ->select('act_participant_id')
                    ->distinct()
                    ->where(['act_participant_id' => $actIds])
                    ->column();

                if (empty($achievementActIds)) {
                    $winnerPercentage = 0;
                } else {
                    // Получаем участников-победителей
                    $winnerIds = SquadParticipantWork::find()
                        ->select('participant_id')
                        ->distinct()
                        ->where(['act_participant_id' => $achievementActIds])
                        ->count();

                    $winnerPercentage = $participantIds > 0
                        ? round($winnerIds / $participantIds * 100, 2)
                        : 0;
                }

                echo Yii::$app->branches->get($branch) . ": {$winnerPercentage}% победителей\n";
            }
        }
    }



}