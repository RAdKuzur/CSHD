<?php

namespace backend\services\report\form;

use backend\builders\GroupParticipantReportBuilder;
use backend\builders\ParticipantReportBuilder;
use backend\builders\TrainingGroupReportBuilder;
use backend\forms\report\ManHoursReportForm;
use backend\forms\report\StatsReportForm;
use common\components\dictionaries\base\EventLevelDictionary;
use common\helpers\common\HeaderWizard;
use common\helpers\files\FilePaths;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\event\ForeignEventRepository;
use frontend\models\work\event\ParticipantAchievementWork;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\helpers\ArrayHelper;

class StatsReportService
{
    const CALCULATE_TYPES = [
        ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN,
        ManHoursReportForm::PARTICIPANT_START_IN_FINISH_AFTER,
        ManHoursReportForm::PARTICIPANT_START_IN_FINISH_IN,
        ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_AFTER
    ];
    const EVENT_LEVELS = [
        EventLevelDictionary::URBAN,
        EventLevelDictionary::REGIONAL,
        EventLevelDictionary::INTERREGIONAL,
        EventLevelDictionary::FEDERAL,
        EventLevelDictionary::INTERNATIONAL
    ];
    const EVENT_LEVELS_CYCLE = [
        EventLevelDictionary::URBAN,
        EventLevelDictionary::REGIONAL,
        EventLevelDictionary::INTERREGIONAL,
        EventLevelDictionary::FEDERAL,
        EventLevelDictionary::INTERNATIONAL
    ];
    private ForeignEventRepository $repository;
    private ParticipantReportBuilder $builder;
    private ActParticipantRepository $actRepository;
    private TrainingGroupReportBuilder $groupBuilder;
    private TrainingGroupRepository $groupRepository;
    private GroupParticipantReportBuilder $participantBuilder;

    public function __construct(
        ForeignEventRepository   $repository,
        ParticipantReportBuilder $builder,
        ActParticipantRepository $actRepository,
        TrainingGroupReportBuilder $groupBuilder,
        TrainingGroupRepository $groupRepository,
        GroupParticipantReportBuilder $participantBuilder
    )
    {
        $this->repository = $repository;
        $this->builder = $builder;
        $this->actRepository = $actRepository;
        $this->groupBuilder = $groupBuilder;
        $this->groupRepository = $groupRepository;
        $this->participantBuilder = $participantBuilder;
    }
    public function statsReport($year) {
        //1.8 и 1.9
        $totalStudents = $this->totalParticipants($year);
        $data_1_8 = $this->section_1_8($year);
        $data_1_9 = $this->section_1_9($year);
        $data = [
            '1.8' => $data_1_8['all'],
            '1.8.1' => $data_1_8[EventLevelDictionary::URBAN] . "/" . round($data_1_8[EventLevelDictionary::URBAN] / $totalStudents * 100),
            '1.8.2' => $data_1_8[EventLevelDictionary::REGIONAL] . "/" . round($data_1_8[EventLevelDictionary::REGIONAL] / $totalStudents * 100),
            '1.8.3' => $data_1_8[EventLevelDictionary::INTERREGIONAL] . "/" . round($data_1_8[EventLevelDictionary::INTERREGIONAL] / $totalStudents * 100),
            '1.8.4' => $data_1_8[EventLevelDictionary::FEDERAL] . "/" . round($data_1_8[EventLevelDictionary::FEDERAL] / $totalStudents * 100),
            '1.8.5' => $data_1_8[EventLevelDictionary::INTERNATIONAL] . "/" . round($data_1_8[EventLevelDictionary::INTERNATIONAL] / $totalStudents * 100),

            '1.9' => $data_1_9['all'],
            '1.9.1' => $data_1_9[EventLevelDictionary::URBAN] . "/" . round($data_1_9[EventLevelDictionary::URBAN] / $totalStudents * 100),
            '1.9.2' => $data_1_9[EventLevelDictionary::REGIONAL] . "/" . round($data_1_9[EventLevelDictionary::REGIONAL] / $totalStudents * 100),
            '1.9.3' => $data_1_9[EventLevelDictionary::INTERREGIONAL] . "/" . round($data_1_9[EventLevelDictionary::INTERREGIONAL] / $totalStudents * 100),
            '1.9.4' => $data_1_9[EventLevelDictionary::FEDERAL] . "/" . round($data_1_9[EventLevelDictionary::FEDERAL] / $totalStudents * 100),
            '1.9.5' => $data_1_9[EventLevelDictionary::INTERNATIONAL] . "/" . round($data_1_9[EventLevelDictionary::INTERNATIONAL] / $totalStudents * 100),
            'totalStudents' => $totalStudents
        ];
        return $data;
    }
    public function section_1_8($year)
    {
        $data = [];
        $events = $this->repository->getByDatesAndLevelsEndDate("$year-01-01", "$year-12-31", self::EVENT_LEVELS);
        $actsQuery = $this->builder->query();
        $actsQuery = $this->builder->filterByEvents($actsQuery, ArrayHelper::getColumn($events, 'id'));
        $actsQuery = $this->builder->joinWith($actsQuery, 'foreignEventWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'actParticipantBranchWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'participantAchievementWork');
        $totalParticipants = [];
        //1.8.*
        foreach (self::EVENT_LEVELS_CYCLE as $level) {
            $participants = [];
            if ($level == EventLevelDictionary::FEDERAL) {
                $queryLevels = [$level];
            } else {
                $queryLevels = [$level];
            }
            $participantQuery = $this->builder->filterByEventLevels(clone $actsQuery, $queryLevels);
            foreach ($this->actRepository->findAll($participantQuery) as $actParticipant) {
                foreach ($actParticipant->squadParticipant as $participant) {
                    $participants[] = $participant->participant_id;
                    $totalParticipants[] = $participant->participant_id;
                }
            }
            $data[$level] = count(array_unique($participants));

        }
        $data['all'] = count(array_unique($totalParticipants));
        return $data;
    }
    public function section_1_9($year)
    {
        $data = [];
        $events = $this->repository->getByDatesAndLevelsEndDate("$year-01-01", "$year-12-31", self::EVENT_LEVELS);
        $actsQuery = $this->builder->query();
        $actsQuery = $this->builder->filterByEvents($actsQuery, ArrayHelper::getColumn($events, 'id'));
        $actsQuery = $this->builder->joinWith($actsQuery, 'foreignEventWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'actParticipantBranchWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'participantAchievementWork');
        $totalParticipants = [];
        //1.9.*
        foreach (self::EVENT_LEVELS_CYCLE as $level) {
            $participants = [];
            if ($level == EventLevelDictionary::FEDERAL) {
                $queryLevels = [$level];
            } else {
                $queryLevels = [$level];
            }
            $participantQuery = $this->builder->filterByEventLevels(clone $actsQuery, $queryLevels);
            $prizeQuery = $this->builder->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_PRIZE]);
            $winQuery = $this->builder->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_WINNER]);
            foreach ($this->actRepository->findAll($winQuery) as $actParticipant) {
                foreach ($actParticipant->squadParticipant as $participant) {
                    $participants[] = $participant->participant_id;
                    $totalParticipants[] = $participant->participant_id;
                }
            }
            foreach ($this->actRepository->findAll($prizeQuery) as $actParticipant) {
                foreach ($actParticipant->squadParticipant as $participant) {
                    $participants[] = $participant->participant_id;
                    $totalParticipants[] = $participant->participant_id;
                }
            }
            $data[$level] = count(array_unique($participants));

        }
        $data['all'] = count(array_unique($totalParticipants));
        return $data;
    }

    public function totalParticipants($year)
    {
        $groupsQuery = $this->groupBuilder->query();
        $groupsQuery = $this->groupBuilder->filterGroupsByDates($groupsQuery, "$year-01-01", "$year-12-31", self::CALCULATE_TYPES);
        $groupsQuery = $this->groupBuilder->filterGroupsByBudget($groupsQuery, [ 0, 1, 2, 3]);
        $groups = $this->groupRepository->findAll($groupsQuery);

        $participants = $this->participantBuilder->query();
        $participants = $this->participantBuilder->filterByGroups($participants, ArrayHelper::getColumn($groups, 'id'));
        $participantsAllUnic = $this->participantBuilder->distinct(clone $participants, ['participant_id']);
        return count($participantsAllUnic->all());
    }
    public function createExcelVariantReport(StatsReportForm $model, $data) {
        $inputData = IOFactory::load(Yii::$app->basePath . FilePaths::REPORT_TEMPLATES . 'stats.xlsx');
        $inputData->getActiveSheet()->setCellValue('D22', $data['1.8']); //1.8
        $inputData->getActiveSheet()->setCellValue('D23', $data['1.8.1']); //1.8.1
        $inputData->getActiveSheet()->setCellValue('D24', $data['1.8.2']); //1.8.2
        $inputData->getActiveSheet()->setCellValue('D25', $data['1.8.3']); //1.8.3
        $inputData->getActiveSheet()->setCellValue('D26', $data['1.8.4']); //1.8.4
        $inputData->getActiveSheet()->setCellValue('D27', $data['1.8.5']); //1.8.5
        $inputData->getActiveSheet()->setCellValue('D28', $data['1.9']); //1.9
        $inputData->getActiveSheet()->setCellValue('D29', $data['1.9.1']); //1.9.1
        $inputData->getActiveSheet()->setCellValue('D30', $data['1.9.2']); //1.9.2
        $inputData->getActiveSheet()->setCellValue('D31', $data['1.9.3']); //1.9.3
        $inputData->getActiveSheet()->setCellValue('D32', $data['1.9.4']); //1.9.4
        $inputData->getActiveSheet()->setCellValue('D33', $data['1.9.5']); //1.9.5
        HeaderWizard::setExcelLoadHeaders('ПОКАЗАТЕЛИ ДЕЯТЕЛЬНОСТИ ОРГАНИЗАЦИИ ДОПОЛНИТЕЛЬНОГО ОБРАЗОВАНИЯ, ПОДЛЕЖАЩЕЙ САМООБСЛЕДОВАНИЮ.xlsx');
        $writer = new Xlsx($inputData);
        $writer->save('php://output');
        exit;
    }
}