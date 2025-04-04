<?php

namespace backend\models\forms;

use backend\events\user\AddUserPermissionEvent;
use backend\events\user\DeleteUserPermissionEvent;
use common\components\compare\UserPermissionCompare;
use common\components\traits\Math;
use common\events\EventTrait;
use common\helpers\StringFormatter;
use common\Model;
use common\models\work\UserWork;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\rubac\PermissionFunctionWork;
use frontend\models\work\rubac\UserPermissionFunctionWork;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class UserForm extends Model
{
    use EventTrait;
    use Math;

    public UserWork $entity;

    /**
     * @var PeopleWork[] $peoples
     * @var int[] $userPermissions
     * @var int[] $prevUserPermissions
     * @var PermissionFunctionWork[] $prevUserPermissions
     * @var PermissionFunctionWork[] $permissions
     */
    public array $peoples;
    public array $userPermissions;
    public array $prevUserPermissions;
    public array $permissions;

    /**
     * @param UserWork $entity
     * @param PeopleWork[] $peoples
     * @param array $prevUserPermissions
     * @param PermissionFunctionWork[] $permissions
     * @param array $config
     */
    public function __construct(
        UserWork $entity,
        array $peoples,
        array $prevUserPermissions,
        array $permissions,
        array $config = []
    )
    {
        parent::__construct($config);
        $this->entity = $entity;
        $this->peoples = $peoples;
        $this->prevUserPermissions = $prevUserPermissions;
        $this->permissions = $permissions;
        $this->userPermissions = $prevUserPermissions;
    }

    public function load($data, $formName = null)
    {
        $mainDataLoad = parent::load($data, $formName);
        if ($mainDataLoad) {
            $this->entity->load($data);
            $this->entity->setPassword(
                Yii::$app->security->generatePasswordHash(
                    $this->entity->password_hash
                )
            );

            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            [['userPermissions'], 'safe']
        ];
    }

    public function savePermissions()
    {
        $prevPermissionIds = ArrayHelper::getColumn(
            $this->prevUserPermissions,
            'id'
        );

        $addPermissions = array_diff($this->userPermissions, $prevPermissionIds);
        $delPermissions = array_diff($prevPermissionIds, $this->userPermissions);

        foreach ($addPermissions as $permission) {
            $this->recordEvent(new AddUserPermissionEvent($this->entity->id, $permission), UserPermissionFunctionWork::class);
        }

        foreach ($delPermissions as $permission) {
            $this->recordEvent(new DeleteUserPermissionEvent($this->entity->id, $permission), UserPermissionFunctionWork::class);
        }
    }

    public function getPermissions()
    {
        return ArrayHelper::map($this->prevUserPermissions, 'id', 'name');
    }

    public function getAkaLink()
    {
        return $this->entity->aka ? StringFormatter::stringAsLink(
            $this->entity->akaWork->getFIO(PersonInterface::FIO_WITH_POSITION),
            Yii::$app->params['frontendDomain'] . Url::to(['/dictionaries/people/view', 'id' => $this->entity->aka])
        ) : '';
    }
}