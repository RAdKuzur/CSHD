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
        $path = Yii::$app->basePath . '/../' . $certificate->certificateTemplatesWork->path;
        return CertificateBuilder::createPdfClass($content, $path);
    }

    private static function certificateSchool(CertificateWork $certificate, TrainingGroupParticipantWork $participant)
    {
        $genderVerbs = CertificateHelper::getGenderVerbs($participant->participantWork);

        $content = CertificateBuilder::createSchoolCertificate($certificate, $participant, $genderVerbs);
        $path = Yii::$app->basePath . '/../' . $certificate->certificateTemplatesWork->path;
        return CertificateBuilder::createPdfClass($content, $path);
    }

    private static function certificateTechnosummer(CertificateWork $certificate, TrainingGroupParticipantWork $participant)
    {
        $content = CertificateBuilder::createTechnosummerCertificate($certificate, $participant);
        $path = Yii::$app->basePath . '/../' . $certificate->certificateTemplatesWork->path;
        return CertificateBuilder::createPdfClass($content, $path);
    }

    private static function certificateIntensive(CertificateWork $certificate, TrainingGroupParticipantWork $participant)
    {
        $genderVerbs = CertificateHelper::getGenderVerbs($participant->participantWork);

        $content = CertificateBuilder::createIntensiveCertificate($certificate, $participant, $genderVerbs);
        $path = Yii::$app->basePath . '/../' . $certificate->certificateTemplatesWork->path;
        return CertificateBuilder::createPdfClass($content, $path);
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

    public static function sendCertificateToEmail(CertificateWork $certificate): bool
    {
        $userId = Yii::$app->user->identity->getId();
        $downloadDir = Yii::$app->basePath . "/download/{$userId}_temp_certificates/";
        $fileName = self::downloadCertificate($certificate, $certificate->trainingGroupParticipantWork, self::DESTINATION_SERVER, $downloadDir);
        $filePath = "{$downloadDir}{$fileName}.pdf";

        $email = trim($certificate->trainingGroupParticipantWork->participant->email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return self::logAndFail($certificate, "Некорректный email адрес: {$email}");
        }

        $swiftMailer = Yii::$app->mailer->getSwiftMailer();
        $logger = new \Swift_Plugins_Loggers_ArrayLogger();
        $swiftMailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));

        try {
            $message = Yii::$app->mailer->compose()
                ->setFrom('noreply@schooltech.ru')
                ->setTo($email)
                ->setSubject('Сертификат об успешном прохождении программы ДО')
                ->setHtmlBody(
                    'Сертификат находится в прикрепленном файле.<br><br><br>' .
                    'Пожалуйста, обратите внимание, что это сообщение было сгенерировано и отправлено в автоматическом режиме. ' .
                    'Не отвечайте на него. По всем вопросам обращайтесь по телефону 44-24-28 (единый номер).'
                )
                ->attach($filePath);

            if (!$message->send()) {
                return self::logAndFail($certificate, "Не удалось отправить письмо на {$email}. SwiftMailer лог:\n" . $logger->dump());
            }

            $certificate->recordEvent(new CertificateSetStatusEvent($certificate->id, CertificateWork::STATUS_SEND), CertificateWork::class);

        } catch (\Swift_RfcComplianceException | \Exception $e) {
            return self::logAndFail($certificate, "Ошибка при отправке на {$email}: {$e->getMessage()}\nSwiftMailer лог:\n" . $logger->dump());
        }

        $certificate->releaseEvents();
        return true;
    }

    private static function logAndFail(CertificateWork $certificate, string $message): bool
    {
        Yii::error($message, __METHOD__);
        $certificate->recordEvent( new CertificateSetStatusEvent($certificate->id, CertificateWork::STATUS_ERR_SEND), CertificateWork::class);
        return false;
    }
}