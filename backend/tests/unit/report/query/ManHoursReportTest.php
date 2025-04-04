<?php

namespace backend\tests\unit\report\query;

use backend\forms\report\ManHoursReportForm;
use backend\services\report\mock\ReportManHoursMockService;
use backend\services\report\ReportFacade;
use backend\tests\UnitTester;
use common\components\dictionaries\base\AllowRemoteDictionary;
use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\FocusDictionary;
use Yii;

class ManHoursReportTest extends \Codeception\Test\Unit
{
    protected ReportManHoursMockService $manHoursMockService;

    // Набор ожидаемых значений для теста testManHoursReport
    private array $expValManHoursReport = [48, 33, 15, 24, 12, 6, 29];

    /**
     * @var UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        $this->manHoursMockService = Yii::createObject(
            ReportManHoursMockService::class
        );
    }

    protected function _after()
    {
    }


    /**
     * @dataProvider getManHoursReportData
     */
    public function testManHoursReport(ManHoursReportData $data)
    {
        $this->manHoursMockService->setMockData(
            $data->groups,
            $data->participants,
            $data->themes,
            $data->lessons,
            $data->visits
        );

        $tasks = [
            $this->manHoursMockService->calculateManHours(
                '2010-01-01',
                '2010-12-31',
                [BranchDictionary::TECHNOPARK, BranchDictionary::QUANTORIUM],
                [FocusDictionary::TECHNICAL, FocusDictionary::SCIENCE],
                [AllowRemoteDictionary::ONLY_PERSONAL, AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
                [0, 1],
                ManHoursReportForm::MAN_HOURS_ALL
            ),

            $this->manHoursMockService->calculateManHours(
                '2010-01-01',
                '2010-03-31',
                [BranchDictionary::TECHNOPARK, BranchDictionary::QUANTORIUM],
                [FocusDictionary::TECHNICAL, FocusDictionary::SCIENCE],
                [AllowRemoteDictionary::ONLY_PERSONAL, AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
                [0, 1],
                ManHoursReportForm::MAN_HOURS_ALL
            ),

            $this->manHoursMockService->calculateManHours(
                '2010-03-31',
                '2010-12-31',
                [BranchDictionary::TECHNOPARK, BranchDictionary::QUANTORIUM],
                [FocusDictionary::TECHNICAL, FocusDictionary::SCIENCE],
                [AllowRemoteDictionary::ONLY_PERSONAL, AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
                [0, 1],
                ManHoursReportForm::MAN_HOURS_ALL
            ),

            $this->manHoursMockService->calculateManHours(
                '2010-01-01',
                '2010-12-31',
                [BranchDictionary::QUANTORIUM],
                [FocusDictionary::TECHNICAL, FocusDictionary::SCIENCE],
                [AllowRemoteDictionary::ONLY_PERSONAL, AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
                [0, 1],
                ManHoursReportForm::MAN_HOURS_ALL
            ),

            $this->manHoursMockService->calculateManHours(
                '2010-01-01',
                '2010-12-31',
                [BranchDictionary::QUANTORIUM],
                [FocusDictionary::TECHNICAL],
                [AllowRemoteDictionary::ONLY_PERSONAL, AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
                [0, 1],
                ManHoursReportForm::MAN_HOURS_ALL
            ),

            $this->manHoursMockService->calculateManHours(
                '2010-01-01',
                '2010-12-31',
                [BranchDictionary::QUANTORIUM],
                [FocusDictionary::TECHNICAL],
                [AllowRemoteDictionary::ONLY_PERSONAL],
                [0, 1],
                ManHoursReportForm::MAN_HOURS_ALL
            ),

            $this->manHoursMockService->calculateManHours(
                '2010-01-01',
                '2010-12-31',
                [BranchDictionary::TECHNOPARK, BranchDictionary::QUANTORIUM],
                [FocusDictionary::TECHNICAL, FocusDictionary::SCIENCE],
                [AllowRemoteDictionary::ONLY_PERSONAL, AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
                [0, 1],
                ManHoursReportForm::MAN_HOURS_FAIR
            )
        ];

        foreach ($tasks as $index => $task) {
            $this->assertEquals(
                $this->expValManHoursReport[$index],
                $task['result'],
                "В задаче $index обнаружено несоответствие ожидаемого и полученного значений"
            );
        }
    }

    public function getManHoursReportData()
    {
        $data = new ManHoursReportData();

        return [
            [
                $data
            ],
        ];
    }

}