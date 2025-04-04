<?php

namespace frontend\events\educational\certificate;

use common\events\EventInterface;
use common\repositories\educational\CertificateRepository;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use Yii;

class CertificateSetStatusEvent implements EventInterface
{
    private $id;
    private $status;

    private CertificateRepository $repository;

    public function __construct(
        $id,
        $status
    )
    {
        $this->id = $id;
        $this->status = $status;
        $this->repository = Yii::createObject(CertificateRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareSetStatus(
                $this->id,
                $this->status
            )
        ];
    }
}