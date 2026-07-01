<?php

namespace console\controllers;

use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\EventLevelDictionary;
use common\models\scaffold\ForeignEvent;
use common\models\work\UserWork;
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
use frontend\services\ReportService;
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
     // Константы table_name
    private const T_GROUP = 'training_group';
    private const T_PROGRAM = 'training_program';
    private const T_EVENT = 'event';
    private const T_FOREIGN_EVENT = 'foreign_event';
    private const T_ORDER = 'document_order';

    // Комбинации таблиц для разных типов отчётов
    private const ALL_TABLES_EVENTS_ACHIEVEMENTS = [self::T_EVENT, self::T_FOREIGN_EVENT];
    private const ALL_TABLES_GROUPS_PROGRAMS = [self::T_GROUP, self::T_PROGRAM];
    private const ALL_TABLES_FULL = [self::T_GROUP, self::T_PROGRAM, self::T_EVENT, self::T_FOREIGN_EVENT, self::T_ORDER];
    private const ALL_TABLES_GROUPS_TRAINING_ORDERS = [self::T_GROUP, self::T_ORDER];
    private const ALL_TABLES_PROGRAMS = [self::T_PROGRAM];

    private ReportService $reportService;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }
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
                //var_dump(Yii::$app->branches->get($branch), count($winnerParticipants));
                var_dump(Yii::$app->branches->get($branch), count($winnerParticipants)/count($participants) * 100);
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

   /**
     * CRON: отправка отчета по ошибкам
     */
    public function actionSendErrorsReport()
    {
        $map = [
            17  => ['branches' => null, 'type' => 'all'],
            177  => ['branches' => null, 'type' => 'all'],
            146 => ['branches' => null, 'type' => 'orders_main'],
            130 => ['branches' => null, 'type' => 'events_achievements'],

            161 => ['branches' => [BranchDictionary::TECHNOPARK, BranchDictionary::MOBILE_QUANTUM], 'type' => 'branch_full'],
            99  => ['branches' => [BranchDictionary::TECHNOPARK, BranchDictionary::MOBILE_QUANTUM], 'type' => 'branch_events_achievements'],

            11  => ['branches' => [BranchDictionary::QUANTORIUM], 'type' => 'branch_full'],
            78  => ['branches' => [BranchDictionary::QUANTORIUM], 'type' => 'branch_programs'],
            131 => ['branches' => [BranchDictionary::COD], 'type' => 'branch_full'],
            132 => ['branches' => [BranchDictionary::COD], 'type' => 'branch_events_achievements'],
            29  => ['branches' => [BranchDictionary::COD], 'type' => 'branch_groups_programs'],
            10  => ['branches' => [BranchDictionary::CDNTT], 'type' => 'branch_full'],
            16  => ['branches' => [BranchDictionary::CDNTT], 'type' => 'branch_events_achievements'],
            25  => ['branches' => [BranchDictionary::CDNTT], 'type' => 'branch_groups_training_orders'],

            // // // Получатели, у которых нет записи в системе (указываем email)
            'iivanova@schooltech.ru' => ['branches' => [BranchDictionary::TECHNOPARK, BranchDictionary::MOBILE_QUANTUM], 'type' => 'branch_groups_training_orders'],
            'vlim@schooltech.ru'      => ['branches' => [BranchDictionary::QUANTORIUM], 'type' => 'branch_full_without_programs'],
        ];

        $reportService = Yii::$container->get(ReportService::class);

        $configs = [
            'branch_full' => [
                'tables' => self::ALL_TABLES_FULL,
                'filter' => [$this, 'trainingOrderFilter'],
            ],
            'branch_events_achievements' => [
                'tables' => self::ALL_TABLES_EVENTS_ACHIEVEMENTS,
                'filter' => null,
            ],
            'branch_groups_training_orders' => [
                'tables' => self::ALL_TABLES_GROUPS_TRAINING_ORDERS,
                'filter' => [$this, 'trainingOrderFilter'],
            ],
            'branch_groups_programs' => [
                'tables' => self::ALL_TABLES_GROUPS_PROGRAMS,
                'filter' => null,
            ],
            'branch_programs' => [
                'tables' => self::ALL_TABLES_PROGRAMS,
                'filter' => null,
            ],
            'branch_full_without_programs' => [
                'tables' => [self::T_GROUP, self::T_EVENT, self::T_FOREIGN_EVENT, self::T_ORDER],
                'filter' => [$this, 'trainingOrderFilter'],
            ],
        ];

        foreach ($map as $identifier => $params) {
            $branches = $params['branches'];
            $type = $params['type'];

            if (is_numeric($identifier)) {
                $userId = (int)$identifier;
                $email = UserWork::find()
                    ->select('email')
                    ->where(['id' => $userId])
                    ->scalar();
            } else {

                $email = $identifier;
                $userId = null;

                $user = UserWork::findOne(['email' => $email]);
                if ($user) {
                    $userId = $user->id;
                }
            }

            if (!$email) {
                echo "User {$identifier} has no email\n";
                continue;
            }

            $spreadsheet = null;
            $reportName = '';

            switch ($type) {
                case 'all':
                    $spreadsheet = $reportService->prepareErrorsReportByUser($userId ?? 17);
                    $reportName = 'Все_ошибки';
                    break;

                case 'orders_main':
                    $spreadsheet = $reportService->prepareMainActivityOrdersReport();
                    $reportName = 'Приказы_основной_деятельности';
                    break;

                case 'events_achievements':
                    $spreadsheet = $reportService->prepareEventsAndAchievementsReport();
                    $reportName = 'Мероприятия_и_достижения';
                    break;
                
                case 'branch_full':
                    $spreadsheet = $reportService->prepareMultiBranchReportByTables(
                        $branches,
                        $configs['branch_full']['tables'],
                        $configs['branch_full']['filter'],
                        $type
                    );
                    $reportName = 'Все_ошибки_отделов';
                    break;

                case 'branch_events_achievements':
                    $spreadsheet = $reportService->prepareMultiBranchReportByTables(
                        $branches,
                        $configs['branch_events_achievements']['tables'],
                        null,
                        $type
                    );
                    $reportName = 'Мероприятия_и_достижения_отделов';
                    break;

                case 'branch_groups_training_orders':
                    $spreadsheet = $reportService->prepareMultiBranchReportByTables(
                        $branches,
                        $configs['branch_groups_training_orders']['tables'],
                        $configs['branch_groups_training_orders']['filter'],
                        $type
                    );
                    $reportName = 'Группы_и_учебные_приказы_отделов';
                    break;

                case 'branch_groups_programs':
                    $spreadsheet = $reportService->prepareMultiBranchReportByTables(
                        $branches,
                        $configs['branch_groups_programs']['tables'],
                        null,
                        $type
                    );
                    $reportName = 'Группы_и_программы_отделов';
                    break;

                case 'branch_programs':
                    $spreadsheet = $reportService->prepareMultiBranchReportByTables(
                        $branches,
                        $configs['branch_programs']['tables'],
                        null,
                        $type
                    );
                    $reportName = 'Программы_отделов';
                    break;

                case 'branch_full_without_programs':
                    $spreadsheet = $reportService->prepareMultiBranchReportByTables(
                        $branches,
                        $configs['branch_full_without_programs']['tables'],
                        $configs['branch_full_without_programs']['filter'],
                        $type
                    );
                    $reportName = 'Группы_мероприятия_достижения_приказы_отделов';
                    break;

                default:
                    
                    if (isset($configs[$type]) && !empty($branches)) {
                        $cfg = $configs[$type];
                        $spreadsheet = $reportService->prepareMultiBranchReportByTables(
                            $branches,
                            $cfg['tables'],
                            $cfg['filter'] ?? null,
                            $type
                        );
                        $reportName = str_replace('branch_', '', $type);
                    }
                    break;
            }

            if (!$spreadsheet) {
                echo "No data for user {$identifier}\n";
                continue;
            }

            // Формируем заголовок письма
            $title = $this->getReportTitle($type, $branches);

            $sender = new \frontend\invokables\SendErrorsReport(
                $spreadsheet,
                $reportName,
                $email,
                $title
            );
            $sender();

            echo "Sent to {$email}\n";
        }
    }

    /**
     * Дополнительный фильтр: для приказов оставляет только учебные (type = ORDER_TRAINING)
     */
    public function trainingOrderFilter($error, $orderRepo): bool
    {
        if ($error->table_name !== self::T_ORDER) {
            return true;
        }
        $order = $orderRepo->get($error->table_row_id);
        return $order !== null && $order->isTraining();
    }

     /**
     * Заголовок письма в зависимости от типа отчёта и списка отделов
     */
    private function getReportTitle($type, $branches = null): string
    {
        $branchNames = '';
        if (is_array($branches) && !empty($branches)) {
            $names = array_map(function ($b) {
                return Yii::$app->branches->get($b);
            }, $branches);
            $branchNames = implode(', ', $names);
        }

        switch ($type) {
            case 'all':
                return 'все ошибки, зарегистрированные в системе за последнюю неделю';
            case 'orders_main':
                return 'ошибки в приказах по основной деятельности за последнюю неделю';
            case 'events_achievements':
                return 'ошибки в мероприятиях и учёте достижений за последнюю неделю';
            case 'branch_events_achievements':
                return "ошибки в мероприятиях и учёте достижений отделов {$branchNames} за последнюю неделю";
            case 'branch_full':
                return "все ошибки отделов {$branchNames} (учебные группы, программы, мероприятия, достижения, учебные приказы) за последнюю неделю";
            case 'branch_groups_training_orders':
                return "ошибки учебных групп и учебных приказов отделов {$branchNames} за последнюю неделю";
            case 'branch_groups_programs':
                return "ошибки учебных групп и программ отделов {$branchNames} за последнюю неделю";
            case 'branch_programs':
                return "ошибки образовательных программ отделов {$branchNames} за последнюю неделю";
            case 'branch_full_without_programs':
                return "ошибки отделов {$branchNames} (учебные группы, мероприятия, достижения, учебные приказы) за последнюю неделю";
            default:
                return 'отчет об ошибках';
        }
    }
}