<?php

namespace frontend\models\work\dictionaries;

interface PersonInterface
{
    const SEX_MALE = 0;
    const SEX_FEMALE = 1;

    const FIO_FULL = 1;
    const FIO_SURNAME_INITIALS = 2;
    const FIO_WITH_POSITION = 3;
    const FIO_SURNAME_INITIALS_WITH_POSITION = 4;

    const BASE_BIRTHDATE = '1900-01-01';

    public function getFIO(int $type) : string;
}