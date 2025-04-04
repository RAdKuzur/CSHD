<?php

namespace backend\services;

use common\models\scaffold\Certificate;
use common\models\scaffold\CertificateTemplates;
use common\repositories\educational\CertificateTemplatesRepository;
use common\services\DatabaseServiceInterface;
use frontend\models\work\CertificateTemplatesWork;
use Yii;

class CertificateTemplatesService implements DatabaseServiceInterface
{
    private CertificateTemplatesRepository $templatesRepository;

    public function __construct(
        CertificateTemplatesRepository $templatesRepository
    )
    {
        $this->templatesRepository = $templatesRepository;
    }

    public function isAvailableDelete($entityId)
    {
        $templates = $this->templatesRepository->checkDeleteAvailable(Certificate::tableName(), CertificateTemplates::tableName(), $entityId);

        return $templates;
    }

    /**
     * @param $id
     * @return false|int
     */
    public function delete($id)
    {
        if (count($this->isAvailableDelete($id)) > 0) {
            return false;
        }

        /** @var CertificateTemplatesWork $entity */
        $entity = $this->templatesRepository->get($id);
        return $this->templatesRepository->delete($entity);
    }
}