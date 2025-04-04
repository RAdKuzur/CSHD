<?php

namespace frontend\models\work;

use common\models\scaffold\ObjectStates;

class ObjectStatesWork extends ObjectStates
{
    const STATE_FREE = 0;
    const STATE_READ = 1;
    const STATE_WRITE = 2;

}
