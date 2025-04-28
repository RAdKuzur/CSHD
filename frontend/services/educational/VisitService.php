<?php


namespace frontend\services\educational;


use common\models\scaffold\Visit;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;
use frontend\models\work\educational\journal\ParticipantLessons;
use frontend\models\work\educational\journal\VisitWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;

class VisitService
{
    private VisitRepository $visitRepository;
    private TrainingGroupLessonRepository $trainingGroupLessonRepository;
    public function __construct(
        VisitRepository $visitRepository,
        TrainingGroupLessonRepository $trainingGroupLessonRepository
    ){
        $this->visitRepository = $visitRepository;
        $this->trainingGroupLessonRepository = $trainingGroupLessonRepository;

    }
    //очищает явки по заданному training_group_participant.id
    public function clearPresence($id, $model){
        /* @var $lesson TrainingGroupLessonWork */
        $visit = $this->visitRepository->getByTrainingGroupParticipant($id);
        $lessons = json_decode($visit->lessons);
        $newLesson = [];
        foreach($lessons as $item){
            $lesson = $this->trainingGroupLessonRepository->get($item->lesson_id);
            if($lesson->lesson_date > $model->order_date){
                $item->status = VisitWork::NONE;
            }
            $newLesson[] = $item;
        }
        $visit->lessons = json_encode($newLesson);
        $this->visitRepository->save($visit);
    }
}