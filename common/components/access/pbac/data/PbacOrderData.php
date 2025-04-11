<?php

namespace common\components\access\pbac\data;

use common\models\work\UserWork;

class PbacOrderData extends PbacData
{
    public UserWork $user;
    public int $branch;

    public function __construct(
        UserWork $user,
        int $branch
    )
    {
        $this->user = $user;
        $this->branch = $branch;
    }
}