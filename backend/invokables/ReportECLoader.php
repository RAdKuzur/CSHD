<?php

namespace backend\invokables;

use common\components\dictionaries\base\EventLevelDictionary;
use common\helpers\common\HeaderWizard;
use common\helpers\DateFormatter;
use common\helpers\files\FilePaths;
use frontend\models\work\event\ParticipantAchievementWork;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;

class ReportECLoader
{
    private string $templatePath;
    private string $filename;
    private array $data;
    private string $endDate; // новое свойство

    public function __construct(
        $templatePath,
        $filename,
        $data,
        string $endDate // добавляем параметр
    )
    {
        $this->templatePath = $templatePath;
        $this->filename = $filename;
        $this->data = $data;
        $this->endDate = $endDate;
    }

    public function __invoke()
    {
        $inputData = IOFactory::load(Yii::$app->basePath . FilePaths::REPORT_TEMPLATES . $this->templatePath);
        $this->setDodSection($inputData, $this->data['sectionDod']);
        $this->setParticipantSection($inputData, $this->data['sectionParticipant']);

        HeaderWizard::setExcelLoadHeaders($this->filename);
        $writer = new Xlsx($inputData);
        $writer->save('php://output');
        exit;
    }

    public function setDodSection(Spreadsheet $inputData, array $data)
    {
        // заменяем date('Y-m-d') на $this->endDate
        $inputData->getSheet(1)->setCellValue(
            'D4',
            'на ' . DateFormatter::format($this->endDate, DateFormatter::Ymd_dash, DateFormatter::dmY_dot) . ' г.'
        );

        $inputData->getSheet(1)->setCellValue('D5', $data['totalCount']);
        $inputData->getSheet(1)->setCellValue('D6', $data['result'][EventLevelDictionary::INTERNATIONAL]['prizes']);
        $inputData->getSheet(1)->setCellValue('D7', $data['result'][EventLevelDictionary::INTERNATIONAL]['winners']);
        $inputData->getSheet(1)->setCellValue('D8', $data['result'][EventLevelDictionary::FEDERAL]['prizes']);
        $inputData->getSheet(1)->setCellValue('D9', $data['result'][EventLevelDictionary::FEDERAL]['winners']);
        $inputData->getSheet(1)->setCellValue('D10', $data['result'][EventLevelDictionary::REGIONAL]['prizes']);
        $inputData->getSheet(1)->setCellValue('D11', $data['result'][EventLevelDictionary::REGIONAL]['winners']);
    }

    public function convertEvent(int $level): ?string
    {
        switch ($level) {
            case EventLevelDictionary::INTERIOR:
                return 'Внутренний';
            case EventLevelDictionary::DISTRICT:
                return 'Районный';
            case EventLevelDictionary::URBAN:
                return 'Городской';
            case EventLevelDictionary::REGIONAL:
                return 'Региональный';
            case EventLevelDictionary::FEDERAL:
                return 'Всероссийский';
            case EventLevelDictionary::INTERREGIONAL:
                return 'Всероссийский';
            case EventLevelDictionary::INTERNATIONAL:
                return 'Международный';
        }
        return null;
    }


    public function setParticipantSection(Spreadsheet $inputData, array $data)
    {
        $counter = 0;
        $data = $data['result']['levels'];
        foreach ($data as $participants) {
            foreach ($participants['participantsWinner'] as $participant) {
                foreach ($participant->squadParticipantWork as $person) {
                    $inputData->getSheet(2)->setCellValue('B' . (5 + $counter), $person->participantWork->surname);
                    $inputData->getSheet(2)->setCellValue('C' . (5 + $counter), $this->convertEvent($person->actParticipantWork->foreignEventWork->level));
                    $inputData->getSheet(2)->setCellValue('D' . (5 + $counter), $person->actParticipantWork->foreignEventWork->name);
                    $inputData->getSheet(2)->setCellValue('E' . (5 + $counter), $person->actParticipantWork->nomination);
                    $inputData->getSheet(2)->setCellValue('F' . (5 + $counter), $person->actParticipantWork->getTypeParticipantEC());
                    $inputData->getSheet(2)->setCellValue('G' . (5 + $counter), $person->actParticipantWork->participantAchievementWork[0]->getPrettyType());
                    $inputData->getSheet(2)->setCellValue('H' . (5 + $counter), $person->actParticipantWork->participantAchievementWork[0]->achievement);
                    $inputData->getSheet(2)->setCellValue('I' . (5 + $counter), '');
                    $inputData->getSheet(2)->setCellValue('J' . (5 + $counter), $person->actParticipantWork->participantAchievementWork[0]->cert_number);
                    $inputData->getSheet(2)->setCellValue('K' . (5 + $counter), $person->actParticipantWork->foreignEventWork->end_date);
                    $counter++;
                }
            }
            foreach ($participants['participantsPrize'] as $participant) {
                foreach ($participant->squadParticipantWork as $person) {
                    $inputData->getSheet(2)->setCellValue('B' . (5 + $counter), $person->participantWork->surname);
                    $inputData->getSheet(2)->setCellValue('C' . (5 + $counter), $this->convertEvent($person->actParticipantWork->foreignEventWork->level));
                    $inputData->getSheet(2)->setCellValue('D' . (5 + $counter), $person->actParticipantWork->foreignEventWork->name);
                    $inputData->getSheet(2)->setCellValue('E' . (5 + $counter), $person->actParticipantWork->nomination);
                    $inputData->getSheet(2)->setCellValue('F' . (5 + $counter), $person->actParticipantWork->getTypeParticipantEC());
                    $inputData->getSheet(2)->setCellValue('G' . (5 + $counter), $person->actParticipantWork->participantAchievementWork[0]->getPrettyType());
                    $inputData->getSheet(2)->setCellValue('H' . (5 + $counter), $person->actParticipantWork->participantAchievementWork[0]->achievement);
                    $inputData->getSheet(2)->setCellValue('I' . (5 + $counter), '');
                    $inputData->getSheet(2)->setCellValue('J' . (5 + $counter), $person->actParticipantWork->participantAchievementWork[0]->cert_number);
                    $inputData->getSheet(2)->setCellValue('K' . (5 + $counter), $person->actParticipantWork->foreignEventWork->end_date);
                    $counter++;
                }
            }
        }
    }
}