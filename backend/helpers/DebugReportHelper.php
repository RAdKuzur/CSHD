<?php

namespace backend\helpers;

use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Yii;

class DebugReportHelper
{
    /**
     * Форматирует справочную информацию об участнике в CSV-строку
     *
     * @param TrainingGroupParticipantWork $participant
     * @return array
     */
    public static function createParticipantsDataCsv(TrainingGroupParticipantWork $participant)
    {
        $branch = Yii::$app->branches->get($participant->trainingGroupWork->branch);
        $focus = Yii::$app->focus->get($participant->trainingGroupWork->trainingProgramWork->focus);
        $thematicDirection = Yii::$app->thematicDirection->get($participant->trainingGroupWork->trainingProgramWork->thematic_direction);
        $allowRemote = Yii::$app->allowRemote->get($participant->trainingGroupWork->trainingProgramWork->allow_remote);
        $projectType = Yii::$app->projectType->get($participant->groupProjectThemesWork->projectThemeWork->project_type);
        $teacher = count($participant->trainingGroupWork->teachersWork) > 0 ?
            $participant->trainingGroupWork->teachersWork[0]->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS):
            '';

        $expertsList = $participant->trainingGroupWork->expertsWork;
        $expert = count($expertsList) > 0 ?
            $expertsList[0]->expertWork->getFIO(PersonInterface::FIO_FULL) :
            '';
        $expertType = count($expertsList) > 0 ?
            $expertsList[0]->getExpertTypeString() :
            '';
        $expertCompany = count($expertsList) > 0 && count($expertsList[0]->expertWork->peopleWork->positionCompanyWork) > 0?
            $expertsList[0]->expertWork->peopleWork->positionCompanyWork[0]->companyWork->name :
            '';
        $expertPosition = count($expertsList) > 0 && count($expertsList[0]->expertWork->peopleWork->positionCompanyWork) > 0?
            $expertsList[0]->expertWork->peopleWork->positionCompanyWork[0]->positionWork->name :
            '';

        return [
            $participant->participantWork->getFIO(PersonInterface::FIO_FULL),
            $participant->trainingGroupWork->number,
            $participant->trainingGroupWork->start_date,
            $participant->trainingGroupWork->finish_date,
            $branch,
            $participant->participantWork->getSexString(),
            $participant->participantWork->birthdate,
            $focus,
            $teacher,
            $participant->trainingGroupWork->getBudgetString(),
            $thematicDirection,
            $participant->trainingGroupWork->trainingProgramWork->name,
            $allowRemote,
            $participant->success,
            $participant->groupProjectThemesWork->projectThemeWork->name,
            $participant->trainingGroupWork->protection_date,
            $projectType,
            $expert,
            $expertType,
            $expertCompany,
            $expertPosition,
        ];
    }

    public static function getParticipantsReportHeaders()
    {
        return [
            'ФИО обучающегося',
            'Группа',
            'Дата начала занятий',
            'Дата окончания занятий',
            'Отдел',
            'Пол',
            'Дата рождения',
            'Направленность',
            'Педагог',
            'Основа',
            'Тематическое направление',
            'Образовательная программа',
            'Форма реализации',
            'Успешное завершение',
            'Тема проекта',
            'Дата защиты',
            'Тип проекта',
            'ФИО эксперта',
            'Тип эксперта',
            'Место работы эксперта',
            'Должность эксперта'
        ];
    }

    public static function getManHoursReportHeaders()
    {
        return [
            'Группа',
            'Кол-во занятий выбранного педагога',
            'Кол-во занятий всех педагогов',
            'Кол-во учеников',
            'Кол-во ч/ч'
        ];
    }

    public static function getEventReportHeaders()
    {
        return [
            'Мероприятия',
            'Организатор',
            'Уровень',
            'Дата начала',
            'Дата окончания',
            'Кол-во инд. участников',
            'Кол-во команд',
            'Призеры инд.',
            'Призеры-команды',
            'Победители инд.',
            'Победители-команды',
        ];
    }

}