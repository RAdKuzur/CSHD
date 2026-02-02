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
 * @property PeoplePositionCompanyBranchWork[] $peoplePositionCompanyBranchWork
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
            case self::FIO_WITH_POSITION_COMPANY:
                return $this->getFioPositionCompany();
            default:
                throw new InvalidArgumentException('Неизвестный тип вывода ФИО');
        }
    }

    public function getFIOBranch(int $branchid)
    {
        $positions = implode(' ', $this->getPositionsBranch($branchid));
        return "{$this->getFullFio()} ({$positions})";
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
        $positions = implode(', ', $this->getPositions());
        return "{$this->getFullFio()} ({$positions})";
    }

    public function getFioPositionCompany()
    {
        $positions = $this->peoplePositionCompanyBranchWork;
        $positionCompany = array_map(function (PeoplePositionCompanyBranchWork $model) {
            return $model->companyWork->name . ' - ' . $model->positionWork->name;
        }, $positions);

        return $this->getFullFio() . ' (' . implode(', ', $positionCompany) . ')';
    }

    public function getPositionSurnameInitials()
    {
        $positions = implode(', ', $this->getPositions());
        return "{$this->getSurnameInitials()} ({$positions})";
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

    public function getPositions()
    {
        $positions = $this->peoplePositionCompanyBranchWork;
        return array_map(function (PeoplePositionCompanyBranchWork $model) {
            return $model->positionWork->name;
        }, $positions);
    }

    public function getPositionsBranch(int $branch): array
    {
        return array_values(array_filter(
            array_map(function (PeoplePositionCompanyBranchWork $model) use ($branch) {
                return $model->branch == $branch
                    ? $model->positionWork->name
                    : null;
            }, $this->peoplePositionCompanyBranchWork)
        ));
    }

    public function inMainCompany()
    {
        return $this->company_id == Yii::$app->params["mainCompanyId"];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        
        $inMainCompany = $this->inMainCompany();
        $fioChanged = $this->isAttributeChanged('surname') || 
                    $this->isAttributeChanged('firstname') || 
                    $this->isAttributeChanged('patronymic');
        

        if (!$inMainCompany) {
            $this->short = null;
            return true;
        }
        

        if (empty($this->short) || $fioChanged) {
            $this->short = $this->generateShortCode();
        }
        
        return true;
    }

    private function generateShortCode()
    {

        $code = mb_substr($this->surname, 0, 1, 'UTF-8');
        
        if (!empty($this->firstname)) {
            $code .= mb_substr($this->firstname, 0, 1, 'UTF-8');
        }
        
        if (!empty($this->patronymic)) {
            $code .= mb_substr($this->patronymic, 0, 1, 'UTF-8');
        }
        
        $code = mb_strtoupper($code, 'UTF-8');
        
        return $this->getUniqueShortCode($code);
    }

    private function getUniqueShortCode($baseCode)
    {
        $counter = 1;

        $existingCodes = self::find()
            ->select('short')
            ->where(['like', 'short', $baseCode . '%', false])
            ->andWhere(['company_id' => Yii::$app->params["mainCompanyId"]])
            ->andWhere(['!=', 'id', $this->id ?: 0])
            ->andWhere(['IS NOT', 'short', null])
            ->column();

        do {
            $testCode = $baseCode . $counter;
            $counter++;
        } while (in_array($testCode, $existingCodes) && $counter <= 100);
        
        return $testCode;
    }

    public function getPeoplePositionCompanyBranchWork()
    {
        return $this->hasMany(PeoplePositionCompanyBranchWork::className(), ['people_id' => 'id']);
    }

}
