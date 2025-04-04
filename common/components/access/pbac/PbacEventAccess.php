<?php

namespace common\components\access\pbac;

use common\components\access\pbac\data\PbacEventData;
use common\repositories\act_participant\ActParticipantBranchRepository;
use common\repositories\event\ForeignEventRepository;
use Yii;

class PbacEventAccess implements PbacComponentInterface
{
    private PbacEventData $data;
    private ForeignEventRepository $eventRepository;
    private ActParticipantBranchRepository $actParticipantBranchRepository;

    public function __construct(
        PbacEventData $data
    )
    {
        $this->data = $data;
        $this->eventRepository = Yii::createObject(ForeignEventRepository::class);
        $this->actParticipantBranchRepository = Yii::createObject(ActParticipantBranchRepository::class);
    }

    public function getAllowedEvents()
    {
        return $this->eventRepository->getByIds(
            $this->actParticipantBranchRepository->getEventIdsByBranches(
                $this->data->branches
            )
        );
    }
}