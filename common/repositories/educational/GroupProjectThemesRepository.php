<?php

namespace common\repositories\educational;

use common\components\traits\CommonDatabaseFunctions;
use common\repositories\providers\group_project_themes\GroupProjectThemesProvider;
use common\repositories\providers\group_project_themes\GroupProjectThemesProviderInterface;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use Yii;

class GroupProjectThemesRepository
{
    use CommonDatabaseFunctions;

    private $provider;

    public function __construct(GroupProjectThemesProviderInterface $provider = null)
    {
        if (!$provider) {
            $provider = Yii::createObject(GroupProjectThemesProvider::class);
        }

        $this->provider = $provider;
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function delete(GroupProjectThemesWork $model)
    {
        return $model->delete();
    }

    public function getProjectThemesFromGroup($groupId)
    {
        return $this->provider->getProjectThemesFromGroup($groupId);
    }

    public function prepareCreate($groupId, $themeId, $confirm)
    {
        return $this->provider->prepareCreate($groupId, $themeId, $confirm);
    }

    public function prepareDelete($id)
    {
        return $this->provider->prepareDelete($id);
    }

    public function save(GroupProjectThemesWork $theme)
    {
        return $this->provider->save($theme);
    }
}