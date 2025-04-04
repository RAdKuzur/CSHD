<?php

namespace frontend\components\wizards;

use common\components\files\CreateDirZip;
use common\helpers\common\BaseFunctions;
use common\helpers\files\FilesHelper;
use common\helpers\html\CertificateBuilder;
use frontend\events\educational\certificate\CertificateSetStatusEvent;
use frontend\helpers\CertificateHelper;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\CertificateWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Yii;

class CertificateWizard
{
    // Места итоговой загрузки сгенерированных сертификатов
    const DESTINATION_DOWNLOAD = 1;
    const DESTINATION_SERVER = 2;

    public static function downloadCertificate(
        CertificateWork $certificate,
        TrainingGroupParticipantWork $participant,
        int $destination,
        string $path = null
    )
    {
        if (strripos($certificate->certificateTemplatesWork->name, CertificateWork::TECHNOSUMMER)) {
            if (
                strripos($certificate->certificateTemplatesWork->name, CertificateWork::INTENSIVE) ||
                strripos($certificate->certificateTemplatesWork->name, CertificateWork::PRO)
            ) {
                $mpdf = CertificateWizard::certificateIntensive($certificate, $participant);
            }
            else {
                $mpdf = CertificateWizard::certificateTechnosummer($certificate, $participant);
            }
        }
        else if (strripos($certificate->certificateTemplatesWork->name, CertificateWork::SCHOOL)) {
            $mpdf = CertificateWizard::certificateSchool($certificate, $participant);
        }
        else {
            $mpdf = CertificateWizard::certificateStandard($certificate, $participant);
        }

        if ($destination === self::DESTINATION_DOWNLOAD) {
            $mpdf->Output(
                'Сертификат №'. $certificate->getCertificateLongNumber() . ' '.
                $participant->participantWork->getFIO(PersonInterface::FIO_FULL) .'.pdf',
                'D'
            );
            exit;
        }
        else {
            $certificateName = 'Certificate #'.
                $certificate->getCertificateLongNumber() . ' '.
                BaseFunctions::rus2EngTranslit($participant->participantWork->getFIO(PersonInterface::FIO_FULL));
            if (is_null($path)) {
                $mpdf->Output(Yii::$app->basePath.'/download/'. Yii::$app->user->identity->getId().'/'. $certificateName . '.pdf', 'F'); // call the mpdf api output as needed
            }
            else {
                $mpdf->Output($path . $certificateName . '.pdf', 'F');
            }

            return $certificateName;
        }
    }

    private static function certificateStandard(CertificateWork $certificate, TrainingGroupParticipantWork $participant)
    {
        $genderVerbs = CertificateHelper::getGenderVerbs($participant->participantWork);

        $trainedText = CertificateHelper::getMainText($participant, $genderVerbs);
        $size = CertificateHelper::getTextSize(strlen($trainedText));

        $content = CertificateBuilder::createStandardCertificate($certificate, $participant, $size, $trainedText);
        return CertificateBuilder::createPdfClass($content);
    }

    private static function certificateSchool(CertificateWork $certificate, TrainingGroupParticipantWork $participant)
    {
        $genderVerbs = CertificateHelper::getGenderVerbs($participant->participantWork);

        $content = CertificateBuilder::createSchoolCertificate($certificate, $participant, $genderVerbs);
        return CertificateBuilder::createPdfClass($content);
    }

    private static function certificateTechnosummer(CertificateWork $certificate, TrainingGroupParticipantWork $participant)
    {
        $content = CertificateBuilder::createTechnosummerCertificate($certificate, $participant);
        return CertificateBuilder::createPdfClass($content);
    }

    private static function certificateIntensive(CertificateWork $certificate, TrainingGroupParticipantWork $participant)
    {
        $genderVerbs = CertificateHelper::getGenderVerbs($participant->participantWork);

        $content = CertificateBuilder::createIntensiveCertificate($certificate, $participant, $genderVerbs);
        return CertificateBuilder::createPdfClass($content);
    }

    public static function archiveDownload()
    {
        $path = Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'/';
        $createZip = new CreateDirZip();
        $createZip->getFilesFromFolder($path, '');
        $fileName = 'archive_certificates_'.Yii::$app->user->identity->getId().'.zip';

        $fd = fopen($fileName, 'wb');
        fwrite($fd, $createZip->getZippedfile());
        fclose($fd);
        $createZip->forceDownload($fileName);
        unlink(Yii::$app->basePath.'/web/'.$fileName);
        FilesHelper::removeDirectory(Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'/');
    }

    /**
     * @param CertificateWork[] $certificates
     * @return void
     */
    public static function sendCertificates(array $certificates)
    {
        FilesHelper::createDirectory(Yii::$app->basePath . '/download/' . Yii::$app->user->identity->getId() . '_temp_certificates/');

        foreach ($certificates as $certificate) {
            self::sendCertificateToEmail($certificate);
        }

        FilesHelper::removeDirectory(Yii::$app->basePath . '/download/' . Yii::$app->user->identity->getId() . '_temp_certificates/');
    }

    public static function sendCertificateToEmail(CertificateWork $certificate)
    {
        $name = self::downloadCertificate($certificate, $certificate->trainingGroupParticipantWork, self::DESTINATION_SERVER, Yii::$app->basePath . '/download/' . Yii::$app->user->identity->getId() . '_temp_certificates/');
        $result = Yii::$app->mailer->compose()
            ->setFrom('noreply@schooltech.ru')
            ->setTo($certificate->trainingGroupParticipantWork->participant->email)
            ->setSubject('Сертификат об успешном прохождении программы ДО')
            ->setHtmlBody('Сертификат находится в прикрепленном файле.<br><br><br>Пожалуйста, обратите внимание, что это сообщение было сгенерировано и отправлено в автоматическом режиме. Не отвечайте на него. По всем вопросам обращайтесь по телефону 44-24-28 (единый номер).')
            ->attach(Yii::$app->basePath . '/download/' . Yii::$app->user->identity->getId() . '_temp_certificates/' . $name . '.pdf')
            ->send();
        if ($result) {
            $certificate->recordEvent(new CertificateSetStatusEvent($certificate->id, CertificateWork::STATUS_SEND), CertificateWork::className());
        }
        else {
            $certificate->recordEvent(new CertificateSetStatusEvent($certificate->id, CertificateWork::STATUS_ERR_SEND), CertificateWork::className());
        }
        $certificate->releaseEvents();

        return $result;
    }
}