<?php

namespace backend\tests\unit\report\query;

use backend\forms\report\ManHoursReportForm;
use backend\services\report\mock\ReportForeignEventMockService;
use backend\tests\UnitTester;
use common\components\dictionaries\base\AllowRemoteDictionary;
use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\EventLevelDictionary;
use common\components\dictionaries\base\FocusDictionary;
use frontend\forms\event\ForeignEventForm;
use frontend\models\work\event\ForeignEventWork;
use Throwable;
use Yii;

class ForeignEventReportTest extends \Codeception\Test\Unit
{
    protected ReportForeignEventMockService $foreignEventMockService;

    // Набор ожидаемых значений для теста testForeignEventReport
    private array $expValForeignEventReport;

    /**
     * @var UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        $this->foreignEventMockService = Yii::createObject(
            ReportForeignEventMockService::class
        );
    }

    protected function _after()
    {
    }


    /**
     * @dataProvider getForeignEventReportData
     */
    public function testForeignEventReport(ForeignEventReportData $data)
    {
        $this->foreignEventMockService->setMockData(
            $data->events,
            $data->acts,
            $data->actsBranch,
            $data->achieves
        );

        $this->expValForeignEventReport = $data->expectedValues;

        $tasks = [
            $this->foreignEventMockService->calculateEventParticipants(
                '2010-01-01',
                '2010-12-31',
                [BranchDictionary::TECHNOPARK, BranchDictionary::QUANTORIUM, BranchDictionary::CDNTT, BranchDictionary::COD, BranchDictionary::MOBILE_QUANTUM],
                [FocusDictionary::TECHNICAL, FocusDictionary::SCIENCE, FocusDictionary::ART, FocusDictionary::SPORT],
                [AllowRemoteDictionary::ONLY_PERSONAL, AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
                [
                    EventLevelDictionary::INTERIOR,
                    EventLevelDictionary::DISTRICT,
                    EventLevelDictionary::URBAN,
                    EventLevelDictionary::REGIONAL,
                    EventLevelDictionary::FEDERAL,
                    EventLevelDictionary::INTERNATIONAL
                ]
            ),
            $this->foreignEventMockService->calculateEventParticipants(
                '2010-01-01',
                '2010-12-31',
                [BranchDictionary::TECHNOPARK, BranchDictionary::QUANTORIUM, BranchDictionary::MOBILE_QUANTUM],
                [FocusDictionary::TECHNICAL, FocusDictionary::SCIENCE],
                [AllowRemoteDictionary::ONLY_PERSONAL],
                [
                    EventLevelDictionary::INTERIOR,
                    EventLevelDictionary::DISTRICT,
                    EventLevelDictionary::URBAN,
                    EventLevelDictionary::REGIONAL,
                    EventLevelDictionary::FEDERAL,
                    EventLevelDictionary::INTERNATIONAL
                ]
            ),
            $this->foreignEventMockService->calculateEventParticipants(
                '2010-01-01',
                '2010-01-16',
                [BranchDictionary::TECHNOPARK, BranchDictionary::QUANTORIUM, BranchDictionary::CDNTT, BranchDictionary::COD, BranchDictionary::MOBILE_QUANTUM],
                [FocusDictionary::TECHNICAL, FocusDictionary::SCIENCE, FocusDictionary::ART, FocusDictionary::SPORT],
                [AllowRemoteDictionary::ONLY_PERSONAL, AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
                [
                    EventLevelDictionary::INTERIOR,
                    EventLevelDictionary::DISTRICT,
                    EventLevelDictionary::URBAN,
                    EventLevelDictionary::REGIONAL,
                    EventLevelDictionary::FEDERAL
                ]
            ),
        ];

        foreach ($tasks as $index => $task) {
            foreach ($task['result']['levels'] as $indexLevel => $level) {
                $levelName = (new EventLevelDictionary())->get($indexLevel);
                $this->assertEquals(
                    $this->expValForeignEventReport[$index]['levels'][$indexLevel]['participant'],
                    $level['participant'],
                    "В задаче $index обнаружено несоответствие кол-ва участников в $levelName уровне"
                );
                $this->assertEquals(
                    $this->expValForeignEventReport[$index]['levels'][$indexLevel]['winners'],
                    $level['winners'],
                    "В задаче $index обнаружено несоответствие кол-ва победителей в $levelName уровне"
                );
                $this->assertEquals(
                    $this->expValForeignEventReport[$index]['levels'][$indexLevel]['prizes'],
                    $level['prizes'],
                    "В задаче $index обнаружено несоответствие кол-ва призеров в $levelName уровне"
                );
            }
            $this->assertEquals(
                $this->expValForeignEventReport[$index]['percent'],
                $task['result']['percent'],
                "В задаче $index обнаружено несоответствие процентного соотношения"
            );
        }
    }

    public function getForeignEventReportData()
    {
        $data = new ForeignEventReportData();

        return [
            [
                $data
            ],
        ];
    }

}