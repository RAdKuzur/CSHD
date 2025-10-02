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
    // В классе DebugReportHelper

    public static function createParticipantsDataCsv(TrainingGroupParticipantWork $participant)
    {
        // Для обратной совместимости оставляем старый метод, но используем оптимизированный внутри
        $services = self::getServices();
        return self::processParticipantData($participant, $services);
    }

    /**
     * Создание отладочных данных участников с батчевой оптимизацией
     */
    public static function createParticipantDebugData(array $participants): array
    {
        $data = [];
        $batchSize = 100;
        $batches = array_chunk($participants, $batchSize);

        $services = self::getServices();

        foreach ($batches as $batch) {
            // Предзагружаем все связи для всего батча
            $preloadedParticipants = self::preloadBatchData($batch);

            foreach ($preloadedParticipants as $participant) {
                $data[] = self::processParticipantData($participant, $services);
            }

            unset($batch, $preloadedParticipants);
            gc_collect_cycles();
        }

        return $data;
    }

    /**
     * Предзагрузка данных для всего батча участников
     */
    private static function preloadBatchData(array $participants): array
    {
        $participantIds = array_map(fn($p) => $p->id, $participants);

        return TrainingGroupParticipantWork::find()
            ->where(['id' => $participantIds])
            ->with([
                'trainingGroupWork.trainingProgramWork',
                'trainingGroupWork.teachersWork.teacherWork' => function($query) {
                    $query->orderBy(['id' => SORT_ASC])->limit(1);
                },
                'trainingGroupWork.expertsWork.expertWork.peopleWork.peoplePositionCompanyBranchWork.companyWork' => function($query) {
                    $query->orderBy(['id' => SORT_ASC])->limit(1);
                },
                'trainingGroupWork.expertsWork.expertWork.peopleWork.peoplePositionCompanyBranchWork.positionWork' => function($query) {
                    $query->orderBy(['id' => SORT_ASC])->limit(1);
                },
                'groupProjectThemesWork.projectThemeWork',
                'participantWork'
            ])
            ->indexBy('id')
            ->all();
    }

    /**
     * Обработка данных одного участника (оптимизированная версия)
     */
    /**
     * Обработка данных одного участника (оптимизированная версия)
     */
    private static function processParticipantData(TrainingGroupParticipantWork $participant, array $services): array
    {
        $trainingGroup = $participant->trainingGroupWork;
        $trainingProgram = $trainingGroup->trainingProgramWork;
        $groupProjectTheme = $participant->groupProjectThemesWork;

        // Быстрое получение связанных данных
        $branch = $services['branches']->get($trainingGroup->branch);
        $focus = $services['focus']->get($trainingProgram->focus);
        $thematicDirection = $services['thematicDirection']->get($trainingProgram->thematic_direction);
        $allowRemote = $services['allowRemote']->get($trainingProgram->allow_remote);
        $projectType = $services['projectType']->get($groupProjectTheme->projectThemeWork->project_type);

        // Учитель - безопасные проверки с учетом возможных null
        $teacher = '';
        if (isset($trainingGroup->teachersWork[0]) &&
            $trainingGroup->teachersWork[0]->teacherWork !== null) {
            $teacher = $trainingGroup->teachersWork[0]->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS);
        }

        // Эксперт - безопасные проверки с учетом возможных null
        $expert = '';
        $expertType = '';
        $expertCompany = '';
        $expertPosition = '';

        if (isset($trainingGroup->expertsWork[0]) &&
            $trainingGroup->expertsWork[0]->expertWork !== null) {

            $expertWork = $trainingGroup->expertsWork[0];
            $expert = $expertWork->expertWork->getFIO(PersonInterface::FIO_FULL);
            $expertType = $expertWork->getExpertTypeString();

            // Данные компании и должности эксперта
            if (isset($expertWork->expertWork->peopleWork->peoplePositionCompanyBranchWork[0])) {
                $positionData = $expertWork->expertWork->peopleWork->peoplePositionCompanyBranchWork[0];
                $expertCompany = $positionData->companyWork->name ?? '';
                $expertPosition = $positionData->positionWork->name ?? '';
            }
        }

        return [
            $participant->participantWork->getFIO(PersonInterface::FIO_FULL),
            $trainingGroup->number,
            $trainingGroup->start_date,
            $trainingGroup->finish_date,
            $branch,
            $participant->participantWork->getSexString(),
            $participant->participantWork->birthdate,
            $services['benefits']->get($participant->participantWork->benefits),
            $focus,
            $teacher,
            $trainingGroup->getBudgetString(),
            $thematicDirection,
            $trainingProgram->name,
            $allowRemote,
            $participant->success,
            $groupProjectTheme->projectThemeWork->name,
            $trainingGroup->protection_date,
            $projectType,
            $expert,
            $expertType,
            $expertCompany,
            $expertPosition,
        ];
    }
    /**
     * Получение сервисов для оптимизации
     */
    private static function getServices(): array
    {
        return [
            'branches' => Yii::$app->branches,
            'focus' => Yii::$app->focus,
            'thematicDirection' => Yii::$app->thematicDirection,
            'allowRemote' => Yii::$app->allowRemote,
            'projectType' => Yii::$app->projectType,
            'benefits' => Yii::$app->benefits,
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
            'Льготы',
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