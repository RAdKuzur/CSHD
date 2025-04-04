<?php

namespace frontend\models\work\general;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\models\scaffold\People;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use InvalidArgumentException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property PeoplePositionCompanyBranchWork[] $positionCompanyWork
 */

class PeopleWork extends People implements PersonInterface
{
    use EventTrait;

    public $branches;
    public $positions;
    public $companies;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function() {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    public static function fill(
        $name,
        $surname,
        $patronymic
    )
    {
        $entity = new static();
        $entity->firstname = $name;
        $entity->surname = $surname;
        $entity->patronymic = $patronymic;
        return $entity;
    }

    public static function getFioTypes()
    {
        return [
            self::FIO_FULL => 'ФИО полностью',
            self::FIO_SURNAME_INITIALS => 'Фамилия и инициалы',
            self::FIO_WITH_POSITION => 'ФИО полностью и должность с местом работы в скобках',
            self::FIO_SURNAME_INITIALS_WITH_POSITION => 'Должность и Фамилия c инициалами',
        ];
    }

    public function getFIO(int $type) : string
    {
        switch ($type) {
            case self::FIO_FULL:
                return $this->getFullFio();
            case self::FIO_SURNAME_INITIALS:
                return $this->getSurnameInitials();
            case self::FIO_WITH_POSITION:
                return $this->getFioPosition();
            case self::FIO_SURNAME_INITIALS_WITH_POSITION:
                return $this->getPositionSurnameInitials();
            default:
                throw new InvalidArgumentException('Неизвестный тип вывода ФИО');
        }
    }

    public function getFullFio()
    {
        return "$this->surname $this->firstname $this->patronymic";
    }

    public function getSurnameInitials()
    {
        return $this->surname
            . ' ' . mb_substr($this->firstname, 0, 1)
            . '. ' . ($this->patronymic ? mb_substr($this->patronymic, 0, 1) . '.' : '');
    }

    public function getFioPosition()
    {
        return "{$this->getFullFio()} stub";
    }

    public function getPositionSurnameInitials()
    {
        return "{$this->getPositionName()} {$this->getSurnameInitials()}";
    }

    public function getPositionName()
    {
        return $this->positionWork ? $this->positionWork->getPositionName() : '';
    }

    public function getSexString()
    {
        switch ($this->sex) {
            case 0:
                return 'Мужской';
            case 1:
                return 'Женский';
            default:
                return 'Другое';
        }
    }

    public function getBranchByPost($post)
    {
        return $post["PeopleWork"]['branches'];
    }
    public function getPositionsByPost($post)
    {
        return $post["PeopleWork"]['positions'];
    }
    public function beforeValidate()
    {
        $this->firstname = str_replace(' ', '', $this->firstname);
        $this->surname = str_replace(' ', '', $this->surname);
        $this->patronymic = str_replace(' ', '', $this->patronymic);

        if ($this->birthdate !== '') {
            $this->birthdate = DateFormatter::format($this->birthdate, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        }

        return parent::beforeValidate();
    }

    public function inMainCompany()
    {
        return $this->company_id == Yii::$app->params["mainCompanyId"];
    }

    public function getPeoplePositionCompanyBranchWork()
    {
        return $this->hasMany(PeoplePositionCompanyBranchWork::className(), ['people_id' => 'id']);
    }
}
