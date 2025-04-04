<?php

namespace common\helpers\html;

use common\helpers\common\BaseFunctions;
use common\helpers\files\FilePaths;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\CertificateWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use kartik\mpdf\Pdf;
use Yii;

class CertificateBuilder
{
    const VOIKOV_SEAL_PATH = '/templates/seal.png';
    const KISELEV_SEAL_PATH = '/templates/seal2.png';
    const KISELEV_SEAL_START_DATE = '2022-12-07';
    const KISELEV_SEAL_END_DATE = '2022-12-23';

    public static function createStandardCertificate(CertificateWork $certificate, TrainingGroupParticipantWork $participant, int $textSize, string $text)
    {
        $date = $participant->trainingGroupWork->protection_date;
        $content = '<body style="
                                 background: url('. Yii::$app->basePath . FilePaths::CERTIFICATE_TEMPLATES . $certificate->certificateTemplatesWork->path . ') no-repeat ;
                                 background-size: 10%;">
            <div>
            <table>
                <tr>
                    <td style="width: 780px; height: 130px; font-size: 19px; vertical-align: top;">
                        Министерство образования и науки Астраханской области<br>
                        государственное автономное образовательное учреждение Астраханской области<br>
                        дополнительного образования "Региональный школьный технопарк"<br>
                        отдел "'. $participant->trainingGroupWork->getBranchString() .'" ГАОУ АО ДО "РШТ"<br>
                    </td>
                </tr>
                <tr>
                    <td style="width: 700px; font-size: 19px; color: #626262;">
                        '. date("d", strtotime($date)) . ' '
            . BaseFunctions::monthFromNumbToString(date("m", strtotime($date))) . ' '
            . date("Y", strtotime($date)) . ' года
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 56px; height: 110px; vertical-align: bottom; color: #427fa2; text-align: left">
                        СЕРТИФИКАТ
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 15px; font-style: italic; height: 50px; vertical-align: bottom;">
                        удостоверяет, что
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 28px; text-decoration: none; color: black; font-weight: bold;">
                        '. $participant->participantWork->getFIO(PersonInterface::FIO_FULL) .'
                    </td>
                </tr>
                <tr>
                    <td style="line-height: 3ex; font-size: '.$textSize.'px; text-align: justify; text-justify: inter-word; height: 160px; vertical-align: bottom;">
                            '. $text .'
                    </td>
                </tr>
                </table><table>
                <tr>
                    <td style="width: 850px; font-size: 20px; vertical-align: bottom">
                        Рег. номер '.$certificate->getCertificateLongNumber().'
                    </td>
                    <td style="width: 180px; font-size: 18px; vertical-align: bottom">';

        if ($date >= self::KISELEV_SEAL_START_DATE && $date <= self::KISELEV_SEAL_END_DATE) {
            $content .= '
                        Е.В. Киселев <br>
                        и.о. директора <br>
                        ГАОУ АО ДО "РШТ" <br>
                        г. Астрахань - ' . date("Y", strtotime($date)) . '
                    </td>
                    <td style="">
                       <img width="332" height="202" src="'. Yii::$app->basePath . self::KISELEV_SEAL_PATH . '">';
        }

        else {
            $content .= '
                        В.В. Войков <br>
                        директор <br>
                        ГАОУ АО ДО "РШТ" <br>
                        г. Астрахань - ' . date("Y", strtotime($date)) . '
                    </td>
                    <td style="">
                       <img width="282" height="202" src="'. Yii::$app->basePath . self::VOIKOV_SEAL_PATH . '">';
        }

        $content .= '
                    </td>
                </tr>
            </table>
            </div>
            </body>';

        return $content;
    }

    public static function createSchoolCertificate(CertificateWork $certificate, TrainingGroupParticipantWork $participant, array $genderVerbs)
    {
        $date = $participant->trainingGroupWork->protection_date;
        $style = 'padding-left: -15px; margin: 10px;';
        $styleDistance = 'height: 1px; margin: 10px;';
        $content = self::createCertificateHeader($certificate, $participant, $date, $style, $styleDistance);
        $content .= '
            <p style="'.$styleDistance.'"></p><p style="height: 20px;"></p>
            <p style="font-size: 18px; '.$style.'">'. date("d", strtotime($date)) . ' '
            . BaseFunctions::monthFromNumbToString(date("m", strtotime($date))) . ' '
            . date("Y", strtotime($date)) . ' года
            </p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 24px; font-weight: bold;'.$style.'">'. $participant->participantWork->getFIO(PersonInterface::FIO_FULL) .'</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 16px;'.$style.'">'.$genderVerbs[0].' очное обучение по программе мероприятия</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 24px;'.$style.'">ЛЕТНЯЯ ШКОЛА</p>
            <p style="font-size: 20px;'.$style.'">"'.$participant->trainingGroupWork->trainingProgramWork->name.'"</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 16px;'.$style.'">в объеме '.$participant->trainingGroupWork->trainingProgram->capacity .' академических часов</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 16px;'.$style.'">и '.$genderVerbs[4].' участие в итоговом конкурсе по решению криптографических задач.</span></p>
            <p style="height: 70px;"></p>
            <p style="width: 600px; border-bottom: 1px solid black; margin: 0; padding-left: -40px; font-size: 2px;"></p>
            <p style="font-size: 14px; '.$style.'">В.В. Войков <br>
                        Директор <br>
                        ГАОУ АО ДО "РШТ"</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 14px; color: #585858;'.$style.'">Рег. номер '.$certificate->getCertificateLongNumber().'</p>
            </div>
            </body>';

        return $content;
    }

    public static function createTechnosummerCertificate(CertificateWork $certificate, TrainingGroupParticipantWork $participant)
    {
        return '<body style="font-family: sans-serif; 
                                 background: url('. Yii::$app->basePath . FilePaths::CERTIFICATE_TEMPLATES . $certificate->certificateTemplatesWork->path . ') no-repeat ;">
            <div>
                <p style="height: 160px;"></p>
                <p style="font-size: 28px; text-decoration: none; color: #164192; font-weight: bold; padding-left: -5px;">'.
            $participant->participantWork->getFIO(PersonInterface::FIO_FULL) .
            '</p>
            </div>
            <div>
                <p style="height: 293px;"></p>
                <p style="padding-left: 120px; font-size: 20px; vertical-align: bottom; color: #164192; ">'.$certificate->getCertificateLongNumber().'</p>
            </div>
            </body>';
    }

    public static function createIntensiveCertificate(CertificateWork $certificate, TrainingGroupParticipantWork $participant, array $genderVerbs)
    {
        $date = $participant->trainingGroupWork->protection_date;
        $prof = '';
        $type = strripos($certificate->certificateTemplatesWork->name, CertificateWork::PLUS) ? 'ИНТЕНСИВ+' : 'ИНТЕНСИВ';
        if (strripos($certificate->certificateTemplatesWork->name, CertificateWork::PRO)) {
            $type = 'РШТ.ПРО';
            $prof = ' профориентационной';
        }

        $style = 'padding-left: -15px; margin: 10px;';
        $styleDistance = 'height: 1px; margin: 10px;';
        $content = self::createCertificateHeader($certificate, $participant, $date, $style, $styleDistance);
        $content .= '
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 18px; '.$style.'">'. date("d", strtotime($date)) . ' '
            . BaseFunctions::monthFromNumbToString(date("m", strtotime($date))) . ' '
            . date("Y", strtotime($date)) . ' года
            </p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 24px; font-weight: bold;'.$style.'">'. $participant->participantWork->getFIO(PersonInterface::FIO_FULL) .'</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 16px;'.$style.'">'.$genderVerbs[0].' очное обучение по'. $prof .' программе мероприятия</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 24px;'.$style.'">'.$type.'</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 16px;'.$style.'">в объеме '.$participant->trainingGroupWork->trainingProgram->capacity .' академических часов, '.$genderVerbs[1].' проект</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 20px; width: 800px;'.$style.'">"'.$participant->groupProjectThemes->projectTheme->name.'"</p>
            <p style="font-size: 16px;'.$style.'">и '.$genderVerbs[2].' с итоговой презентацией на научной конференции</p>
            <p style="height: 70px;"></p>
            <p style="width: 600px; border-bottom: 1px solid black; margin: 0; padding-left: -40px; font-size: 2px;"></p>
            <p style="font-size: 14px; '.$style.'">В.В. Войков <br>
                        Директор <br>
                        ГАОУ АО ДО "РШТ"</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 14px; color: #585858;'.$style.'">Рег. номер '.$certificate->getCertificateLongNumber().'</p>
            </div>
            </body>';

        return $content;
    }

    private static function createCertificateHeader(
        CertificateWork $certificate,
        TrainingGroupParticipantWork $participant,
        string $date,
        string $style,
        string $styleDistance
    )
    {
        $content = '<body style="font-family: sans-serif; background: url('.
            Yii::$app->basePath . FilePaths::CERTIFICATE_TEMPLATES . $certificate->certificateTemplatesWork->path .
            ') no-repeat ;">
            <div>';
        if ($date >= "2023-07-21") {
            $content .= '<p style="'.$styleDistance.'"></p>
                         <p style="font-size: 16px;'.$style.' padding-top: -20px;">
                            Министерство образования и науки Астраханской области<br>
                            государственное автономное образовательное учреждение Астраханской области<br>
                            дополнительного образования "Региональный школьный технопарк"<br>
                            отдел "'. $participant->trainingGroupWork->getBranchString() .'" ГАОУ АО ДО "РШТ"<br></p>';
        }
        else {
            $content .= '<p style="'.$style.' padding-top: -10px;"><img width="535" height="110" src="'.Yii::$app->basePath . FilePaths::CERTIFICATE_TEMPLATES .'seal.png"></p>';
        }

        return $content;
    }

    public static function createPdfClass(string $content)
    {
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'destination' => Pdf::DEST_BROWSER,
            'options' => [],
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'methods' => [
                'SetTitle' => 'Privacy Policy - Krajee.com',
                'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetFooter' => ['|Page {PAGENO}|'],
                'SetAuthor' => 'ЦСХД (с) РШТ',
                'SetCreator' => 'ЦСХД (с) РШТ',
                'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->getApi();
        $mpdf->WriteHtml($content);
        $mpdf->setProtection(array('print', 'print-highres'));

        return $mpdf;
    }
}