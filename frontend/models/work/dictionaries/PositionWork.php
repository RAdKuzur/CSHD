<?php

namespace frontend\models\work\dictionaries;

use common\models\scaffold\Position;
use morphos\Russian\NounDeclension;

class PositionWork extends Position
{
    public function getPositionName(){
        return $this->name;
    }
    public function getGenitivePositionName()
    {
        $parts = explode(' ', $this->name);
        $parts[0] = NounDeclension::getCase($parts[0], 'родительный');
        return implode(' ', $parts);
    }
}
