<?php

namespace app\events;

use common\events\EventInterface;
use common\repositories\general\FilesRepository;
use common\repositories\regulation\RegulationRepository;
use common\services\general\files\FileService;

class RegulationChangeStatusEvent implements EventInterface
{
    private $id;
    private RegulationRepository $regulationRepository;
    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->regulationRepository = new RegulationRepository(new FileService(), new FilesRepository());
    }

    public function isSingleton(): bool
    {
        return false;
    }
    public function execute(){
        return [
            $this->regulationRepository->prepareChangeStatus($this->id)
        ];
    }
}