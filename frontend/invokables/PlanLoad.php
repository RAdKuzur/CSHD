<?php


namespace frontend\invokables;


use common\helpers\common\HeaderWizard;
use common\helpers\DateFormatter;
use common\helpers\files\FilePaths;
use frontend\models\work\educational\training_group\LessonThemeWork;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;

class PlanLoad
{
    const TEMPLATE_FILENAME = 'template_KUG.xlsx';

    /** @var LessonThemeWork[] $lessonThemes */
    private array $lessonThemes;
    private string $groupNumber;
    private string $branch;

    public function __construct(
        array $lessonThemes,
        string $groupNumber,
        string $branch
    )
    {
        $this->lessonThemes = $lessonThemes;
        $this->groupNumber = $groupNumber;
        $this->branch = $branch;
    }

    public function __invoke()
    {
        $inputData = IOFactory::load(Yii::$app->basePath . FilePaths::TEMPLATE_FILEPATH . '/' . self::TEMPLATE_FILENAME);

        $this->fill($inputData);
        $this->setStyles($inputData);

        HeaderWizard::setExcelLoadHeaders("КУГ_$this->groupNumber.xlsx");
        $writer = new Xlsx($inputData);
        $writer->save('php://output');
        exit;
    }

    public function fill(Spreadsheet $inputData)
    {
        $c = 1;

        $inputData->getActiveSheet()->setCellValue('C8', $this->groupNumber);
        $inputData->getActiveSheet()->setCellValue('C9', 'Отдел "' . Yii::$app->branches->get($this->branch). '" ГАОУ АО ДО "РШТ"');

        foreach ($this->lessonThemes as $lessonTheme) {
            $inputData->getActiveSheet()->setCellValue('A' . (11 + $c), $c);
            $lessonDate = DateFormatter::format($lessonTheme->trainingGroupLessonWork->lesson_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
            $inputData->getActiveSheet()->setCellValue('B' . (11 + $c), $lessonDate);
            $inputData->getActiveSheet()->setCellValue(
                'C' . (11 + $c),
                mb_substr($lessonTheme->trainingGroupLessonWork->lesson_start_time, 0, -3) . ' - ' . mb_substr($lessonTheme->trainingGroupLessonWork->lesson_end_time, 0, -3)
            );
            $inputData->getActiveSheet()->setCellValue('D' . (11 + $c), $lessonTheme->thematicPlanWork->theme);
            $inputData->getActiveSheet()->getRowDimension(11 + $c)->setRowHeight(12.5 * (strlen($lessonTheme->thematicPlanWork->theme) / 60) + 15);
            $inputData->getActiveSheet()->setCellValue('E' . (11 + $c), $lessonTheme->trainingGroupLesson->duration);
            $inputData->getActiveSheet()->setCellValue('F' . (11 + $c), "Групповая");
            $inputData->getActiveSheet()->setCellValue('G' . (11 + $c), Yii::$app->controlType->get($lessonTheme->thematicPlanWork->control_type));
            $c++;
        }
    }

    private function setStyles(Spreadsheet $inputData)
    {
        for ($i = 11; $i < 11 + count($this->lessonThemes); $i++) {
            $inputData->getActiveSheet()->getStyle('A'.$i.':B'.($i+1))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
            $inputData->getActiveSheet()->getStyle('B'.$i.':C'.($i+1))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
            $inputData->getActiveSheet()->getStyle('C'.$i.':D'.($i+1))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
            $inputData->getActiveSheet()->getStyle('D'.$i.':E'.($i+1))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
            $inputData->getActiveSheet()->getStyle('E'.$i.':F'.($i+1))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
            $inputData->getActiveSheet()->getStyle('F'.$i.':G'.($i+1))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
        }

        $inputData
            ->getActiveSheet()
            ->getStyle('A12:G'. (11 + count($this->lessonThemes)))
            ->getBorders()
            ->getOutline()
            ->setBorderStyle(Border::BORDER_THIN);
    }
}