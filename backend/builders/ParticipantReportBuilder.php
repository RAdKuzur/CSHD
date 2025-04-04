<?php
namespace backend\builders;

use frontend\models\work\team\ActParticipantWork;
use yii\db\ActiveQuery;

class ParticipantReportBuilder
{
    public function query() : ActiveQuery
    {
        return ActParticipantWork::find();
    }

    public function joinWith(ActiveQuery $query, string $relation) : ActiveQuery
    {
        return $query->joinWith([$relation]);
    }

    public function filterByBranches(ActiveQuery $query, array $branches = []) : ActiveQuery
    {
        return $query->andWhere(['IN', 'act_participant_branch.branch', $branches]);
    }

    public function filterByEvents(ActiveQuery $query, array $eventIds = []) : ActiveQuery
    {
        return $query->andWhere(['IN', 'foreign_event_id', $eventIds]);
    }

    public function filterByFocuses(ActiveQuery $query, array $focuses = []) : ActiveQuery
    {
        return $query->andWhere(['IN', 'focus', $focuses]);
    }

    public function filterByAllowRemote(ActiveQuery $query, array $allowRemotes = []) : ActiveQuery
    {
        return $query->andWhere(['IN', 'allow_remote', $allowRemotes]);
    }

    public function filterByPrizes(ActiveQuery $query, array $prizeTypes = []) : ActiveQuery
    {
        return $query->andWhere(['IN', 'participant_achievement.type', $prizeTypes]);
    }

    public function filterByEventLevels(ActiveQuery $query, array $levels = []) : ActiveQuery
    {
        return $query->andWhere(['IN', 'foreign_event.level', $levels]);
    }

    public function filterByParticipantIds(ActiveQuery $query, array $participantIds = []) : ActiveQuery
    {
        return $query->andWhere(['IN', 'squad_participant.participant_id', $participantIds]);
    }
}