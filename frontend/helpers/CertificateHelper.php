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

    public static function getMainText(TrainingGroupParticipantWork $participant, array $genderVerbs, int $maxPoints = 100)
    {
        $typeText = '';
        if ($participant->trainingGroupWork->trainingProgramWork->isProjectCertificate()) {
            if ($participant->groupProjectThemesWork){
                $typeText = ', ' . $genderVerbs[1] . ' '. mb_strtolower($participant->groupProjectThemesWork->projectThemeWork->getProjectTypeString()) .' проект "'
                    . $participant->groupProjectThemesWork->projectThemeWork->name . '" и ' . $genderVerbs[2] . ' на научной конференции.';
            }
        }
        if ($participant->trainingGroupWork->trainingProgramWork->isControlWorkCertificate()) {
            $typeText = ', ' . $genderVerbs[1] . ' итоговую контрольную работу с результатом '
                . $participant->points .' из '.$maxPoints.' баллов.';
        }
        if ($participant->trainingGroupWork->trainingProgramWork->isOpenLessonCertificate()) {
            if ($participant->groupProjectThemesWork) {
                $typeText = ', ' . $genderVerbs[1] . ' ' . mb_strtolower($participant->groupProjectThemesWork->projectThemeWork->getProjectTypeString()) . ' проект "'
                    . $participant->groupProjectThemesWork->projectThemeWork->name . '" и ' . $genderVerbs[3] . ' его в публичном выступлении на открытом уроке.';
            }
        }

        return 'успешно '. $genderVerbs[0] . ' обучение по дополнительной общеразвивающей программе 
                            "'.$participant->trainingGroupWork->trainingProgramWork->name.'" в объеме '
                            .$participant->trainingGroupWork->trainingProgram->capacity .' ак. ч.'. $typeText;
    }

    public static function getPointText(TrainingGroupParticipantWork $participant, int $maxPoints = 100) {
        return 'с результатом '. $participant->points .' баллов из '. $maxPoints .' возможных.';
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