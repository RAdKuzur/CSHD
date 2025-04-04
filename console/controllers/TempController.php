<?php

namespace console\controllers;

use backend\forms\report\ManHoursReportForm;
use backend\services\report\ReportManHoursService;
use common\components\dictionaries\base\BranchDictionary;
use common\components\logger\base\LogInterface;
use common\components\logger\LogFactory;
use common\components\logger\search\CrudSearchData;
use common\components\logger\search\MethodSearchData;
use common\components\logger\search\SearchLog;
use common\components\logger\SearchLogFacade;
use common\invokables\ErrorsSender;
use common\repositories\act_participant\SquadParticipantRepository;
use common\services\general\errors\ErrorService;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\team\SquadParticipantWork;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class TempController extends Controller
{
    public function actionCheck()
    {

    }

    public function actionReport()
    {
        /*$service = Yii::createObject(ReportManHoursService::class);
        var_dump($service->calculateManHours(
            '2025-01-01',
            '2025-02-02',
            [BranchDictionary::TECHNOPARK],
            [1, 2, 3, 4, 5],
            [1, 2],
            [0, 1],
            ManHoursReportForm::MAN_HOURS_FAIR
        ));*/

        $service = Yii::createObject(ReportManHoursService::class);
        var_dump($service->calculateParticipantsByPeriod(
            '2025-01-01',
            '2025-02-02',
            [BranchDictionary::TECHNOPARK, BranchDictionary::QUANTORIUM],
            [1, 2, 3, 4, 5],
            [1, 2],
            [0, 1],
            [ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN, ManHoursReportForm::PARTICIPANT_START_IN_FINISH_AFTER, ManHoursReportForm::PARTICIPANT_START_IN_FINISH_IN, ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_AFTER],
            ManHoursReportForm::PARTICIPANTS_ALL,
            [45]
        ));
    }
}