<?php

namespace backend\forms;

use common\Model;
use common\models\work\UserWork;
use common\repositories\general\UserRepository;
use common\repositories\rubac\PermissionFunctionRepository;
use common\repositories\rubac\PermissionTokenRepository;
use frontend\models\work\rubac\PermissionFunctionWork;
use frontend\models\work\rubac\PermissionTokenWork;
use Yii;

class TokensForm extends Model
{
    /**
     * @var UserWork[] $users
     * @var PermissionFunctionWork[] $permissions
     * @var PermissionTokenWork[] $tokens
     */
    public array $users;
    public array $permissions;
    public array $tokens;

    public $userId;
    public $permissionId;
    public $branch;
    public $duration;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->users = (Yii::createObject(UserRepository::class))->getAll();
        $this->permissions = (Yii::createObject(PermissionFunctionRepository::class))->getAllPermissions();
        $this->tokens = (Yii::createObject(PermissionTokenRepository::class))->getAll();
    }

    public function rules()
    {
        return [
            [['userId', 'permissionId', 'duration'], 'integer'],
            [['branch'], 'safe']
        ];
    }

    public function beforeValidate()
    {
        if ($this->branch == '') {
            $this->branch = null;
        }

        return parent::beforeValidate();
    }
}