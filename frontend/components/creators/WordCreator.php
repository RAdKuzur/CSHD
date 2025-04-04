<?php

namespace frontend\components\creators;
use app\models\work\order\OrderEventGenerateWork;
use common\components\dictionaries\base\BranchDictionary;
use common\components\wizards\WordWizard;
use common\helpers\common\BaseFunctions;
use common\models\scaffold\ActParticipantBranch;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\general\OrderPeopleWork;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\team\ActParticipantWork;
use frontend\models\work\team\SquadParticipantWork;
use PhpOffice\PhpWord\PhpWord;
use Yii;
use yii\helpers\ArrayHelper;

class WordCreator
{
    /**
     * @param TrainingGroupWork $modelGroup
     * @param TrainingGroupParticipantWork[] $groupParticipants
     * @param TrainingGroupExpertWork[] $experts
     * @param string $eventName
     * @return PhpWord
     */
    public static function createProtocol(TrainingGroupWork $modelGroup, array $groupParticipants, array $experts, string $eventName) : PhpWord
    {
        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));

        $section->addText('ПРОТОКОЛ ИТОГОВОЙ АТТЕСТАЦИИ', array('bold' => true), array('align' => 'center'));
        $section->addText('отдел «'. Yii::$app->branches->get($modelGroup->branch) .'» ГАОУ АО ДО «РШТ»', array('underline' => 'single'), array('align' => 'center'));
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(4000);
        $cell->addText('«' . date("d", strtotime($modelGroup->protection_date)) . '» '
            . BaseFunctions::monthFromNumbToString(date("m", strtotime($modelGroup->protection_date))) . ' '
            . date("Y", strtotime($modelGroup->protection_date)) . ' г.');
        $cell = $table->addCell(6000);
        $cell->addText('№ ' . $modelGroup->number, null, array('align' => 'right'));
        $section->addTextBreak(2);

        $section->addText('Демонстрация результатов образовательной деятельности', array('bold' => true), array('align' => 'center'));
        $section->addTextBreak(1);
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(5000);
        $cell->addText($modelGroup->trainingProgram->name, array('underline' => 'single'));
        $table->addCell(2000);
        $table->addRow();
        $cell = $table->addCell(5000);
        $cell->addText($modelGroup->number, array('underline' => 'single'));
        $table->addCell(2000);
        $section->addTextBreak(2);

        switch (Yii::$app->branches->get($modelGroup->branch)) {
            case BranchDictionary::QUANTORIUM:
                $boss = 'Цырульников Евгений Сергеевич';
                $bossShort = 'Цырульников Е.С.';
                $expertExept = 19;
                break;
            case BranchDictionary::TECHNOPARK:
                $boss = 'Толочина Оксана Георгиевна';
                $bossShort = 'Толочина О.Г.';
                $expertExept = 946;
                break;
            case BranchDictionary::CDNTT:
                $boss = 'Дубовская Лариса Валерьевна';
                $bossShort = 'Дубовская Л.В.';
                $expertExept = 21;
                break;
            case BranchDictionary::COD:
                $boss = 'Баганина Анна Александровна';
                $bossShort = 'Баганина А.А.';
                $expertExept = 36;
                break;
            default:
                $boss = 'Толочина Оксана Георгиевна';
                $bossShort = 'Толочина О.Г.';
                $expertExept = 946;
        }

        $section->addText('Присутствовали ответственные лица:', null, array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('          1. Руководитель учебной группы – ' . $modelGroup->teachersWork[0]->teacherWork->getFIO(PersonInterface::FIO_FULL) . '.', null, array('align' => 'both', 'spaceAfter' => 0));
        if (Yii::$app->branches->get($modelGroup->branch) === BranchDictionary::MOBILE_QUANTUM) {
            $section->addText('          2. Заместитель руководителя - заведующий по образовательной деятельности ' . $boss . '.', null, array('align' => 'both', 'spaceAfter' => 0));
        }
        else {
            $section->addText('          2. Руководитель отдела «'.Yii::$app->branches->get($modelGroup->branch).'» ' . $boss . '.', null, array('align' => 'both', 'spaceAfter' => 0));
        }

        $numberStr = 3;
        foreach ($experts as $expert) {
            if ($expert->expert_id !== $expertExept) {
                $section->addText('          '.$numberStr.'. ' . $expert->expertWork->positionWork->name . ' ' . $expert->expertWork->getFIO(PersonInterface::FIO_FULL) . '.',null, array('align' => 'both', 'spaceAfter' => 0));
                $numberStr++;
            }
        }
        $section->addTextBreak(1);
        $section->addText($eventName, array('underline' => 'single'), array('spaceAfter' => 0));
        $section->addText('(публичное мероприятие, на котором проводилась аттестация)', array('size' => 12, 'italic' => true), array('spaceAfter' => 0));
        $section->addTextBreak(1);

        $expertFlag = false;
        if ($modelGroup->expertsWork) {
            $numberStr = 1;
            foreach ($modelGroup->expertsWork as $expert) {
                if ($expert->expert_type == TrainingGroupExpertWork::TYPE_EXTERNAL && $expert->expert_id !== $expertExept) {
                    if ($numberStr === 1) {
                        $expertFlag = true;
                        $section->addText('Приглашенные эксперты:', array('underline' => 'single'), array('spaceAfter' => 0));
                    }
                    $section->addText('          '.$numberStr.'. ' . $expert->expertWork->companyWork->short_name . ' ' . $expert->expertWork->positionWork->name . ' ' . $expert->expertWork->getFIO(PersonInterface::FIO_FULL),null, array('align' => 'both', 'spaceAfter' => 0));
                    $numberStr++;
                }
            }
        }
        $section->addTextBreak(1);

        $section->addText('Повестка дня:', null, array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('          1. Принятие решения о результатах итоговой аттестации.', null, array('align' => 'both', 'spaceAfter' => 0));
        $section->addTextBreak(1);
        $section->addText('Приняли участие в итоговой аттестации обучающиеся согласно Приложению № 1 к настоящему протоколу.', null, array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
        $section->addTextBreak(1);
        if ($modelGroup->trainingGroupExperts && $expertFlag) {
            $section->addText('Ответственными лицами и экспертами были заданы вопросы.', null, array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
        }
        else {
            $section->addText('Ответственными лицами были заданы вопросы.', null, array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
        }
        $section->addText('Ответственные лица, ознакомившись с демонстрацией результатов образовательной деятельности каждого обучающегося,', null, array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
        $section->addText('Постановили:', array('bold' => true), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('          1. Признать обучающихся согласно Приложению № 2 к настоящему протоколу успешно прошедшими итоговую аттестацию и выдать сертификаты об обучении.', null, array('align' => 'both', 'spaceAfter' => 0));

        $refPart = 0;
        foreach ($groupParticipants as $part) {
            if ($part->certificateWork) {
                $refPart++;
                if ($refPart > 1) {
                    break;
                }
            }
        }

        if ($refPart !== 0) {
            if ($refPart > 1) {
                $section->addText('          1.1. Признать обучающихся согласно Приложению № 3 к настоящему протоколу непрошедшими итоговую аттестацию и выдать справки об обучении.', null, array('align' => 'both', 'spaceAfter' => 0));
            }
            else {
                $section->addText('          1.1. Признать обучающегося согласно Приложению № 3 к настоящему протоколу непрошедшим итоговую аттестацию и выдать справку об обучении.', null, array('align' => 'both', 'spaceAfter' => 0));
            }
            $section->addText('          2. Рекомендовать обучающимся согласно Приложению № 3 к настоящему протоколу повторно пройти итоговую аттестацию.', null, array('align' => 'both', 'spaceAfter' => 0));
        }

        $section->addTextBreak(1);
        $section->addText('Подписи ответственных лиц:');
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(8000);
        $cell->addText('Руководитель учебной группы');
        $cell = $table->addCell(6000);
        $cell->addText('________________', null, array('align' => 'center'));
        $cell = $table->addCell(6000);
        $cell->addText('/ '.$modelGroup->teachersWork[0]->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) . '/', null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(8000);
        $cell->addText('Руководитель отдела «'.Yii::$app->branches->get($modelGroup->branch).'»');
        $cell = $table->addCell(6000);
        $cell->addText('________________', null, array('align' => 'center'));
        $cell = $table->addCell(6000);
        $cell->addText('/ '. $bossShort . '/', null, array('align' => 'right'));

        foreach ($experts as $expert) {
            if ($expert->expert_id !== $expertExept) {
                $table->addRow();
                $cell = $table->addCell(8000);
                $cell->addText($expert->expertWork->positionWork->name);
                $cell = $table->addCell(6000);
                $cell->addText('________________', null, array('align' => 'center'));
                $cell = $table->addCell(6000);
                $cell->addText('/ '. $expert->expertWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) . '/', null, array('align' => 'right'));
            }
        }

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15)));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение №1', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к протоколу итоговой аттестации', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('«' . date("d", strtotime($modelGroup->protection_date)) . '» '
            . BaseFunctions::monthFromNumbToString(date("m", strtotime($modelGroup->protection_date))) . ' '
            . date("Y", strtotime($modelGroup->protection_date)) . ' г.', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('№ ' . $modelGroup->number, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        $section->addText('Перечень обучающихся, принявших участие в итоговой аттестации', null, array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(1);
        $numberStr = 1;
        foreach ($groupParticipants as $part) {
            $section->addText($numberStr.' '.$part->participantWork->getFIO(PersonInterface::FIO_FULL), null, array('spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
            $numberStr++;
        }

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15)));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение №2', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к протоколу итоговой аттестации', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('«' . date("d", strtotime($modelGroup->protection_date)) . '» '
            . BaseFunctions::monthFromNumbToString(date("m", strtotime($modelGroup->protection_date))) . ' '
            . date("Y", strtotime($modelGroup->protection_date)) . ' г.', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('№ ' . $modelGroup->number, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        $section->addText('Перечень обучающихся, прошедших итоговую аттестацию', null, array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(1);
        $numberStr = 1;
        $isAnnex3 = false;
        foreach ($groupParticipants as $part) {
            if ($part->certificateWork->certificate_number !== NULL) {
                $section->addText($numberStr.' '.$part->participantWork->getFIO(PersonInterface::FIO_FULL), null, array('spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
                $numberStr++;
            }
            else {
                $isAnnex3 = true;
            }
        }

        if ($isAnnex3) {
            $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
                'marginLeft' => WordWizard::convertMillimetersToTwips(30),
                'marginBottom' => WordWizard::convertMillimetersToTwips(20),
                'marginRight' => WordWizard::convertMillimetersToTwips(15)));
            $table = $section->addTable();
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $cell->addText('Приложение №3', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $cell->addText('к протоколу итоговой аттестации', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $cell->addText('«' . date("d", strtotime($modelGroup->protection_date)) . '» '
                . BaseFunctions::monthFromNumbToString(date("m", strtotime($modelGroup->protection_date))) . ' '
                . date("Y", strtotime($modelGroup->protection_date)) . ' г.', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $cell->addText('№ ' . $modelGroup->number, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $section->addTextBreak(2);

            $section->addText('Перечень обучающихся, признанных непрошедшими итоговую аттестацию', null, array('align' => 'center', 'spaceAfter' => 0));
            $section->addTextBreak(1);
            $numberStr = 1;
            foreach ($groupParticipants as $part) {
                if ($part->certificateWork->certificate_number === NULL) {
                    $section->addText($numberStr.' '.$part->participantWork->getFIO(PersonInterface::FIO_FULL), null, array('spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
                    $numberStr++;
                }
            }
        }

        return $inputData;
    }
    public static function generateOrderEvent($order_id)
    {
        /* @var $supplement OrderEventGenerateWork */
        /* @var $order DocumentOrderWork */
        /* @var $oneActPart SquadParticipantWork*/
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));

        $section->addText('Министерство образования и науки Астраханской области', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('государственное автономное образовательное учреждение', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('Астраханской области дополнительного образования', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('«Региональный школьный технопарк»', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('ГАОУ АО ДО «РШТ»', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('ПРИКАЗ', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        /*----------------*/
        $order = DocumentOrderWork::find()->where(['id' => $order_id])->one();
        $res = OrderPeopleWork::find()->where(['order_id' => $order->id])->all();
        $supplement = OrderEventGenerateWork::find()->where(['order_id' => $order_id])->one();
        $foreignEvent = ForeignEventWork::find()->where(['order_participant_id' => $order_id])->one();
        $acts = ArrayHelper::getColumn(ActParticipantWork::find()->where(['foreign_event_id' => $foreignEvent->id])->all(), 'id');
        $teacherParts = SquadParticipantWork::find()->where(['IN', 'act_participant_id' , $acts])->all();

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('«' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г.');
        $cell = $table->addCell(12000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' . $order->order_postfix;
        $cell->addText($text, null, array('align' => 'right'));
        $section->addTextBreak(1);

        $section->addText($order->order_name, null, array('align' => 'both'));
        $section->addTextBreak(1);

        /* переменная цели и соответствия*/
        $purpose = Yii::$app->goals->get($supplement->purpose);
        $invitations = ['', ' и в соответствии с Регламентом', ' и в соответствии с Письмом', ' и в соответствии с Положением'];
        $invitation = $invitations[$supplement->doc_event].' '.$supplement->document_details;
        $section->addText('С целью '.$purpose.$invitation, null, array('align' => 'both', 'indentation' => array('hanging' => -700)));
        $section->addTextBreak(1);
        $section->addText('ПРИКАЗЫВАЮ:', array('lineHeight' => 1.0), array('spaceAfter' => 0));
        $section->addText('1.	Принять участие в мероприятии «'.$foreignEvent->name.'» (далее – мероприятие) и утвердить перечень учащихся, участвующих в мероприятии, и педагогов, ответственных за подготовку и контроль результатов участия в мероприятии, согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('2.	Назначить ответственным за сбор и предоставление информации об участии в мероприятии для внесения в Цифровую систему хранения документов ГАОУ АО ДО «РШТ» (далее – ЦСХД) '.$supplement->respPeopleInfo->getFIO(PersonInterface::FIO_SURNAME_INITIALS).'.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('3.	Определить срок предоставления информации об участии в мероприятии: '.$supplement->time_provision_day.' рабочих дней со дня завершения мероприятия.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('4.	Назначить ответственным за внесение информации об участии в мероприятии в ЦСХД '.$supplement->extraRespInsert->getFIO(PersonInterface::FIO_SURNAME_INITIALS).'.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('5.	Определить срок для внесения информации об участии в мероприятии: '.$supplement->time_insert_day.' рабочих дней со дня завершения мероприятия.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('6.	Назначить ответственным за методический контроль подготовки учащихся к участию в мероприятии и информационное взаимодействие с организаторами мероприятия '.$supplement->extraRespMethod->getFIO(PersonInterface::FIO_SURNAME_INITIALS).'.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('7.	Назначить ответственным за информирование работников о настоящем приказе '.$supplement->extraRespInfoStuff->getFIO(PersonInterface::FIO_SURNAME_INITIALS).'.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('8.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Проект вносит:');
        $cell = $table->addCell(12000);
        $cell->addText($order->bring->getFIO(PersonInterface::FIO_SURNAME_INITIALS), null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Исполнитель:');
        $cell = $table->addCell(12000);
        $cell->addText($order->executor->getFIO(PersonInterface::FIO_SURNAME_INITIALS), null, array('align' => 'right'));

        $section->addText('Ознакомлены:');
        $table = $section->addTable();
        for ($i = 0; $i != count($res); $i++, $c++)
        {
            $fio = $res[$i]->people->getFIO(PersonInterface::FIO_SURNAME_INITIALS);

            $table->addRow();
            $cell = $table->addCell(8000);
            $cell->addText('«___» __________ 20___ г.');
            $cell = $table->addCell(5000);
            $cell->addText('    ________________/', null, array('align' => 'right'));
            $cell = $table->addCell(5000);
            $cell->addText($fio . '/');
        }

        /*тут перечень учащихся*/
        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(20),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к приказу директора', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $cell->addTextBreak(1);
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('УТВЕРЖДАЮ', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Директор ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('_________________ В.В. Войков', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        $section->addText('Перечень учащихся ГАОУ АО ДО «РШТ» – участников мероприятии', array('bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('«'.$foreignEvent->name.'» –', array('bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('с указанием педагогов, ответственных за подготовку участников и контроль', array('bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('результатов участия', array('bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(1);

        $table = $section->addTable(array('borderColor' => '000000', 'borderSize' => '6'));
        $table->addRow();
        $cell = $table->addCell(1000);
        $cell->addText('<w:br/><w:br/><w:br/>№ п/п', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $cell->addText('<w:br/><w:br/><w:br/>Ф.И.О. участника', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(3000);
        $cell->addText('Номинация (разряд, трек, класс, и т.п.), в которой производится участие в мероприятии', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(3000);
        $cell->addText('Направленность образовательных программ, к которой относится участие в мероприятии', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(3000);
        $cell->addText('Отдел ГАОУ АО ДО «РШТ», на базе которого проведена подготовка участника', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $cell->addText('Ф.И.О. педагога, ответственного за подготовку участника и контроль результатов его участия', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $tBranchs = ActParticipantBranch::find();
        foreach ($teacherParts as $key => $oneActPart)
        {
            $table->addRow();
            $cell = $table->addCell(1000);
            $cell->addText($key+1, array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
            $cell = $table->addCell(4000);
            $cell->addText($oneActPart->participantWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
            $cell = $table->addCell(3000);
            $cell->addText($oneActPart->actParticipantWork->nomination, array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
            $cell = $table->addCell(3000);
            $cell->addText(Yii::$app->focus->get($oneActPart->actParticipantWork->focus), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));

            $cell = $table->addCell(3000);
            $branches = $tBranchs->where(['act_participant_id' => $oneActPart->id])->all();
            foreach ($branches as $branch)
                $cell->addText(Yii::$app->branches->get($branch->branch), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));

            $cell = $table->addCell(4000);
            $cell->addText($oneActPart->actParticipantWork->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
            if ($oneActPart->actParticipantWork->teacher2_id != null)
                $cell->addText($oneActPart->actParticipantWork->teacher2Work->getFIO(PersonInterface::FIO_SURNAME_INITIALS), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));

        }
        $text = 'Пр.' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . substr($order->order_name, 0, 35);
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $text . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        return $inputData;
    }
    public static function generateOrderTrainingEnroll($orderId)
    {
        /* @var $group TrainingGroupWork */
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(7000);
        $cell->addText('РЕГИОНАЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'), array('spaceAfter' => 0));
        $cell = $table->addCell(5000, array('borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText(' ШКОЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'), array('spaceAfter' => 0));
        $cell = $table->addCell(18000, array('valign' => 'bottom', 'borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText('  414000, г. Астрахань, ул. Адмиралтейская, д. 21, помещение № 66', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(7000);
        $cell->addImage(Yii::$app->basePath.'/upload/templates/logo.png', array('width'=>100, 'height'=>40, 'align'=>'left'));
        $cell = $table->addCell(5000, array('valign' => 'top'));
        $cell->addText('ТЕХНОПАРК', array('name' => 'Calibri', 'size' => '14'), array('align' => 'center'));
        $cell = $table->addCell(18000);
        $cell->addText(' +7 8512 442428 • schooltech@astrobl.ru • www.школьныйтехнопарк.рф', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array('align' => 'right', 'spaceAfter' => 0));
        //----------
        $section->addTextBreak(1);
        $section->addText('ПРИКАЗ', array('bold' => true), array('align' => 'center'));
        $section->addTextBreak(1);

        /*----------------*/
        $order = DocumentOrderWork::find()->where(['id' => $orderId])->one();
        $trGParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()->where(['order_id' => $order->id])->all(), 'training_group_participant_in_id');
        $groupsId = ArrayHelper::getColumn(TrainingGroupParticipantWork::find()->where(['IN', 'id',  $trGParticipantId])->all(), 'training_group_id');
        $groups = TrainingGroupWork::find()->where(['IN', 'id', $groupsId])->all();
        $pastaAlDente = OrderTrainingGroupParticipantWork::find();
        $program = TrainingProgramWork::find();
        $teacher = TeacherGroupWork::find();
        $trG = TrainingGroupWork::find(); //все группы
        $part = ForeignEventParticipantsWork::find();
        $gPart = TrainingGroupParticipantWork::find();
        $res = OrderPeopleWork::find()->where(['order_id' => $order->id])->all();
        $pos = PeoplePositionCompanyBranchWork::find();
        $positionName = PositionWork::find();

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('«' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г.');
        $cell = $table->addCell(12000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText($text, null, array('align' => 'right'));
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText($order->order_name, null, array('align' => 'left'));
        $cell = $table->addCell(6000);
        $cell->addTextBreak(1);
        $section->addTextBreak(1);

        $countGroup = count($groups);

        $groupsID = [];
        foreach ($groups as $group)
            $groupsID[] = $group->id;
        $allGroups = $trG->where(['in', 'id', $groupsID])->all();
        $programsID = [];
        foreach ($allGroups as $oneGroup)
            $programsID[] = $oneGroup->training_program_id;
        $countProgram = count($program->where(['in', 'id', $programsID])->all());

        $teachersID = [];
        foreach ($groups as $group)
        {
            $trGs = $teacher->where(['training_group_id' => $group->id])->all();
            foreach ($trGs as $trGr)
                $teachersID [] = $trGr->teacher_id;
        }
        $countTeacher = count(array_unique($teachersID));

        if ($trG->where(['id' => $groups[0]->id])->one()->budget === 1)
        {
            $text = 'В соответствии с ч. 1 ст. 53 Федерального закона от 29.12.2012                    № 273-ФЗ «Об образовании в Российской Федерации», Правилами приема обучающихся в государственное автономное образовательное учреждение Астраханской области дополнительного образования «Региональный школьный технопарк» на обучение по дополнительным общеразвивающим программам, на основании заявлений о приеме на обучение по ';
            if ($countProgram == 1)
                $text .= 'дополнительной общеразвивающей программе';
            else
                $text .= 'дополнительным общеразвивающим программам';
        }
        else
            $text = 'В соответствии с ч. 1, ч. 2 ст. 53 Федерального закона от 29.12.2012                    № 273-ФЗ «Об образовании в Российской Федерации», Положением об оказании платных дополнительных образовательных услуг в государственном автономном образовательном учреждении Астраханской области дополнительного образования «Региональный школьный технопарк», на основании договоров об оказании дополнительных платных образовательных услуг и представленных документов';
        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));

        $section->addText('ПРИКАЗЫВАЮ:', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        $text = '          1.	Зачислить обучающихся с «' . date("d", strtotime($order->order_date)) . '» ' . WordWizard::Month(date("m", strtotime($order->order_date))) . ' ' . date("Y", strtotime($order->order_date)) . ' г.';
        if ($countGroup == 1)
            $text .= ' в учебную группу ';
        else
            $text .= ' в учебные группы ';
        $text .= 'ГАОУ АО ДО «РШТ» на обучение по ';
        if ($countProgram == 1)
            $text .= 'дополнительной общеразвивающей программе ';
        else
            $text .= 'дополнительным общеразвивающим программам ';
        $text .= 'согласно Приложению № 1 к настоящему приказу.';
        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        if ($countTeacher == 1)
            $text = '          2.	Назначить руководителем ';
        else
            $text = '          2.	Назначить руководителями ';
        if ($countGroup == 1)
            $text .= 'учебной группы ';
        else
            $text .= 'учебных групп ';
        if ($countTeacher == 1)
            $text .= 'работника ГАОУ АО ДО «РШТ», указанного в Приложении № 1 к настоящему приказу.';
        else
            $text .= 'работников ГАОУ АО ДО «РШТ», указанных в Приложении № 1 к настоящему приказу.';
        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        if ($order->executor_id == $order->bring_id)
            $text = '          3.	Назначить работника, ответственного за организацию образовательного процесса и контроль соблюдения расписания ';
        else
            $text = '          3.	Назначить работников, ответственных за организацию образовательного процесса и контроль соблюдения расписания ';
        if ($countGroup == 1)
            $text .= 'учебной группы согласно Приложению № 2 к настоящему приказу.';
        else
            $text .= 'учебных групп согласно Приложению № 2 к настоящему приказу.';
        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        if ($countTeacher === 1)
            $text = '          4.	Руководителю ';
        else
            $text = '          4.	Руководителям ';
        if ($countGroup == 1)
            $text .= 'учебной группы ';
        else
            $text .= 'учебных групп ';
        $text .= 'проводить с обучающимися инструктажи по технике безопасности в соответствии с ';
        if ($countProgram == 1)
            $text .= 'дополнительной общеразвивающей программой.';
        else
            $text .= 'дополнительными общеразвивающими программами.';
        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        if ($order->executor_id == $order->bring_id)
            $text = '          5.	Назначить работника, ответственного за своевременное ознакомление ';
        else
            $text = '          5.	Назначить работников, ответственных за своевременное ознакомление ';
        if ($countTeacher === 1)
            $text .= 'руководителя ';
        else
            $text .= 'руководителей ';
        if ($countGroup == 1)
            $text .= 'учебной группы ';
        else
            $text .= 'учебных групп ';
        $text .= 'с настоящим приказом согласно Приложению № 2 к настоящему приказу.';

        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('          6.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));


        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Проект вносит:');
        $cell = $table->addCell(12000);
        $cell->addText($order->bring->getFIO(PersonInterface::FIO_SURNAME_INITIALS), null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Исполнитель:');
        $cell = $table->addCell(12000);
        $cell->addText($order->executor->getFIO(PersonInterface::FIO_SURNAME_INITIALS), null, array('align' => 'right'));

        $section->addText('Ознакомлены:');
        $table = $section->addTable();
        for ($i = 0; $i != count($res); $i++, $c++)
        {
            $fio = $res[$i]->people->getFIO(PersonInterface::FIO_SURNAME_INITIALS);

            $table->addRow();
            $cell = $table->addCell(8000);
            $cell->addText('«___» __________ 20___ г.');
            $cell = $table->addCell(5000);
            $cell->addText('    ________________/', null, array('align' => 'right'));
            $cell = $table->addCell(5000);
            $cell->addText($fio . '/');
        }


        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение №1', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к приказу ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(1);
        foreach ($groups as $group)
        {
            $trGroup = $trG->where(['id' => $group->id])->one();
            $section->addText('Идентификатор учебной группы: ' . $trGroup->number);

            $teacherTrG = $teacher->where(['training_group_id' => $group->id])->all();
            $text = 'Руководитель учебной группы: ';

            foreach ($teacherTrG as $trg)
            {
                $post = [];
                $pPosB = $pos->where(['people_id' => $trg->teacher_id])->all();
                foreach ($pPosB as $posOne)
                {
                    $post [] = $posOne->position_id;
                }
                $post = array_unique($post);    // выкинули все повторы
                $post = array_intersect($post, [15, 16, 35, 44]);   // оставили только преподские должности

                if (count($post) > 0)
                {
                    $posName = $positionName->where(['id' => $post[0]])->one();
                    $text .= mb_strtolower($posName->name) . ' ' . $trg->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) . ', ';
                }
                else
                    $text .= $trg->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) . ', ';
            }
            $text = mb_substr($text, 0, -2);
            $section->addText($text);

            $programTrG = $program->where(['id' => $trGroup->training_program_id])->one();
            $section->addText('Дополнительная общеразвивающая программа: «' . $programTrG->name . '»');
            $section->addText('Направленность: ' . mb_strtolower(Yii::$app->focus->get($programTrG->focus)));

            $section->addText('Форма обучения: очная (в случаях, установленных законодательными актами, возможно применение электронного обучения, дистанционных образовательных технологий).');

            $section->addText('Срок освоения (ак.ч.): ' . $programTrG->capacity);

            $section->addText('Обучающиеся: ');
            $localParticipants = ArrayHelper::getColumn(TrainingGroupParticipantWork::find()->where(['training_group_id' => $group->id])->all(), 'id');
            $pasta = $pastaAlDente->where(['order_id' => $order->id])->andWhere(['IN', 'training_group_participant_in_id', $localParticipants ])->all();
            for ($i = 0; $i < count($pasta); $i++)
            {
                $groupParticipant = $gPart->where(['id' => $pasta[$i]->training_group_participant_in_id])->one();
                $participant = $part->where(['id' => $groupParticipant->participant_id])->one();
                $section->addText($i+1 . '. ' . $participant->getFullFio());
            }
            $section->addTextBreak(2);
        }

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение №2', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к приказу ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        $section->addText('Список работников, ответственных за организацию образовательного процесса и контроль соблюдения расписания учебных групп', array('bold' => true), array('align' => 'center'));
        $section->addTextBreak(1);

        $table = $section->addTable(array('borderColor' => '000000', 'borderSize' => '6'));
        $table->addRow();
        $cell = $table->addCell(1000);
        $cell->addText('№', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $cell->addText('Ф.И.О. ответственного работника', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $cell->addText('Должность', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(9000);
        $cell->addText('Зона ответственности', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(1000);
        $cell->addText('1', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $posOne = $pos->where(['people_id' => $order->executor_id])->one();
        $cell->addText($order->executor->getFIO(PersonInterface::FIO_SURNAME_INITIALS) . '. ', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(3000);
        $cell->addText(mb_strtolower(mb_substr($posOne->position->name, 0, 1)) . mb_substr($posOne->position->name, 1), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(10000);
        $text = '- контроль соблюдения расписания ';
        if ($countGroup == 1)
            $text .= 'учебной группы и соответствия тематики проводимых учебных занятий ';
        else
            $text .= 'учебных групп и соответствия тематики проводимых учебных занятий ';
        if ($countProgram == 1)
            $text .= 'дополнительной общеразвивающей программе';
        else
            $text .= 'дополнительным общеразвивающим программам';
        $cell->addText($text, array('size' => '12'), array('align' => 'both', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(1000);
        $cell->addText('2', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $posOne = $pos->where(['people_id' => $order->bring_id])->one();
        $cell->addText($order->bring->getFIO(PersonInterface::FIO_SURNAME_INITIALS) . '. ', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $cell->addText(mb_strtolower(mb_substr($posOne->position->name, 0, 1)) . mb_substr($posOne->position->name, 1), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(9000);
        $text = '- своевременное ознакомление ';
        if ($countTeacher == 1)
            $text .= 'руководителя ';
        else
            $text .= 'руководителей ';
        if ($countGroup == 1)
            $text .= 'учебной группы с настоящим приказом';
        else
            $text .= 'учебных групп с настоящим приказом';
        $cell->addText($text, array('size' => '12'), array('align' => 'both', 'spaceAfter' => 0));


        $text = 'Пр.' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . mb_substr($order->order_name, 0, 20);
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $text . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');


        return $inputData;
    }
    public static function generateOrderTrainingDeduct($orderId)
    {
        /* @var $group TrainingGroupWork */
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(7000);
        $cell->addText('РЕГИОНАЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'), array('spaceAfter' => 0));
        $cell = $table->addCell(5000, array('borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText(' ШКОЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'), array('spaceAfter' => 0));
        $cell = $table->addCell(18000, array('valign' => 'bottom', 'borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText('  414000, г. Астрахань, ул. Адмиралтейская, д. 21, помещение № 66', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(7000);
        $cell->addImage(Yii::$app->basePath.'/upload/templates/logo.png', array('width'=>100, 'height'=>40, 'align'=>'left'));
        $cell = $table->addCell(5000, array('valign' => 'top'));
        $cell->addText('ТЕХНОПАРК', array('name' => 'Calibri', 'size' => '14'), array('align' => 'center'));
        $cell = $table->addCell(18000);
        $cell->addText(' +7 8512 442428 • schooltech@astrobl.ru • www.школьныйтехнопарк.рф', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array('align' => 'right', 'spaceAfter' => 0));
        //----------
        $section->addTextBreak(1);
        $section->addText('ПРИКАЗ', array('bold' => true), array('align' => 'center'));
        $section->addTextBreak(1);

        /*----------------*/
        $order = DocumentOrderWork::find()->where(['id' => $orderId])->one();
        $trGParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()->where(['order_id' => $order->id])->all(), 'training_group_participant_out_id');
        $groupsId = ArrayHelper::getColumn(TrainingGroupParticipantWork::find()->where(['IN', 'id',  $trGParticipantId])->all(), 'training_group_id');
        $groups = TrainingGroupWork::find()->where(['IN', 'id', $groupsId])->all();
        $pastaAlDente = OrderTrainingGroupParticipantWork::find();
        $program = TrainingProgramWork::find();
        $teacher = TeacherGroupWork::find();
        $trG = TrainingGroupWork::find(); //все группы
        $part = ForeignEventParticipantsWork::find();
        $gPart = TrainingGroupParticipantWork::find();
        $res = OrderPeopleWork::find()->where(['order_id' => $order->id])->all();
        $pos = PeoplePositionCompanyBranchWork::find();
        $positionName = PositionWork::find();


        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('«' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г.');
        $cell = $table->addCell(12000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText($text, null, array('align' => 'right'));
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText($order->order_name, null, array('align' => 'left'));
        $cell = $table->addCell(6000);
        $cell->addTextBreak(1);

        $section->addTextBreak(1);
        $countPasta = $pastaAlDente->where(['order_id' => $order->id])->count();
        if ($order->study_type == 0)        // Ф-3
        {
            $text = '          В связи с завершением обучения в ГАОУ АО ДО «РШТ», на основании ';
            if ($countPasta > 1)
                $text .= 'протоколов итоговой аттестации от «___»_______ 20___ г.';
            else
                $text .= 'протокола итоговой аттестации от «___»_______ 20___ г.';
        }
        else if ($order->study_type == 1)    //Ф-4
        {
            $text = '          В связи с завершением обучения в ГАОУ АО ДО «РШТ»», на основании ';
            if ($countPasta > 1)
                $text .= 'протоколов итоговой аттестации от «___»_______ 20___ г.';
            else
                $text .= 'протокола итоговой аттестации от «___»_______ 20___ г.';
        }
        else if ($order->study_type == 2)
        {
            $text = '          В связи с досрочным прекращением образовательных отношений на основании статьи 61 Федерального закона от 29.12.2012 № 273-ФЗ «Об образовании в Российской Федерации» и ';
            if ($countPasta > 1)
                $text .= 'заявлений родителей или законных представителей,   ';
            else
                $text .= 'заявления родителя или законного представителя,   ';
        }
        else
            $text = '          В связи с досрочным прекращением образовательных отношений на основании статьи 61 Федерального закона от 29.12.2012 № 273-ФЗ «Об образовании в Российской Федерации», п. 6.2.3 договоров об оказании платных дополнительных образовательных услуг,  ';

        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('ПРИКАЗЫВАЮ:', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        if ($order->study_type == 0 && $countPasta > 1)
        {
            $section->addText('          1.	Отчислить обучающихся согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            $section->addText('          2.	Выдать обучающимся, указанным в Приложении к настоящему приказу, сертификаты об успешном завершении обучения.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        }
        if ($order->study_type == 0 && $countPasta == 1)
        {
            $section->addText('          1.	Отчислить обучающегося согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            $section->addText('          2.	Выдать обучающемуся, указанному в Приложении к настоящему приказу, сертификат об успешном завершении обучения.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        }
        if ($order->study_type == 1 && $countPasta > 1)
        {
            $section->addText('          1.	Отчислить обучающихся согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            $section->addText('          2.	Выдать обучающимся, не прошедшим итоговую форму контроля и указанным в Приложении к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного учреждением образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        }
        if ($order->study_type == 1 && $countPasta == 1)
        {
            $section->addText('          1.	Отчислить обучающегося согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            $section->addText('          2.	Выдать обучающемуся, не прошедшему итоговую форму контроля и указанному в Приложении к настоящему приказу, справку об обучении в ГАОУ АО ДО «РШТ» установленного учреждением образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        }
        if ($order->study_type == 0 || $order->study_type == 1)
            $section->addText('          3.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        if ($order->study_type == 2)
        {
            if ($trG->where(['id' => $groups[0]->id])->one()->budget === 1)
            {
                if ($countPasta > 1)
                {
                    $section->addText('          1.	Отчислить обучающихся согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                    $section->addText('          2.	Выдать обучающимся, указанным в Приложении к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного учреждением образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                }
                else
                {
                    $section->addText('          1.	Отчислить обучающегося согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                    $section->addText('          2.	Выдать обучающемуся, указанному в Приложении к настоящему приказу, справку об обучении в ГАОУ АО ДО «РШТ» установленного учреждением образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                }
                $section->addText('          3.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            }
            else
            {
                if ($countPasta > 1)
                {
                    $section->addText('          1.	Расторгнуть договора об оказании платных дополнительных образовательных услуг согласно Приложению № 1.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                    $section->addText('          2.	Отчислить обучающихся согласно Приложению № 2 к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                    $section->addText('          3.	Выдать обучающимся, указанным в Приложении № 2 к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                }
                else
                {
                    $section->addText('          1.	Расторгнуть договор об оказании платных дополнительных образовательных услуг согласно Приложению № 1.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                    $section->addText('          2.	Отчислить обучающегося согласно Приложению № 2 к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                    $section->addText('          3.	Выдать обучающемуся, указанному в Приложении № 2 к настоящему приказу, справку об обучении в ГАОУ АО ДО «РШТ» установленного образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                }
                $section->addText('          4.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            }
        }

        if ($order->study_type == 3)
        {
            if ($countPasta > 1)
            {
                $section->addText('          1.	Расторгнуть договора об оказании платных дополнительных образовательных услуг согласно Приложению № 1.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                $section->addText('          2.	Отчислить обучающихся согласно Приложению № 2 к настоящему приказу. ', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                $section->addText('          3.	Выдать обучающимся, указанным в Приложении № 2 к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            }
            else
            {
                $section->addText('          1.	Расторгнуть договор об оказании платных дополнительных образовательных услуг согласно Приложению № 1.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                $section->addText('          2.	Отчислить обучающегося согласно Приложению № 2 к настоящему приказу. ', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                $section->addText('          3.	Выдать обучающемуся, указанному в Приложении № 2 к настоящему приказу, справку об обучении в ГАОУ АО ДО «РШТ» установленного образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            }
            $section->addText('          4.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        }

        //$section->addText($text, null, array('align' => 'both'));


        $section->addTextBreak(2);
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));
        $section->addTextBreak(1);


        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Проект вносит:');
        $cell = $table->addCell(12000);
        $cell->addText($order->bring->getFIO(PersonInterface::FIO_SURNAME_INITIALS), null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Исполнитель:');
        $cell = $table->addCell(12000);
        $cell->addText($order->executor->getFIO(PersonInterface::FIO_SURNAME_INITIALS), null, array('align' => 'right'));
        $section->addTextBreak(1);
        $section->addText('Ознакомлены:');
        $table = $section->addTable();
        for ($i = 0; $i != count($res); $i++, $c++)
        {
            $fio = $res[$i]->people->getFIO(PersonInterface::FIO_SURNAME_INITIALS);

            $table->addRow();
            $cell = $table->addCell(8000);
            $cell->addText('«___» __________ 20___ г.');
            $cell = $table->addCell(5000);
            $cell->addText('    ________________/', null, array('align' => 'right'));
            $cell = $table->addCell(5000);
            $cell->addText($fio . '/');
        }

        if (($order->study_type == 2 && $trG->where(['id' => $groups[0]->id])->one()->budget !== 1) || $order->study_type == 3)
        {
            $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
                'marginLeft' => WordWizard::convertMillimetersToTwips(30),
                'marginBottom' => WordWizard::convertMillimetersToTwips(20),
                'marginRight' => WordWizard::convertMillimetersToTwips(15)));
            $table = $section->addTable();
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $cell->addText('Приложение №1', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $cell->addText('к приказу ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
            if ($order->order_postfix !== NULL)
                $text .= '/' . $order->order_postfix;
            $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
                . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
                . date("Y", strtotime($order->order_date)) . ' г. '
                . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $section->addTextBreak(2);

            $text = '';
            for ($i = 0; $i < $countPasta; $i++)
            {
                $text .= '<w:br/>' . ($i + 1) . '. Договор об оказании платных дополнительных образовательных услуг от __________ г. № ____.';
            }
            $section->addText($text, null, array('align' => 'both'));
        }

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        if (($order->study_type == 2 && $trG->where(['id' => $groups[0]->id])->one()->budget !== 1) || $order->study_type == 3)
            $cell->addText('Приложение №2', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        else
            $cell->addText('Приложение', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к приказу ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);//8000 10000
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        foreach ($groups as $group)
        {
            $trGroup = $trG->where(['id' => $group->id])->one();
            $section->addText('Идентификатор учебной группы: ' . $trGroup->number);

            $teacherTrG = $teacher->where(['training_group_id' => $group->id])->all();
            $text = 'Руководитель учебной группы: ';

            foreach ($teacherTrG as $trg)
            {
                $post = [];
                $pPosB = $pos->where(['people_id' => $trg->teacher_id])->all();
                foreach ($pPosB as $posOne)
                {
                    $post [] = $posOne->position_id;
                }
                $post = array_unique($post);    // выкинули все повторы
                $post = array_intersect($post, [15, 16, 35, 44]);   // оставили только преподские должности

                if (count($post) > 0)
                {
                    $posName = $positionName->where(['id' => $post[0]])->one();
                    $text .= mb_strtolower($posName->name) . ' ' . $trg->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) . ', ';
                }
                else
                    $text .= $trg->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) . ', ';
            }
            $text = mb_substr($text, 0, -2);
            $section->addText($text);

            $programTrG = $program->where(['id' => $trGroup->training_program_id])->one();
            $section->addText('Дополнительная общеразвивающая программа: «' . $programTrG->name . '»');
            $section->addText('Направленность: ' . Yii::$app->focus->get($programTrG->focus));

            $section->addText('Форма обучения: очная (в случаях, установленных законодательными актами, возможно применение электронного обучения с дистанционными образовательными технологиями).');

            $section->addText('Срок освоения: ' . $programTrG->capacity . ' академ. ч.');
            $section->addText('Обучающиеся: ');
            $localParticipants = ArrayHelper::getColumn(TrainingGroupParticipantWork::find()->where(['training_group_id' => $group->id])->all(), 'id');
            $pasta = $pastaAlDente->where(['order_id' => $order->id])->andWhere(['IN', 'training_group_participant_out_id', $localParticipants ])->all();
            for ($i = 0; $i < count($pasta); $i++)
            {
                $groupParticipant = $gPart->where(['id' => $pasta[$i]->training_group_participant_out_id])->one();
                $participant = $part->where(['id' => $groupParticipant->participant_id])->one();
                $section->addText($i+1 . '. ' . $participant->getFullFio());
            }
            $section->addTextBreak(2);
        }

        $text = 'Пр.' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . mb_substr($order->order_name, 0, 20);
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $text . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        return $inputData;
    }
    public static function generateOrderTrainingTransfer($orderId)
    {
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(7000);
        $cell->addText('РЕГИОНАЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'));
        $cell = $table->addCell(5000, array('borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText(' ШКОЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'));
        $cell = $table->addCell(18000, array('valign' => 'bottom', 'borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText('  414000, г. Астрахань, ул. Адмиралтейская, д. 21, помещение № 66', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(7000);
        $cell->addImage(Yii::$app->basePath.'/upload/templates/logo.png', array('width'=>100, 'height'=>40, 'align'=>'left'));
        $cell = $table->addCell(5000, array('valign' => 'top'));
        $cell->addText('ТЕХНОПАРК', array('name' => 'Calibri', 'size' => '14'), array('align' => 'center'));
        $cell = $table->addCell(18000);
        $cell->addText(' +7 8512 442428 • schooltech@astrobl.ru • www.школьныйтехнопарк.рф', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array('align' => 'right', 'spaceAfter' => 0));
        //----------
        $section->addTextBreak(1);
        $section->addText('ПРИКАЗ', array('bold' => true), array('align' => 'center'));
        $section->addTextBreak(1);

        //----------------
        $order = DocumentOrderWork::find()->where(['id' => $orderId])->one();
        $trGParticipantId = ArrayHelper::getColumn(
            OrderTrainingGroupParticipantWork::find()
                ->where(['order_id' => $order->id])
                ->andWhere(['IS NOT', 'training_group_participant_out_id', NULL])
                ->andWhere(['IS NOT', 'training_group_participant_in_id', NULL])
                ->all(), 'training_group_participant_out_id');
        $trGParticipantOut = ArrayHelper::getColumn(
            OrderTrainingGroupParticipantWork::find()
                ->where(['order_id' => $order->id])
                ->andWhere(['IS NOT', 'training_group_participant_out_id', NULL])
                ->andWhere(['IS NOT', 'training_group_participant_in_id', NULL])
                ->all(), 'training_group_participant_in_id');
        $groupsIdIn = ArrayHelper::getColumn(TrainingGroupParticipantWork::find()->where(['IN', 'id',  $trGParticipantId])->all(), 'training_group_id');
        $groupsIdOut = ArrayHelper::getColumn(TrainingGroupParticipantWork::find()->where(['IN', 'id',  $trGParticipantOut])->all(), 'training_group_id');
        $groups = TrainingGroupWork::find()->where(['IN', 'id', $groupsIdIn])->all();
        $groupsOUT = TrainingGroupWork::find()->where(['IN', 'id', $groupsIdOut])->all();
        //var_dump(ArrayHelper::getColumn($groupsOUT, 'number'));
        $tempGID = [];
        foreach ($groups as $g)
            $tempGID[] = $g->id;
        $tempGID[] = 0;

        $part = ForeignEventParticipantsWork::find();
        $teacher = TeacherGroupWork::find();
        $gPartIN = TrainingGroupParticipantWork::find()->where(['IN', 'id',  $trGParticipantId])->all();
        $countPart = count($gPartIN);
        //var_dump($gPartIN->createCommand()->getRawSql());

        $groupsID = [];
        foreach ($gPartIN as $tempPart)
        {
            if (!in_array($tempPart->training_group_id, $groupsID))
                $groupsID[] = $tempPart->training_group_id;
        }

        //$programsIN = TrainingProgramWork::find()->joinWith(['trainingGroups trG'])->where(['IN', 'trG.id', $groupsID])->groupBy('training_program.id')->all();
        //$programsOUT = TrainingProgramWork::find()->joinWith(['trainingGroups trG'])->joinWith(['trainingGroups.orderGroups orderGr'])->where(['orderGr.document_order_id' => $orderId])->all();

        $programsIN = TrainingProgramWork::find()->where(['IN', 'id',  ArrayHelper::getColumn($groups, 'training_program_id')])->all();
        $programsOUT = TrainingProgramWork::find()->where(['IN', 'id',  ArrayHelper::getColumn($groupsOUT, 'training_program_id')])->all();


        $res = OrderPeopleWork::find()->where(['order_id' => $order->id])->all();
        $pos = PeoplePositionCompanyBranchWork::find();
        $positionName = PositionWork::find();

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('«' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г.');
        $cell = $table->addCell(12000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' . $order->order_postfix;
        $cell->addText($text, null, array('align' => 'right'));
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText($order->order_name, null, array('align' => 'left'));
        $cell = $table->addCell(6000);
        $cell->addTextBreak(1);

        $section->addTextBreak(1);
        if ($order->study_type == 0)
        {
            $text = 'На основании решения Педагогического совета ГАОУ АО ДО «РШТ» от «____»_________ 20___ г. № ______, в соответствии с п. 2.1.1 Положения о порядке и основаниях перевода, отчисления и восстановления обучающихся государственного автономного образовательного учреждения Астраханской области дополнительного образования «Региональный школьный технопарк»';
        }
        if ($order->study_type == 1 || $order->study_type == 2)
        {
            $text = 'На основании ';
            if ($countPart <= 1)
                $text .= 'заявления родителя (или законного представителя) ';
            else
                $text .= 'заявлений родителей (или законных представителей) ';

            if ($order->study_type == 1)
                $text .= 'и решения Педагогического совета ГАОУ АО ДО «РШТ» от «____»_________ 20___ г. № ______, в соответствии с п. 2.1.2 Положения о порядке и основаниях перевода, отчисления и восстановления обучающихся государственного автономного образовательного учреждения Астраханской области дополнительного образования «Региональный школьный технопарк»';
            else if ($order->study_type == 2)
                $text .= 'от «___»_________ 20___ г., в соответствии с п. 2.1.3 Положения о порядке и основаниях перевода, отчисления и восстановления обучающихся государственного автономного образовательного учреждения Астраханской области дополнительного образования «Региональный школьный технопарк»';
        }

        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));

        $section->addText('ПРИКАЗЫВАЮ:', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));

        if ($order->study_type == 0)
        {
            $text = '          1.	Перевести ';
            if ($countPart <= 1)
                $text .= 'обучающегося, успешно прошедшего итоговую форму контроля, ';
            else
                $text .= 'обучающихся, успешно прошедших итоговую форму контроля, ';
            $text .= 'на следующий год обучения по дополнительным общеразвивающим программам согласно Приложению к настоящему приказу.';
        }
        if ($order->study_type == 1 || $order->study_type == 2)
        {
            // если внезапно, по какой-то причине вошли в условие, значит регистратор приказа накосячил
            if (((count($programsIN) > 1 || count($programsOUT) > 1) && $order->study_type == 1) || ((count($groups) > 1 /*|| count($groupsID) > 1*/) && $order->study_type == 2))
            {
                if ($order->study_type == 1)
                    $message = ['Невозможно сгенерировать приказ, т.к. отсутствуют утвержденные формы! К приказу о переводе из одной ДОП в другую ДОП добавлено слишком много учебных групп с разными образовательными программами.', 'При генерации приказа ID='.$orderId.' обнаружена ошибка: у всех групп (из которой переводят) должна быть одна ДОП, у всех групп в которую переводят тоже должна быть одна ДОП. Регистратор приказа создает приказ по которому отсутствует утвержденная форма'];
                else
                    $message = ['Невозможно сгенерировать приказ, т.к. отсутствуют утвержденные формы! К приказу о переводе из одной группы в другую добавлено слишком много учебных групп.', 'При генерации приказа ID='.$orderId.' обнаружена ошибка: должна быть одна группа из которой переводят и одна группа в которую переводят. Регистратор приказа создает приказ по которому отсутствует утвержденная форма'];
                Yii::$app->session->setFlash('danger', $message[0]);
                return;
            }

            if ($order->study_type == 1)
            {
                $text = '          1.	Перевести с обучения по дополнительной общеразвивающей программе «' . $programsOUT[0]->name . '» ('. mb_substr(mb_strtolower($programsOUT[0]->stringFocus), 0, mb_strlen($programsOUT[0]->stringFocus) - 2, "utf-8")
                    . 'ой направленности) на обучение по дополнительной общеразвивающей программе «' . $programsIN[0]->name . '» ('. mb_substr(mb_strtolower($programsIN[0]->stringFocus), 0, mb_strlen($programsIN[0]->stringFocus) - 2, "utf-8") . 'ой направленности) ';
            }
            else if ($order->study_type == 2)
            {
                $oldGr = TrainingGroupWork::find()->where(['id' => $groupsOUT[0]->id])->one();
                $newGr = TrainingGroupWork::find()->where(['id' => $groupsID[0]])->one();

                $text = '          1.	Перевести из учебной группы ' . $oldGr->number . ' в учебную группу ' . $newGr->number .  ' в рамках обучения по дополнительной общеразвивающей программе «' . $programsIN[0]->name . '», '
                    . mb_substr(mb_strtolower(Yii::$app->focus->get($programsIN[0]->focus)), 0, mb_strlen(Yii::$app->focus->get($programsIN[0]->focus)) - 2, "utf-8") . 'ой направленности ';
            }

            if ($countPart <= 1)
                $text .= 'обучающегося согласно Приложению к настоящему приказу.';
            else
                $text .= 'обучающихся согласно Приложению к настоящему приказу.';
        }

        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('          2.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));


        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));


        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Проект вносит:');
        $cell = $table->addCell(12000);
        $cell->addText($order->bring->getFIO(PersonInterface::FIO_SURNAME_INITIALS), null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Исполнитель:');
        $cell = $table->addCell(12000);
        $cell->addText($order->executor->getFIO(PersonInterface::FIO_SURNAME_INITIALS), null, array('align' => 'right'));

        $section->addText('Ознакомлены:');
        $table = $section->addTable();
        for ($i = 0; $i != count($res); $i++, $c++)
        {
            $fio = $res[$i]->people->getFIO(PersonInterface::FIO_SURNAME_INITIALS);
            $table->addRow();
            $cell = $table->addCell(8000);
            $cell->addText('«___» __________ 20___ г.');
            $cell = $table->addCell(5000);
            $cell->addText('    ________________/', null, array('align' => 'right'));
            $cell = $table->addCell(5000);
            $cell->addText($fio . '/');
        }


        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение №1', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к приказу ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(1);


        foreach ($groupsID as $group)
        {
            if ($groups[0] !== $group) {
                $trGroup = TrainingGroupWork::find()->where(['id' => $group])->one();
                $section->addText('Идентификатор учебной группы: ' . $trGroup->number);

                $teacherTrG = $teacher->where(['training_group_id' => $group])->all();
                $text = 'Руководитель учебной группы: ';

                foreach ($teacherTrG as $trg) {
                    $post = [];
                    $pPosB = $pos->where(['people_id' => $trg->teacher_id])->all();
                    foreach ($pPosB as $posOne) {
                        $post [] = $posOne->position_id;
                    }
                    $post = array_unique($post);    // выкинули все повторы
                    $post = array_intersect($post, [15, 16, 35, 44]);   // оставили только преподские должности

                    if (count($post) > 0) {
                        $posName = $positionName->where(['id' => $post[0]])->one();
                        $text .= mb_strtolower($posName->name) . ' ' . $trg->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) . ', ';
                    } else
                        $text .= $trg->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) . ', ';
                }
                $text = mb_substr($text, 0, -2);
                $section->addText($text);

                $programTrG = TrainingProgramWork::find()->where(['id' => $trGroup->training_program_id])->one();
                $section->addText('Дополнительная общеразвивающая программа: «' . $programTrG->name . '»');
                $section->addText('Направленность: ' . Yii::$app->focus->get($programTrG->focus));

                $section->addText('Форма обучения: очная (в случаях, установленных законодательными актами, возможно применение электронного обучения, дистанционных образовательных технологий).');

                $section->addText('Срок освоения (ак.ч.): ' . $programTrG->capacity);

                $section->addText('Обучающиеся: ');
                $participants = TrainingGroupParticipantWork::find()->where(['IN', 'id' , $trGParticipantId])->all();
                for ($i = 0; $i < count($participants); $i++) {
                    $participant = ForeignEventParticipantsWork::find()->where(['id' => $participants[$i]->participant_id])->one();
                    $section->addText($i + 1 . '. ' . $participant->getFullFio());
                }
                $section->addTextBreak(2);
            }
        }

        $text = 'Пр.' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . substr($order->order_name, 0, 20);
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $text . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        return $inputData;
    }
}