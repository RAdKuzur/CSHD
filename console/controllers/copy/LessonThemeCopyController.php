<?php

namespace console\controllers\copy;

use common\services\general\PeopleStampService;
use Yii;
use yii\console\Controller;

class LessonThemeCopyController extends Controller
{
    private PeopleStampService $peopleStampService;
    public function __construct(
        $id,
        $module,
        PeopleStampService $peopleStampService,
        $config = []
    )
    {
        $this->peopleStampService = $peopleStampService;
        parent::__construct($id, $module, $config);
    }

    public function actionCopyLessonTheme(){
        ini_set('memory_limit', '1024M');
        $query = Yii::$app->old_db->createCommand("SELECT * FROM lesson_theme");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('lesson_theme',
                [
                    'id' => $record['id'],
                    'training_group_lesson_id' => $record['training_group_lesson_id'],
                    'thematic_plan_id' => NULL, //???
                    'teacher_id' => $record['teacher_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['teacher_id']) : NULL,
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteLessonTheme()
    {
        Yii::$app->db->createCommand()->delete('lesson_theme')->execute();
    }
    public function actionDeleteAll(){
        $this->actionDeleteLessonTheme();
    }
    public function actionCopyAll(){
        $this->actionCopyLessonTheme();
    }
}