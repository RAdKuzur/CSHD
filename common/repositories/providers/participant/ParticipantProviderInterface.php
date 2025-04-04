<?php

namespace common\repositories\providers\participant;

use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;

interface ParticipantProviderInterface
{
    public function get($id);
    public function getParticipants(array $ids);
    public function delete(ForeignEventParticipantsWork $model);
    public function save(ForeignEventParticipantsWork $model);
}