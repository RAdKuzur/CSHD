<?php

namespace console\controllers;

use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\EventLevelDictionary;
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
    public function actionReportParticipant(){
        foreach(self::BRANCHES as $branch) {
            $allGroups = TrainingGroupWork::find()
                ->joinWith('trainingProgram') // Убедитесь, что связь `trainingProgram` определена в модели
                ->where([
                    'and',
                    ['<=', 'start_date', '2024-12-31'],
                    ['>=', 'finish_date', '2024-01-01'],
                    ['branch' => $branch],
                    ['budget' => TrainingGroupWork::IS_BUDGET],
                    ['training_program.focus' => 4] // Теперь `trainingProgram` доступна благодаря joinWith
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
            if($participantsAll != 0){
                var_dump(Yii::$app->branches->get($branch),  100 - ($counter / $participantsAll) * 100);
            }
        }
    }
    public function actionReportProject(){
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
    public function actionProjects(){
        foreach (self::BRANCHES as $branch) {
            $counter = 0;
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
            $data = [];
            foreach ($allGroups as $group) {
                /* @var $participant TrainingGroupParticipantWork */
                /* @var $project GroupProjectThemesWork */
                $participants = TrainingGroupParticipantWork::find()->where(['training_group_id' => $group->id])->all();
                foreach ($participants as $participant) {
                    $project = NULL;
                    if ($participant->groupProjectThemesWork) {
                        $project = $participant->groupProjectThemesWork->projectThemeWork->name;
                        $counter++;
                    }
                    $data[] = [$participant->getFullFio(), $project];
                }
            }
            $data = array_filter($data, function ($item) {
                return $item[1] !== null;
            });
            $fullFios = array_column($data, 0); // Получаем массив ФИО
            $uniqueFullFios = array_unique($fullFios);   // Оставляем только уникальные
            $countUniqueParticipants = count($uniqueFullFios); // Считаем количество
            if ($counter != 0) {
                var_dump(Yii::$app->branches->get($branch), $countUniqueParticipants / count($participantsAll) * 100);
            }

        }
    }
    public function actionTest()
    {
        foreach (self::BRANCHES as $branch) {
            $allGroups = TrainingGroupWork::find()
                ->joinWith('trainingProgram')
                ->where(['and',
                    ['<=', 'start_date', '2024-12-31'],
                    ['>=', 'finish_date', '2024-01-01'],
                    ['branch' => $branch],
                    ['budget' => TrainingGroupWork::IS_BUDGET],
                    ['training_program.focus' => 2]
                ])
                ->all();
            $participantAll = TrainingGroupParticipantWork::find()
                ->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])
                ->select('participant_id')
                ->distinct()  // учитываем только уникальные participant_id
                ->all();

            $allParticipantProject = ArrayHelper::getColumn(TrainingGroupParticipantWork::find()
                ->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])
                ->andWhere(['IS NOT', 'group_project_themes_id', NULL])
                ->select('participant_id')
                ->distinct()
                ->all(), 'participant_id');
            $projects = GroupProjectThemesWork::find()->where(['IN', 'training_group_id', ArrayHelper::getColumn($allGroups, 'id')])->all();
            $participants = ArrayHelper::getColumn(TrainingGroupParticipantWork::find()->where(['IN', 'id' , ArrayHelper::getColumn($projects, 'id')])->all(), 'participant_id');
            /*if ($branch == BranchDictionary::MOBILE_QUANTUM) {
                var_dump($participants, $allParticipantProject );
            }*/
            /*$array = array_unique(array_merge($participants, $allParticipantProject));*/
            if( count($participantAll) != 0) {
                var_dump(Yii::$app->branches->get($branch), count($allParticipantProject) / count($participantAll) * 100);
            }
        }
    }
    public function actionReportWinners(){
        /* @var $participant TrainingGroupParticipantWork*/
        foreach(self::BRANCHES as $branch) {
            $allForeignEvents = ForeignEventWork::find()
                ->where(['and',
                    ['<=', 'begin_date', '2024-12-31'],
                    ['>=', 'end_date', '2024-01-01'],
                    ['>=' , 'level' , EventLevelDictionary::REGIONAL]
            ])->all();

            //уникальные foreign_event_participant
            $acts = ActParticipantWork::find()
                ->where(['IN', 'foreign_event_id', ArrayHelper::getColumn($allForeignEvents, 'id')])
                ->andWhere(['focus' => 1])
                ->all();
            //только акты из данного отдела:
            $acts = array_filter($acts, function (ActParticipantWork $item) use ($branch) {
                if(ActParticipantBranchWork::find()->where(['act_participant_id' => $item->id])->andWhere(['branch' => $branch])->exists()){
                    return true;
                }
                else {
                    return false;
                }
            });
            //все участники
            $participants = ArrayHelper::getColumn(SquadParticipantWork::find()->where(['IN','act_participant_id', ArrayHelper::getColumn($acts, 'id')])->all(),'participant_id');
            $participants = array_unique($participants);

            $achievementActs = ArrayHelper::getColumn(ParticipantAchievementWork::find()->where(['IN', 'act_participant_id', ArrayHelper::getColumn($acts, 'id')])->all(), 'act_participant_id');
            $actWinners = ActParticipantWork::find()->where(['IN', 'id', $achievementActs])->all();
            $winnerParticipants = ArrayHelper::getColumn(SquadParticipantWork::find()->where(['IN','act_participant_id', ArrayHelper::getColumn($actWinners, 'id')])->all(),'participant_id');
            $winnerParticipants = array_unique($winnerParticipants);
            if (count($acts) != 0){
                var_dump(Yii::$app->branches->get($branch), count($winnerParticipants));
                //var_dump(Yii::$app->branches->get($branch), count($winnerParticipants)/count($participants) * 100);
            }
        }
    }
    public function actionReportEventParticipant()
    {
        foreach (self::BRANCHES as $branch) {
            $allForeignEvents = ForeignEventWork::find()
                ->where(['and',
                    ['<=', 'begin_date', '2024-12-31'],
                    ['>=', 'end_date', '2024-01-01'],
                    ['>=' , 'level' , EventLevelDictionary::REGIONAL]
                ])->all();

            //уникальные foreign_event_participant
            $acts = ActParticipantWork::find()
                ->where(['IN', 'foreign_event_id', ArrayHelper::getColumn($allForeignEvents, 'id')])
                ->andWhere(['focus' => 1])
                ->all();
            //только акты из данного отдела:
            $acts = array_filter($acts, function (ActParticipantWork $item) use ($branch) {
                if(ActParticipantBranchWork::find()->where(['act_participant_id' => $item->id])->andWhere(['branch' => $branch])->exists()){
                    return true;
                }
                else {
                    return false;
                }
            });
            //все участники
            $participants = ArrayHelper::getColumn(SquadParticipantWork::find()->where(['IN','act_participant_id', ArrayHelper::getColumn($acts, 'id')])->all(),'participant_id');
            $participants = array_unique($participants);
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
            $generalCount = count($participantsAll) + count($participants);
            if ($generalCount != 0) {
                var_dump(Yii::$app->branches->get($branch), count($participants) / $generalCount * 100);
            }
        }
    }
}