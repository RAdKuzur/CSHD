<?php

namespace common\repositories\providers\group_expert;

use frontend\models\work\educational\training_group\TrainingGroupExpertWork;

interface TrainingGroupExpertProviderInterface
{
    public function get($id);
    public function getExpertsFromGroup($groupId, $type);
    public function save(TrainingGroupExpertWork $model);
}