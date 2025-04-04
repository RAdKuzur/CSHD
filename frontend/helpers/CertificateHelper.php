<?php

namespace frontend\helpers;

use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;

class CertificateHelper
{
    public static function getGenderVerbs(ForeignEventParticipantsWork $participant)
    {
        if ($participant->isFemale()){
            return ['прошла', 'выполнила', 'выступила', 'представила', 'приняла'];
        }
        else {
            return ['прошел', 'выполнил', 'выступил', 'представил', 'принял'];
        }
    }

    public static function getMainText(TrainingGroupParticipantWork $participant, array $genderVerbs)
    {
        $typeText = '';
        if ($participant->trainingGroupWork->trainingProgramWork->isProjectCertificate()) {
            $typeText = ', ' . $genderVerbs[1] . ' '. mb_strtolower($participant->groupProjectThemesWork->projectThemeWork->getProjectTypeString()) .' проект "'
                . $participant->groupProjectThemesWork->projectThemeWork->name . '" и ' . $genderVerbs[2] . ' на научной конференции "SchoolTech Conference".';
        }
        if ($participant->trainingGroupWork->trainingProgramWork->isControlWorkCertificate()) {
            $typeText = ', ' . $genderVerbs[1] . ' итоговую контрольную работу с оценкой '
                . $participant->points .' из 100 баллов.';
        }
        if ($participant->trainingGroupWork->trainingProgramWork->isOpenLessonCertificate()) {
            $typeText = ', ' . $genderVerbs[1] . ' '. mb_strtolower($participant->groupProjectThemesWork->projectThemeWork->getProjectTypeString()) .' проект "'
                . $participant->groupProjectThemesWork->projectThemeWork->name . '" и ' . $genderVerbs[3] . ' его в публичном выступлении на открытом уроке.';
        }

        return 'успешно '. $genderVerbs[0] . ' обучение по дополнительной общеразвивающей программе 
                            "'.$participant->trainingGroupWork->trainingProgramWork->name.'" в объеме '
                            .$participant->trainingGroupWork->trainingProgram->capacity .' ак. ч.'. $typeText;
    }

    public static function getTextSize(int $textLength)
    {
        if ($textLength >= 1070) {
            return 13;
        }

        if ($textLength >= 920) {
            return 15;
        }

        if ($textLength >= 650) {
            return 17;
        }

        return 19;
    }
}