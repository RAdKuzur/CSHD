<?php

namespace common\repositories\providers\group_project_themes;

use frontend\models\work\educational\training_group\GroupProjectThemesWork;

interface GroupProjectThemesProviderInterface
{
    public function get($id);
    public function getProjectThemesFromGroup($groupId);
    public function save(GroupProjectThemesWork $model);
}