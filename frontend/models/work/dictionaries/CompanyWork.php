<?php

namespace frontend\models\work\dictionaries;

use common\models\scaffold\Company;
use Yii;

class CompanyWork extends Company
{
    public static function fill(
        $name,
        $shortName,
        $isContractor,
        $companyType = null,
        $inn = null,
        $categorySmsp = null,
        $comment = null,
        $phoneNumber = null,
        $email = null,
        $site = null,
        $ownershipType = null,
        $okved = null,
        $headFio = null
    )
    {
        $entity = new static();
        $entity->name = $name;
        $entity->short_name = $shortName;
        $entity->is_contractor = $isContractor;
        $entity->company_type = $companyType;
        $entity->inn = $inn;
        $entity->category_smsp = $categorySmsp;
        $entity->comment = $comment;
        $entity->phone_number = $phoneNumber;
        $entity->email = $email;
        $entity->site = $site;
        $entity->ownership_type = $ownershipType;
        $entity->okved = $okved;
        $entity->head_fio = $headFio;

        return $entity;
    }

    public static function fastFillWithId(
        $id,
        $name,
        $shortName,
        $isContractor
    )
    {
        $entity = new static();
        $entity->id = $id;
        $entity->name = $name;
        $entity->short_name = $shortName;
        $entity->is_contractor = $isContractor;

        return $entity;
    }

    public function isMainCompany()
    {
        return $this->id == Yii::$app->params['mainCompanyId'];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getShortName()
    {
        return $this->short_name;
    }
}
