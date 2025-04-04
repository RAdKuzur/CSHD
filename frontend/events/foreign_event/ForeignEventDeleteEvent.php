<?php

namespace app\events\foreign_event;

use common\events\EventInterface;
use common\repositories\event\ForeignEventRepository;

class ForeignEventDeleteEvent implements EventInterface
{
    private $id;
    private ForeignEventRepository $foreignEventRepository;
    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->foreignEventRepository = new ForeignEventRepository();
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute()
    {
        // TODO: Implement execute() method.
        return [
            $this->foreignEventRepository->prepareDelete($this->id),
        ];
    }
}