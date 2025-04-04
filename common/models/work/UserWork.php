<?php

namespace common\models\work;

use common\models\scaffold\User;
use frontend\models\work\general\PeopleWork;
use Yii;
use yii\web\IdentityInterface;

/** @property PeopleWork $akaWork */

class UserWork extends User implements IdentityInterface
{
    public static function fill(
        string $firstname,
        string $surname,
        string $username,
        string $passwordHash,
        string $email,
        string $patronymic = null,
        string $authKey = null,
        string $passwordResetToken = null,
        int $aka = null
    )
    {
        $entity = new static();
        $entity->firstname = $firstname;
        $entity->surname = $surname;
        $entity->patronymic = $patronymic;
        $entity->username = $username;
        $entity->password_hash = $passwordHash;
        $entity->email = $email;
        $entity->auth_key = $authKey;
        $entity->password_reset_token = $passwordResetToken;
        $entity->aka = $aka;

        return $entity;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public function getFullName()
    {
        return $this->surname . ' ' . $this->firstname . ' ' . $this->patronymic;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    public function getAkaWork()
    {
        return $this->hasOne(PeopleWork::class, ['id' => 'aka']);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword(string $passwordHash)
    {
        $this->password_hash = $passwordHash;
    }

    public function beforeSave($insert)
    {
        if(!(Yii::$app instanceof yii\console\Application)) {
            if (Yii::$app->user->identity) {
                if ($this->creator_id == null) {
                    $this->creator_id = Yii::$app->user->identity->getId();
                }
                $this->last_edit_id = Yii::$app->user->identity->getId();
            }
        }

        return parent::beforeSave($insert);
    }
}