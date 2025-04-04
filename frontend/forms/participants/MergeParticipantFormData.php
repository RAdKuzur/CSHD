<?php

namespace frontend\forms\participants;

use frontend\models\work\dictionaries\ForeignEventParticipantsWork;

class MergeParticipantFormData
{
    /**
     * @var ForeignEventParticipantsWork[] $participants
     */
    public array $participants;

    public function __construct(
        array $participants
    )
    {
        $this->participants = $participants;
    }
}