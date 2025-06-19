<?php

namespace common\components\dictionaries\base;

class BenefitsDictionary extends BaseDictionary
{
    const NONE_BENEFITS = 0;
    const SVO = 1;

    public function  __construct(array $list = [])
    {
        parent::__construct($list);
        $this->list = [
            self::NONE_BENEFITS => 'Без льгот',
            self::SVO => 'Льготы по СВО',
        ];
    }

    /**
     * @inheritDoc
     */
    public function customSort()
    {
        return [
            $this->list[self::NONE_BENEFITS],
            $this->list[self::SVO],
        ];
    }
}