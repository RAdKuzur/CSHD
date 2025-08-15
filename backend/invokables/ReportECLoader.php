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
    public function __construct(
        $templatePath, $filename, $data
    )
    {
        $this->templatePath = $templatePath;
        $this->filename = $filename;
        $this->data = $data;
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
        //var_dump($data);
        $inputData->getSheet(1)->setCellValue('D4', 'на ' . DateFormatter::format(date('Y-m-d'), DateFormatter::Ymd_dash, DateFormatter::dmY_dot) . ' г.');
        $inputData->getSheet(1)->setCellValue('D5', $data['reportData']['totalCount']);
        $inputData->getSheet(1)->setCellValue('D6', $data['reportData'][EventLevelDictionary::INTERNATIONAL][ParticipantAchievementWork::TYPE_PRIZE]);
        $inputData->getSheet(1)->setCellValue('D7', $data['reportData'][EventLevelDictionary::INTERNATIONAL][ParticipantAchievementWork::TYPE_WINNER]);
        $inputData->getSheet(1)->setCellValue('D8', $data['reportData'][EventLevelDictionary::FEDERAL][ParticipantAchievementWork::TYPE_PRIZE]);
        $inputData->getSheet(1)->setCellValue('D9', $data['reportData'][EventLevelDictionary::FEDERAL][ParticipantAchievementWork::TYPE_WINNER]);
        $inputData->getSheet(1)->setCellValue('D10', $data['reportData'][EventLevelDictionary::REGIONAL][ParticipantAchievementWork::TYPE_PRIZE]);
        $inputData->getSheet(1)->setCellValue('D11', $data['reportData'][EventLevelDictionary::REGIONAL][ParticipantAchievementWork::TYPE_WINNER]);
    }
    public function setParticipantSection(Spreadsheet $inputData, array $data)
    {
        $counter = 0;
        foreach ($data['participants'] as $item) {
            $inputData->getSheet(2)->setCellValue('B' . (5 + $counter), $item->participantWork->getFullFio());
            $inputData->getSheet(2)->setCellValue('C' . (5 + $counter), Yii::$app->eventLevel->get($item->actParticipantWork->foreignEventWork->level));
            $inputData->getSheet(2)->setCellValue('D' . (5 + $counter), $item->actParticipantWork->foreignEventWork->name);
            $inputData->getSheet(2)->setCellValue('E' . (5 + $counter), $item->actParticipantWork->nomination);
            $inputData->getSheet(2)->setCellValue('F' . (5 + $counter), $item->actParticipantWork->getTypeParticipant());
            $inputData->getSheet(2)->setCellValue('G' . (5 + $counter), $item->actParticipantWork->participantAchievementWork[0]->getPrettyType());
            $inputData->getSheet(2)->setCellValue('H' . (5 + $counter), $item->actParticipantWork->participantAchievementWork[0]->achievement);
            $counter++;
        }
    }
}