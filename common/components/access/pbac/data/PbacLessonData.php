<?php

namespace common\components\access\pbac\data;

use common\models\work\UserWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;

class PbacLessonData extends PbacData
{
    public UserWork $user;
    public TrainingGroupWork $group;

    public function __construct(
        UserWork $user,
        TrainingGroupWork $group
    )
    {
        $this->user = $user;
        $this->group = $group;
    }
}