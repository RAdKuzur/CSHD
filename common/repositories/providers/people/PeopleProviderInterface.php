<?php

namespace common\repositories\providers\people;

use frontend\models\work\general\PeopleWork;

interface PeopleProviderInterface
{
    public function get($id);
    public function save(PeopleWork $model);
    public function delete(PeopleWork $model);
}