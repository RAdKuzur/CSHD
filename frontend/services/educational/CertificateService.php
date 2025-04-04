<?php

namespace frontend\services\educational;

use common\helpers\files\FilesHelper;
use common\repositories\educational\CertificateRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use frontend\components\wizards\CertificateWizard;
use frontend\forms\certificate\CertificateForm;
use frontend\models\work\educational\CertificateWork;
use Yii;
use yii\helpers\FileHelper;

class CertificateService
{
    private CertificateRepository $repository;
    private TrainingGroupRepository $groupRepository;
    private TrainingGroupParticipantRepository $participantRepository;

    public function __construct(
        CertificateRepository $repository,
        TrainingGroupRepository $groupRepository,
        TrainingGroupParticipantRepository $participantRepository
    )
    {
        $this->repository = $repository;
        $this->groupRepository = $groupRepository;
        $this->participantRepository = $participantRepository;
    }

    /**
     * Функция сохранения сертификатов в БД
     *
     * @param CertificateForm $form
     * @return int[]
     */
    public function saveAllCertificates(CertificateForm $form)
    {
        $currentNumber = $this->getCurrentCertificateNumber();
        $ids = [];
        if (is_array($form->participants)) {
            foreach ($form->participants as $participantId) {
                $ids[] = $this->repository->save(
                    CertificateWork::fill(
                        $currentNumber,
                        $form->templateId,
                        $participantId,
                        CertificateWork::STATUS_CREATE
                    )
                );
                $currentNumber++;
            }
        }

        return $ids;
    }

    private function getCurrentCertificateNumber()
    {
        return $this->repository->getCount() + 1;
    }

    public function uploadCertificates(array $certificateIds)
    {
        FilesHelper::createDirectory(Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'/');
        foreach ($certificateIds as $id) {
            /** @var CertificateWork $certificate */
            $certificate = $this->repository->get($id);
            $participant = $certificate->trainingGroupParticipantWork;
            CertificateWizard::downloadCertificate($certificate, $participant, CertificateWizard::DESTINATION_SERVER);
        }
    }

    public function downloadCertificates()
    {
        CertificateWizard::archiveDownload();
    }

    public function buildGroupQuery(?int $groupId)
    {
        return $groupId ? $this->groupRepository->getQueryById($groupId) : null;
    }

    public function buildParticipantQuery(?int $groupId)
    {
        return $groupId ? $this->participantRepository->getQueryCertificateAllowed($groupId) : null;
    }
}