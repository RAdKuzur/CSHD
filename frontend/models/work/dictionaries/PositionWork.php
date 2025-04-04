<?php

namespace frontend\models\work\dictionaries;

use common\models\scaffold\Position;

class PositionWork extends Position
{
    public function getPositionName(){
        return $this->name;
    }
}
