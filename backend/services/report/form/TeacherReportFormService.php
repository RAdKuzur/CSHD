<?php

namespace backend\services\report\form;

use backend\builders\TrainingGroupReportBuilder;
use backend\forms\report\ManHoursReportForm;
use backend\forms\report\TeacherReportForm;
use common\helpers\common\HeaderWizard;
use common\helpers\files\FilePaths;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\helpers\ArrayHelper;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;


class TeacherReportFormService
{
    const CALCULATE_TYPES = [
        ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN,
        ManHoursReportForm::PARTICIPANT_START_IN_FINISH_AFTER,
        ManHoursReportForm::PARTICIPANT_START_IN_FINISH_IN,
        ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_AFTER
    ];

    const MONTH = [
        1 => 'Январь',
        2 => 'Февраль',
        3 => 'Март',
        4 => 'Апрель',
        5 => 'Май',
        6 => 'Июнь',
        7 => 'Июль',
        8 => 'Август',
        9 => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь'
    ];
    public TrainingGroupReportBuilder $trainingGroupReportBuilder;
    public function __construct(
        TrainingGroupReportBuilder $trainingGroupReportBuilder
    )
    {
        $this->trainingGroupReportBuilder = $trainingGroupReportBuilder;
    }

    public function prepareTeacherReportForm(TeacherReportForm $model){
        /**
         * @var TeacherGroupWork $teacherGroup
         */
        //данные об уроках по месяцам

        $dataLessons = [];
        $dataHours = [];
        $data = [];
        //фильтрация по дате и отделу
        $groupsQuery = $this->trainingGroupReportBuilder->query();
        $groupsQuery = $this->trainingGroupReportBuilder->filterGroupsByDates($groupsQuery, $model->getYear().'-01-01', ($model->getYear() + 1).'-01-01', self::CALCULATE_TYPES);
        $groupsQuery = $this->trainingGroupReportBuilder->filterGroupsByBranches($groupsQuery, [$model->getBranch()]);
        $groupIds = ArrayHelper::getColumn($groupsQuery->all(), 'id');

        //teacher_groups

        $teacherGroupQuery = $this->trainingGroupReportBuilder->teacherGroupQuery();
        $teacherGroupQuery = $this->trainingGroupReportBuilder->filterTrainingGroupIDs($teacherGroupQuery, $groupIds);
        $teacherGroupQuery = $this->trainingGroupReportBuilder->orderByTeacherGroup($teacherGroupQuery);

        $teacherGroups = $teacherGroupQuery->all();

        foreach ($teacherGroups as $teacherGroup) {

            $fioPosition = $teacherGroup->teacherWork->getFioPosition();

            //добавление помесячных данных о занятиях
            foreach ($teacherGroup->trainingGroup->trainingGroupLessons as $lesson) {
                $monthOfLesson = (new \DateTime($lesson->lesson_date))->format('n');
                $yearOfLesson = (new \DateTime($lesson->lesson_date))->format('Y');
                if ($yearOfLesson === $model->getYear()) {
                    $dataLessons[$lesson->id] = $monthOfLesson;
                }
            }
            $totalGroupLesson = [];
            //сбор данных о часах в группах (помесячно)
            foreach ($teacherGroup->trainingGroup->trainingGroupParticipants as $participant) {
                $lessons = json_decode($participant->visit->lessons); // это ячейка lessons в таблице  visits
                foreach ($lessons as $lesson) {
                    $month = $dataLessons[$lesson->lesson_id];
                    $totalGroupLesson[$month]++;
                    if ($lesson->status !== -1) { // в зависимости от ТЗ условие может меняться
                        // $dataHours[$teacherGroup->trainingGroup->id][$month]['hours']++;
                        $dataHours[$teacherGroup->trainingGroup->id][$month]['count'] = count($teacherGroup->trainingGroup->trainingGroupParticipants);
                    }
                }
            }

            foreach (self::MONTH as $index => $month) {
                $dataHours[$teacherGroup->trainingGroup->id][$index]['hours'] = $totalGroupLesson[$index] / count($teacherGroup->trainingGroup->trainingGroupParticipants);
            }

            //сборка даннных в итоговый массив
            $data[$fioPosition][] = [
                'teacher' => $fioPosition,
                'group' => [
                    'id' => $teacherGroup->trainingGroup->id,
                    'number' => $teacherGroup->trainingGroup->number,
                    'dataHours' => $dataHours[$teacherGroup->trainingGroup->id]
                ],

            ];
        }
        return $data;
    }

    public function createExcelVariantReport(TeacherReportForm $model, $data)
    {
        $inputData = IOFactory::load(Yii::$app->basePath . FilePaths::REPORT_TEMPLATES . 'report_teacher_hours.xlsx');

        $startIndex = 2; // начинаем со второй
        foreach ($data as $index => $item) {
            $teacherTotalHours = 0;
            $dataMonth = [];

            //ФИО педагога
            $mergedRange = 'A'.$startIndex . ':' . 'Z'.$startIndex;
            $inputData->getActiveSheet()->mergeCells($mergedRange);
            $inputData->getActiveSheet()->setCellValue('A'. $startIndex, $index);
            $inputData->getActiveSheet()->getStyle($mergedRange)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            $startIndex++;
            $startIndex = $this->prepareTeacherHeader($inputData, $startIndex ,$model->getYear()); //заголовок с датой и временем
            foreach ($item as $group) {
                $totalHours = 0;
                $inputData->getActiveSheet()->setCellValue(
                    'A'. $startIndex,
                    $group['group']['number']
                );

                //пошли человеко-часы
                $column = 'B';
                foreach (self::MONTH as $indexMonth => $month) {
                    $inputData->getActiveSheet()->setCellValue(
                        $column . $startIndex,
                        $group['group']['dataHours'][$indexMonth]['hours'] ?? 0
                    );
                    $column++;

                    $inputData->getActiveSheet()->setCellValue(
                        $column . $startIndex,
                        $group['group']['dataHours'][$indexMonth]['count'] ?? 0
                    );
                    $column++;

                    //$totalHours += $group['group']['dataHours'][$indexMonth]['hours'];

                    $totalHours += $group['group']['dataHours'][$indexMonth]['hours'] * $group['group']['dataHours'][$indexMonth]['count'];
                    $dataMonth[$indexMonth]['hours'] += $group['group']['dataHours'][$indexMonth]['hours'];
                    $dataMonth[$indexMonth]['count'] += $group['group']['dataHours'][$indexMonth]['count'];
                }

                $inputData->getActiveSheet()->setCellValue(
                    'Z' . $startIndex,
                    $totalHours
                );
                $teacherTotalHours += $totalHours;
                $startIndex++;
            }

            $this->prepareTotalTeacherFooter($inputData, $startIndex, $dataMonth, $teacherTotalHours);

            $startIndex++;
            $startIndex++;
        }
        HeaderWizard::setExcelLoadHeaders('Расчёт по выработки пед. работников.xlsx');
        $writer = new Xlsx($inputData);
        $writer->save('php://output');
    }

    public function prepareTeacherHeader($inputData, $index, $year)
    {
        $column = 'B';
        foreach (self::MONTH as $month) {
            $firstCell = $column . $index;
            $secondCell = ++$column . $index;
            $inputData->getActiveSheet()->mergeCells($firstCell . ':' . $secondCell);
            $inputData->getActiveSheet()->setCellValue($firstCell, $month . ' ' . $year);
            $inputData->getActiveSheet()->getStyle($firstCell)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
            $column++;
        }

        $inputData->getActiveSheet()->setCellValue('Z' . $index, 'ИТОГО');
        $inputData->getActiveSheet()->getStyle('Z' . $index)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $index++;
        $inputData->getActiveSheet()->setCellValue('A' . $index, 'Группа');
        $inputData->getActiveSheet()->getStyle('A' . $index)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $column = 'B';
        foreach (self::MONTH as $month) {
            $inputData->getActiveSheet()->setCellValue($column . $index, 'Кол-во ак. часов');
            $inputData->getActiveSheet()->getStyle($column . $index)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
            $column++;

            $inputData->getActiveSheet()->setCellValue($column . $index, 'Кол-во чел.');
            $inputData->getActiveSheet()->getStyle($column . $index)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
            $column++;
        }

        $inputData->getActiveSheet()->setCellValue('Z' . $index, 'ИТОГО');
        $inputData->getActiveSheet()->getStyle('Z' . $index)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        return $index + 1;
    }

    public function prepareTotalTeacherFooter($inputData, $startIndex, $dataMonth, $teacherTotalHours) {
        $inputData->getActiveSheet()->setCellValue(
            'A'. $startIndex,
            'ИТОГО'
        );
        $inputData->getActiveSheet()->getStyle('A' . $startIndex)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $column = 'B';
        foreach (self::MONTH as $indexMonth => $month) {
            $inputData->getActiveSheet()->setCellValue(
                $column . $startIndex,
                $dataMonth[$indexMonth]['hours']
            );
            $inputData->getActiveSheet()->getStyle($column . $startIndex)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $column++;

            $inputData->getActiveSheet()->setCellValue(
                $column . $startIndex,
                $dataMonth[$indexMonth]['count']
            );
            $inputData->getActiveSheet()->getStyle($column . $startIndex)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $column++;
        }

        $inputData->getActiveSheet()->setCellValue(
            'Z' . $startIndex,
            $teacherTotalHours
        );
        $inputData->getActiveSheet()->getStyle('Z' . $startIndex)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }
}