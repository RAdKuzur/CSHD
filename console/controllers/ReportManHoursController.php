<?php

namespace console\controllers;

use backend\builders\ParticipantReportBuilder;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;
use frontend\invokables\CalculateAttendance;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\console\Controller;

class ReportManHoursController extends Controller
{
    const START_DATE = '2024-01-01';
    const END_DATE = '2024-12-31';
    private VisitRepository $visitRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private TrainingGroupLessonRepository $lessonRepository;
    public function __construct(
        $id,
        $module,
        VisitRepository $visitRepository,
        TrainingGroupParticipantRepository $participantRepository,
        TrainingGroupLessonRepository $lessonRepository,
        $config = []
    )
    {
        $this->visitRepository = $visitRepository;
        $this->participantRepository = $participantRepository;
        $this->lessonRepository = $lessonRepository;
        parent::__construct($id, $module, $config);
    }


    public function actionHours(){
        /* @var TrainingGroupWork $group */
        $branches = Yii::$app->branches->getList();
        $focuses = Yii::$app->focus->getList();
        foreach ($branches as $branch){
            foreach ($focuses as $focus){
                $indexBranch = array_search($branch, $branches);
                $focusBranch = array_search($focus, $focuses);
                $allGroups = TrainingGroupWork::find()
                    ->joinWith('trainingProgram') // Убедитесь, что связь `trainingProgram` определена в модели
                    ->where([
                        'and',
                        ['<=', 'start_date', self::END_DATE],
                        ['>=', 'finish_date', self::START_DATE],
                        ['branch' => $indexBranch],
                        ['budget' => TrainingGroupWork::IS_BUDGET],
                        ['training_program.focus' => $focusBranch] // Теперь `trainingProgram` доступна благодаря joinWith
                    ])
                    ->all();
                $manHours = 0;
                $visitHours = 0;
                foreach ($allGroups as $group){
                    $manHours = $manHours +
                        count($this->lessonRepository->getLessonsFromGroup($group->id)) *
                        count($this->participantRepository->getParticipantsFromGroups([$group->id]));
                    $visitHours = $visitHours + (new CalculateAttendance(
                        $this->visitRepository->getByTrainingGroup($group->id),
                        $this->lessonRepository
                    ))();
                }
                if (count($allGroups) != 0){
                    echo $branch . ' ' . $focus .":  " . count($allGroups) ."  :  " . $visitHours . '/' . $manHours . "\n";
                }
            }
        }
    }
}