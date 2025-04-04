<?php

namespace frontend\components\creators;

use common\helpers\DateFormatter;
use common\helpers\files\FilePaths;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\journal\VisitWork;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\LessonThemeWork;
use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\order\DocumentOrderWork;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Yii;
use yii\helpers\ArrayHelper;

class ExcelCreator
{
    public const TEMPLATE_FILENAME = 'electronicJournal.xlsx';
    public const LIMIT = 21;
    public const PARTICIPANT_LIMIT = 20;
    public const START_INDEX = "B";
    /*public static function createJournal(int $groupId) : Spreadsheet
    {
        $onPage = 21; //количество занятий на одной строке в листе
        $lesCount = 0; //счетчик для страниц

        $lessons = (Yii::createObject(TrainingGroupLessonRepository::class))->getLessonsFromGroup($groupId);
        $newLessons = array();
        foreach ($lessons as $lesson) {
            $newLessons[] = $lesson->id;
        }
        $visits = (Yii::createObject(VisitRepository::class));
        $visits = VisitWork::find()
            ->joinWith(['foreignEventParticipant foreignEventParticipant'])
            ->joinWith(['trainingGroupLesson trainingGroupLesson'])
            ->where(['in', 'training_group_lesson_id', $newLessons])
            ->orderBy(
                [
                    'foreignEventParticipant.secondname' => SORT_ASC,
                    'foreignEventParticipant.firstname' => SORT_ASC,
                    'trainingGroupLesson.lesson_date' => SORT_ASC,
                    'trainingGroupLesson.id' => SORT_ASC
                ]
            )->all();

        $newVisits = array();
        $newVisitsId = array();
        foreach ($visits as $visit) $newVisits[] = $visit->status;
        foreach ($visits as $visit) $newVisitsId[] = $visit->id;
        $model->visits = $newVisits;
        $model->visits_id = $newVisitsId;

        $group = TrainingGroupWork::find()->where(['id' => $training_group_id])->one();
        $parts = \app\models\work\TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $model->trainingGroup])->orderBy(['participant.secondname' => SORT_ASC])->all();
        $lessons = \app\models\work\TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC, 'id' => SORT_ASC])->all();

        $flag = 1; // флаг вида журнала, в зависимости от количества детей
        if (count($parts) > 20)
        {
            $fileName = '/templates/electronicJournal2.xlsx';
            $flag = 0;
        }
        else
            $fileName = '/templates/electronicJournal.xlsx';

        $inputType = IOFactory::identify(Yii::$app->basePath . $fileName);
        $reader = IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath . $fileName);

        for ($i = 1; $i < (count($lessons) / ($onPage * (1 + $flag))) * ceil(count($parts) / 46); $i++)
        {
            $clone = clone $inputData->getActiveSheet();
            $clone->setTitle('Шаблон' . $i);
            $inputData->addSheet($clone);
        }

        $magic = 0; //  смещение между страницами за счет фио+подписи и пустых строк
        $sheets = 0;
        while ($lesCount < count($lessons) / $onPage)
        {
            if ($lesCount !== 0 && $lesCount % 2 === 0)
            {
                $sheets++;
                $magic = 0;
            }
            if ($lesCount % 2 !== 0)
            {
                if ($flag == 1)
                    $magic = 26;
                else
                {
                    $sheets++;
                    $magic = 0;
                }
            }

            $inputData->getSheet($sheets)->setCellValueByColumnAndRow(1, 1 + $magic, 'Группа: ' . PHP_EOL . $group->number);
            $inputData->getSheet($sheets)->setCellValueByColumnAndRow(2, 1 + $magic, 'Программа: ' . $group->programNameNoLink);
            $inputData->getSheet($sheets)->getStyle('B'. $magic);

            $tempSheets = $sheets;
            for ($cp = 0; $cp < ceil(count($parts) / 46); $cp++)
            {
                for ($i = 0; $i + $lesCount * $onPage < count($lessons) && $i < $onPage; $i++) //цикл заполнения дат на странице
                {
                    $inputData->getSheet($tempSheets)->getCellByColumnAndRow(2 + $i, 4 + $magic)->setValueExplicit(date("d.m", strtotime($lessons[$i + $lesCount * $onPage]->lesson_date)), \PHPExcel_Cell_DataType::TYPE_STRING);
                    $inputData->getSheet($tempSheets)->getCellByColumnAndRow(2 + $i, 4 + $magic)->getStyle()->getAlignment()->setTextRotation(90);
                }
                $tempSheets++;
            }

            for($i = 0; $i < count($parts); ) //цикл заполнения детей на странице
            {
                if ($i !== 0 && $i % 46 === 0)
                {
                    $sheets++;
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(1, 1 + $magic, 'Группа: ' . $group->number);
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(2, 1 + $magic, 'Программа: ' . $group->programNameNoLink);
                    $inputData->getSheet($sheets)->getStyle('B'. $magic);
                    $inputData->getSheet($sheets)->getCellByColumnAndRow(2 + $i, 4 + $magic)->setValueExplicit(date("d.m", strtotime($lessons[$i + $lesCount * $onPage]->lesson_date)), \PHPExcel_Cell_DataType::TYPE_STRING);
                    $inputData->getSheet($sheets)->getCellByColumnAndRow(2 + $i, 4 + $magic)->getStyle()->getAlignment()->setTextRotation(90);
                }
                for ($j = 0; $j < 46 && $i < count($parts); $j++)
                {
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(1, $j + 6 + $magic, $parts[$i]->participantWork->shortName);
                    $i++;
                }
                //$inputData->getSheet($sheets)->setCellValueByColumnAndRow(1, $i + 6 + $magic, $parts[$i]->participantWork->shortName);
            }
            $lesCount++;
        }


        for ($cp = 0; $cp < count($parts); )
        {
            $sheets = 0;
            $delay = 0;

            if ($cp !== 0 && $cp % 46 === 0)
            {
                $sheets++;
            }

            for ($j = 0; $j < 46 && $cp < count($parts); $j++)
            {
                $magic = 0;
                $tempSheets = $sheets;
                for ($i = 0; $i < count($lessons); $i++, $delay++)
                {
                    $visits = \app\models\work\VisitWork::find()->where(['id' => $model->visits_id[$delay]])->one();

                    if ($i % $onPage === 0 && $i !== 0)
                    {
                        if (($magic === 26 && $flag === 1) || $flag === 0)
                        {
                            $magic = 0;
                            if (count($parts) > 46)
                                $tempSheets = $tempSheets + 2;
                            else
                                $tempSheets++;
                        }
                        else if ($flag === 1)
                            $magic = 26;
                    }
                    $inputData->getSheet($tempSheets)->setCellValueByColumnAndRow(2 + $i % $onPage, 6 + $j + $magic, $visits->excelStatus);
                }
                $cp++;
            }
        }

        for ($sheets = 0; $sheets < $inputData->getSheetCount(); $sheets++)
        {
            $inputData->getSheet($sheets)->setCellValueByColumnAndRow(32, 51, count($lessons)*count($parts));
        }

        $lessons = LessonThemeWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['trainingGroupLesson.training_group_id' => $training_group_id])
            ->orderBy(['trainingGroupLesson.lesson_date' => SORT_ASC, 'trainingGroupLesson.lesson_start_time' => SORT_ASC])->all();

        $sheets = 0;
        for ($i = 0; $i < ceil(count($parts) / 46); $i++)
        {
            $magic = 5;
            $tempSheets = $sheets;
            foreach ($lessons as $lesson)
            {
                $inputData->getSheet($tempSheets)->setCellValueByColumnAndRow(26, $magic, date("d.m.Y", strtotime($lesson->trainingGroupLesson->lesson_date)));
                $inputData->getSheet($tempSheets)->setCellValueByColumnAndRow(27, $magic, truncateString($lesson->theme));
                $magic++;

                if ($magic > 20 * (1 + $flag) + 5 + $flag)
                {
                    if (count($parts) > 46)
                        $tempSheets += 2;
                    else
                        $tempSheets++;
                    if ($tempSheets >= $inputData->getSheetCount())
                    {
                        break;
                    }
                    $magic = 5;
                }
            }
            $sheets++;
        }

        $themes = GroupProjectThemesWork::find()->where(['confirm' => 1])->andWhere(['training_group_id' => $training_group_id])->all();

        $strThemes = 'Тема проекта: ';
        foreach ($themes as $theme)
            $strThemes .= $theme->projectTheme->name.', ';

        $strThemes = substr($strThemes, 0, -2);

        $order1 = DocumentOrderWork::find()->joinWith(['orderGroups orderGroups'])->where(['orderGroups.training_group_id' => $training_group_id])->orderBy(['order_date' => SORT_ASC])->one();
        $order2 = DocumentOrderWork::find()->joinWith(['orderGroups orderGroups'])->where(['orderGroups.training_group_id' => $training_group_id])->andWhere(['study_type' => 0])->orderBy(['order_date' => SORT_ASC])->one();

        for ($sheets = 0; $sheets < $inputData->getSheetCount(); $sheets++)
        {
            if ($order1)
            {
                if ($order1->order_postfix == null)
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(26,51, $order1->order_number.'/'.$order1->order_copy_id);
                else
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(26, 51, $order1->order_number.'/'.$order1->order_copy_id.'/'.$order1->order_postfix);
            }

            if ($order2)
            {
                if ($order2->order_postfix == null)
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(30,51, $order2->order_number.'/'.$order2->order_copy_id);
                else
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(30, 51, $order2->order_number.'/'.$order2->order_copy_id.'/'.$order2->order_postfix);
            }

            if ($group->protection_date)
                $inputData->getSheet($sheets)->setCellValue('AB51', date("d.m.Y", strtotime($group->protection_date)));

            $inputData->getSheet($sheets)->setCellValue('Z1', $strThemes);
            $inputData->getSheet($sheets)->getStyle('Z1')->getAlignment()->setWrapText(true);
            $inputData->getSheet($sheets)->getStyle('B1')->getAlignment()->setWrapText(true);
        }

        return $inputData;
    }*/
    public static function findStatus($visitId, $lessonId){
        $visit = VisitWork::findOne($visitId);
        $lessons = json_decode(($visit->lessons));
        foreach ($lessons as $lesson) {
            if ($lesson->lesson_id == $lessonId) {
                return ExcelCreator::status($lesson->status);
            }
        }
        return ExcelCreator::status(VisitWork::NONE);
    }
    public static function status($status) {
        switch ($status) {
            case VisitWork::ATTENDANCE:
                return 'Я';
            case VisitWork::NO_ATTENDANCE:
                return 'Н';
            case VisitWork::DISTANCE:
                return 'Д';
            case VisitWork::NONE:
                return '-';
        }
        return '-';
    }
    public static function createJournal($groupId)
    {
        $fileName = FilePaths::TEMPLATE_FILEPATH . '/' . self::TEMPLATE_FILENAME;
        $inputType = IOFactory::identify(Yii::$app->basePath . '/' . $fileName);
        $reader = IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath . $fileName);
        $group = TrainingGroupWork::findOne($groupId);
        $defences = GroupProjectThemesWork::find()->where(['training_group_id' => $group->id])->all();
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $group->id])->orderBy('lesson_date ASC')->all();
        $participants = TrainingGroupParticipantWork::find()->where(['training_group_id' => $group->id])->all();
        $visits = VisitWork::find()->where(['IN','training_group_participant_id', ArrayHelper::getColumn($participants, 'id')])->all();
        $amountSheets = ExcelCreator::countList($lessons, $participants);
        ExcelCreator::createList($lessons, $group, $participants, $defences, $inputData, $amountSheets);
        $inputData = ExcelCreator::fillVisits($lessons, $visits, $inputData, $amountSheets);
        $inputData = ExcelCreator::fillThemes($inputData, $lessons, $amountSheets);
        return $inputData;
    }
    public static function fillVisits($lessons, $visits , $inputData, $amountSheets)
    {
        /* @var $lesson TrainingGroupLessonWork */
        /*  @var $visit VisitWork */
        $styleArray = array(
            'alignment' => array(
                'textRotation' => 90  // Поворот текста на 90 градусов
            ),
             'numberFormat' => array(
                'code' => NumberFormat::FORMAT_TEXT
            )
        );
        usort($visits, function($a, $b) {
            return strcmp(
                $a->trainingGroupParticipantWork->participantWork->getFullFio(),
                $b->trainingGroupParticipantWork->participantWork->getFullFio()
            );
        });
        $currentSheet = 0;
        $currentIndex = 6;
        foreach ($visits as $counter => $visit) {
            if ($counter != 0 && $counter % self::PARTICIPANT_LIMIT == 0){
                $currentSheet = $currentSheet + $amountSheets['lessonList'];
                $currentIndex = 6;
            }
            $localSheet = $currentSheet;
            $currentVisitIndex = 4;
            $visitIndex = self::START_INDEX;
            foreach ($lessons as $i => $lesson) {
                if ($i != 0 && $i % self::LIMIT == 0) {
                    $visitIndex = self::START_INDEX;
                    $currentVisitIndex = 30;
                }
                if ($i != 0 && $i % (self::LIMIT * 2) == 0) {
                    $visitIndex = self::START_INDEX;
                    $currentVisitIndex = 4;
                    $localSheet++;
                }
                $inputData->getSheet($currentSheet)->setCellValue("$visitIndex". $currentVisitIndex, DateFormatter::format($lesson->lesson_date, DateFormatter::Ymd_dash, DateFormatter::dm_dot));
                $inputData->getSheet($localSheet)->setCellValue("$visitIndex" . ($currentVisitIndex + ($counter % self::PARTICIPANT_LIMIT) + 2), ExcelCreator::findStatus($visit->id, $lesson->id));
                $visitIndex++;
            }
            for($sheet = 0; $sheet < $amountSheets['lessonList']; $sheet++){
                $visitIndex = self::START_INDEX;
                $iterator = 0;
                foreach ($lessons as $lesson) {
                    if ($sheet * (self::LIMIT * 2) <= $iterator && $iterator < ($sheet + 1) * (self::LIMIT * 2)){
                        if ($iterator % self::LIMIT == 0 && $iterator != $sheet * (self::LIMIT * 2)){
                             $visitIndex = self::START_INDEX;
                        }
                        if ($iterator + self::LIMIT < ($sheet + 1) * (self::LIMIT * 2)){
                            $inputData->getSheet($currentSheet + $sheet)->getStyle("$visitIndex". 4)->applyFromArray($styleArray);
                            $inputData->getActiveSheet()
                                ->getCell("$visitIndex". 4)
                                ->setValueExplicit(
                                    DateFormatter::format($lesson->lesson_date, DateFormatter::Ymd_dash, DateFormatter::dm_dot),
                                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2
                                );

                            $visitIndex++;
                        }
                        else {
                            $inputData->getSheet($currentSheet + $sheet)->getStyle("$visitIndex". 30)->applyFromArray($styleArray);
                            $inputData->getActiveSheet()
                                ->getCell("$visitIndex". 30)
                                ->setValueExplicit(
                                    DateFormatter::format($lesson->lesson_date, DateFormatter::Ymd_dash, DateFormatter::dm_dot),
                                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2
                                );
                            $visitIndex++;
                        }
                    }
                    $iterator++;
                }
                $inputData->getSheet($currentSheet + $sheet)->setCellValue("A". ($currentIndex), $visit->trainingGroupParticipantWork->participantWork->getFIO(ForeignEventParticipantsWork::FIO_SURNAME_INITIALS));
                $inputData->getSheet($currentSheet + $sheet)->setCellValue("A". ($currentIndex + 26), $visit->trainingGroupParticipantWork->participantWork->getFIO(ForeignEventParticipantsWork::FIO_SURNAME_INITIALS));
            }
            $currentIndex++;
        }
        return $inputData;
    }
    public static function fillThemes(
        $inputData,
        $lessons,
        $amountSheets
    )
    {
        /* @var $defence GroupProjectThemesWork */
        $currentSheet = 0;
        for($counter = 0; $counter < $amountSheets['participantList']; $counter++) {
            foreach ($lessons as $i => $lesson) {
                if ($i != 0 && $i % (self::LIMIT * 2) == 0) {
                    $currentSheet++;
                }
                $address = ($i % (self::LIMIT * 2)) + 5;
                $lessonTheme = LessonThemeWork::find()->where(['training_group_lesson_id' => $lesson->id])->one();
                $inputData->getSheet($currentSheet)->setCellValue("Z$address", DateFormatter::format($lessonTheme->trainingGroupLessonWork->lesson_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot));
                $inputData->getSheet($currentSheet)->setCellValue("AA$address", $lessonTheme->thematicPlanWork->theme);
            }
            $currentSheet++;
        }
        return $inputData;
    }
    public static function createList($lessons, $group, $participants, $defences, $inputData, $amountLists)
    {
        /* @var $group TrainingGroupWork */
        /* @var $defence GroupProjectThemesWork */

        $orderEnroll = [];
        $orderDeduct = [];
        foreach ($participants as $participant) {
            $orderEnrollIds = array_unique(ArrayHelper::getColumn(
                OrderTrainingGroupParticipantWork::find()
                    ->where(['training_group_participant_out_id' => NULL])
                    ->andWhere(['training_group_participant_in_id' => $participant->id])
                    ->all(),
                'order_id'
            ));
            $orderDeductIds = array_unique(ArrayHelper::getColumn(
                OrderTrainingGroupParticipantWork::find()
                    ->where(['training_group_participant_out_id' => $participant->id])
                    ->andWhere(['training_group_participant_in_id' => NULL])
                    ->all(),
                'order_id'
            ));
            $orderEnroll[] = array_unique(ArrayHelper::getColumn(DocumentOrderWork::find()->where(['IN', 'id' , $orderEnrollIds])->all(), 'id'));
            $orderDeduct[] = array_unique(ArrayHelper::getColumn(DocumentOrderWork::find()->where(['IN', 'id' , $orderDeductIds])->all(), 'id'));
        }

        $enroll = '';
        $deduct = '';
        $orderEnroll = array_unique($orderEnroll);
        $orderDeduct = array_unique($orderDeduct);
        foreach ($orderEnroll as $orders) {
            foreach ($orders as $order) {
                $enroll = $enroll . DocumentOrderWork::find()->where(['id' => $order])->one()['order_number'] . '/' .DocumentOrderWork::find()->where(['id' => $order])->one()['order_copy_id'] . ' ';
            }
        }
        foreach ($orderDeduct as $orders) {
            foreach ($orders as $order) {
                $deduct = $deduct . DocumentOrderWork::find()->where(['id' => $order])->one()['order_number'] . '/' .DocumentOrderWork::find()->where(['id' => $order])->one()['order_copy_id'] . ' ';
            }
        }
        for ($i = 0; $i < $amountLists['common']; $i++){
            $clone = clone $inputData->getActiveSheet();
            $clone->setTitle('Шаблон ' . ($i + 2));
            $inputData->addSheet($clone);
        }
        for($i = 0; $i <= $amountLists['common']; $i++){
            $defenceName = [];
            $defenceDate = [];
            foreach ($defences as $defence) {
                $defenceName[] = $defence->projectThemeWork->name;
                $defenceDate[] = NULL;
            }
            $defenceDate = implode(' , ', array_unique($defenceDate));
            $defenceName = implode(' , ', array_unique($defenceName));
            $inputData->getSheet($i)->setCellValue('A1', 'Группа: ' . $group->number);
            $inputData->getSheet($i)->setCellValue('B1', 'Программа: ' . $group->trainingProgramWork->name);
            $inputData->getSheet($i)->setCellValue('Z1', 'Тема проектов: ' . $defenceName);
            $inputData->getSheet($i)->setCellValue('Z51',  $enroll);
            $inputData->getSheet($i)->setCellValue('AB51',  $defenceDate);
            $inputData->getSheet($i)->setCellValue('AD51', $deduct);
            $inputData->getSheet($i)->setCellValue('AF51', count($lessons) * count($participants));
        }
    }
    public static function countList($lessons, $participants){
        return [
            'lessonList' => intdiv(count($lessons), self::LIMIT * 2) + 1,
            'participantList' => intdiv(count($participants), self::PARTICIPANT_LIMIT) + 1,
            'common' => max((intdiv(count($lessons), self::LIMIT * 2) + 1) * (intdiv(count($participants), self::PARTICIPANT_LIMIT) + 1) - 1, intdiv(count($lessons), self::LIMIT * 2) , intdiv(count($participants), self::PARTICIPANT_LIMIT))
        ];
    }
}