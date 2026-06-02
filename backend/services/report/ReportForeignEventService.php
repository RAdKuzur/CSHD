<?php

namespace backend\services\report;

use backend\builders\ParticipantReportBuilder;
use backend\services\report\interfaces\ForeignEventServiceInterface;
use common\components\dictionaries\base\BranchDictionary;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\event\ForeignEventRepository;
use frontend\models\work\event\ParticipantAchievementWork;
use Yii;
use yii\helpers\ArrayHelper;

class ReportForeignEventService implements ForeignEventServiceInterface
{
    private ForeignEventRepository $repository;
    private ParticipantReportBuilder $builder;
    private ActParticipantRepository $actRepository;
    private DebugReportService $debugService;

    public function __construct(
        ForeignEventRepository   $repository,
        ParticipantReportBuilder $builder,
        ActParticipantRepository $actRepository,
        DebugReportService       $debugService
    )
    {
        $this->repository = $repository;
        $this->builder = $builder;
        $this->actRepository = $actRepository;
        $this->debugService = $debugService;
    }

    public function calculateEventParticipants(
        string $startDate,
        string $endDate,
        array $branches,
        array $focuses,
        array $allowRemotes,
        array $levels = [],
        int $mode = ReportFacade::MODE_PURE
    )
    {
        $events = $this->repository->getByDatesAndLevels($startDate, $endDate, $levels);

        $actsQuery = $this->builder->query();
        $actsQuery = $this->builder->filterByEvents($actsQuery, ArrayHelper::getColumn($events, 'id'));
        $actsQuery = $this->builder->joinWith($actsQuery, 'foreignEventWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'actParticipantBranchWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'participantAchievementWork');

        $actsQuery = $this->builder->filterByBranches($actsQuery, array_merge($branches, [NULL]) );
        $actsQuery = $this->builder->filterByFocuses($actsQuery, array_merge($focuses, [NULL]) );
        $actsQuery = $this->builder->filterByAllowRemote($actsQuery, array_merge($allowRemotes, [null]));

        $result = [];
        $tempSumPart = 0;
        $tempSumAchieve = 0;
        foreach ($levels as $level) {
            $participantQuery = $this->builder->filterByEventLevels(clone $actsQuery, [$level]);
            $prizeQuery = $this->builder->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_PRIZE]);
            $winQuery = $this->builder->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_WINNER]);

            $winners = [];
            $prizers = [];
            foreach ($this->actRepository->findAll($winQuery) as $participant) {
                $winners[] = ArrayHelper::getColumn($participant->squadParticipantsWork, 'participant_id');
            }
            foreach ($this->actRepository->findAll($prizeQuery) as $participant) {
                $prizers[] = ArrayHelper::getColumn($participant->squadParticipantsWork, 'participant_id');
            }

            $result['levels'][$level] = [
                'participant' => count($this->actRepository->findAll($participantQuery)),
                'winners' => count(array_unique(array_merge(...$winners))),
                'prizes' => count(array_unique(array_merge(...$prizers))),
            ];

            if (in_array($level, Yii::$app->eventLevel->getReportLevels())) {
                $tempSumPart += count($this->actRepository->findAll($participantQuery));
                $tempSumAchieve +=
                    count($this->actRepository->findAll($winQuery)) +
                    count($this->actRepository->findAll($prizeQuery));
            }
        }

        $result['percent'] = $tempSumPart != 0 ? round($tempSumAchieve / $tempSumPart, 2) : 0;

        return [
            'result' => $result,
            'debugData' => $mode == ReportFacade::MODE_DEBUG ?
                $this->debugService->createEventDebugData($events) :
                ''
        ];
    }
}