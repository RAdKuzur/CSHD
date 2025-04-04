<?php

namespace frontend\events\document_in;

use common\events\EventInterface;

class InOutDocumentLinkedEvent implements EventInterface
{
    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        // TODO: Implement execute() method.
    }
}