<?php

namespace frontend\events\dictionaries;

use common\events\EventInterface;
use common\repositories\dictionaries\PeopleRepository;
use Yii;

class PeopleEventCreate implements EventInterface {
    private $name;
    private $surname;
    private $patronymic;
    private PeopleRepository $peopleRepository;
    public function __construct(
        $name, $surname, $patronymic
    )
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->patronymic = $patronymic;
        $this->peopleRepository = Yii::createObject(PeopleRepository::class);
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute()
    {
        // TODO: Implement execute() method.
        return [
            $this->peopleRepository->prepareCreate(
                $this->name,
                $this->surname,
                $this->patronymic
            )
        ];
    }
}
