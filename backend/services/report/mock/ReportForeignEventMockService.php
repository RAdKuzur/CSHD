<?php

namespace backend\services\report\mock;

use backend\builders\ParticipantReportBuilder;
use backend\services\report\interfaces\ForeignEventServiceInterface;
use backend\services\report\ReportFacade;
use common\components\dictionaries\base\EventLevelDictionary;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\event\ForeignEventRepository;
use frontend\models\work\event\ParticipantAchievementWork;
use Yii;
use yii\helpers\ArrayHelper;
use function Clue\StreamFilter\fun;

class ReportForeignEventMockService implements ForeignEventServiceInterface
{
    public array $events;
    public array $acts;
    public array $actsBranch;
    public array $achieves;

    public function __construct(
        array $events = [],
        array $acts = [],
        array $actsBranch = [],
        array $achieves = []
    )
    {
        $this->events = $events;
        $this->acts = $acts;
        $this->actsBranch = $actsBranch;
        $this->achieves = $achieves;
    }

    public function setMockData(
        array $events = [],
        array $acts = [],
        array $actsBranch = [],
        array $achieves = []
    )
    {
        $this->events = $events;
        $this->acts = $acts;
        $this->actsBranch = $actsBranch;
        $this->achieves = $achieves;
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
        $events = $this->getEventsByFilters($startDate, $endDate, $levels);
        $actsQuery = $this->getActsByBaseFilters(ArrayHelper::getColumn($events, 'id'), $branches, $focuses, $allowRemotes);

        $result = [];
        $tempSumPart = 0;
        $tempSumAchieve = 0;
        foreach ($levels as $level) {
            $participantQuery = $this->getParticipants($actsQuery, $level);
            $prizeQuery = $this->getPrizes($participantQuery);
            $winQuery = $this->getWinners($participantQuery);

            $result['levels'][$level] = [
                'participant' => count($participantQuery),
                'winners' => count($winQuery),
                'prizes' => count($prizeQuery)
            ];

            if (in_array($level, (new EventLevelDictionary())->getReportLevels())) {
                $tempSumPart += count($participantQuery);
                $tempSumAchieve +=
                    count($winQuery) +
                    count($prizeQuery);
            }
        }

        $result['percent'] = $tempSumPart != 0 ? round($tempSumAchieve / $tempSumPart, 2) : 0;

        return [
            'result' => $result,
            'debugData' => ''
        ];
    }

    public function getEventsByFilters(string $startDate, string $endDate, array $levels)
    {
        return array_filter($this->events, function ($event) use ($startDate, $endDate, $levels) {
            $isDateInRange = ($event['finish_date'] <= $endDate && $event['finish_date'] >= $startDate);
            $isLevelMatch = empty($levels) || in_array($event['level'], $levels);
            return $isDateInRange && $isLevelMatch;
        });
    }

    public function getActsByBaseFilters(array $eventIds, array $branches, array $focuses, array $allowRemotes)
    {
        return array_filter($this->acts, function ($act) use ($eventIds, $branches, $focuses, $allowRemotes) {
            $inEvents = empty($eventIds) || in_array($act['foreign_event_id'], $eventIds);
            $isFocuses = empty($focuses) || in_array($act['focus'], $focuses);
            $isAllowRemotes = empty($allowRemotes) || in_array($act['allow_remote'], $allowRemotes);
            $branchActs = array_filter($this->actsBranch, function ($actBranch) use ($branches) {
                return empty($branches) || in_array($actBranch['branch'], $branches);
            });

            $isBranch = in_array($act['id'], ArrayHelper::getColumn($branchActs, 'id'));

            return $inEvents && $isFocuses && $isAllowRemotes && $isBranch;
        });
    }

    public function getParticipants(array $acts, int $level)
    {
        return array_filter($acts, function ($act) use ($level) {
            $event = array_filter($this->events, function ($event) use ($act) {
                return $event['id'] == $act['foreign_event_id'];
            });
            return reset($event)['level'] == $level;
        });
    }

    public function getWinners(array $acts)
    {
        return array_filter($acts, function ($act) {
            $achieves = array_filter($this->achieves, function ($achieve) use ($act) {
                return $achieve['act_participant_id'] == $act['id'] && $achieve['type'] == ParticipantAchievementWork::TYPE_WINNER;
            });
            return !empty($achieves);
        });
    }

    public function getPrizes(array $acts)
    {
        return array_filter($acts, function ($act) {
            $achieves = array_filter($this->achieves, function ($achieve) use ($act) {
                return $achieve['act_participant_id'] == $act['id'] && $achieve['type'] == ParticipantAchievementWork::TYPE_PRIZE;
            });
            return !empty($achieves);
        });
    }
}