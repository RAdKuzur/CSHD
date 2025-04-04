<?php

namespace console\controllers\copy;

use common\services\general\PeopleStampService;
use console\helper\FileTransferHelper;
use Yii;
use yii\console\Controller;

class TrainingProgramCopyController extends Controller
{
    private FileTransferHelper $fileTransferHelper;
    private PeopleStampService $peopleStampService;
    public function __construct(
        $id,
        $module,
        FileTransferHelper $fileTransferHelper,
        PeopleStampService $peopleStampService,
        $config = [])
    {
        $this->fileTransferHelper = $fileTransferHelper;
        $this->peopleStampService = $peopleStampService;
        parent::__construct($id, $module, $config);
    }
    public function actionCopyTrainingProgram(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM training_program");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('training_program',
                [
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'thematic_direction' => $record['thematic_direction_id'],
                    'level' => $record['level'],
                    'ped_council_date' => $record['ped_council_date'],
                    'ped_council_number' => $record['ped_council_number'],
                    'author_id' => $record['author_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['author_id']) : NULL,
                    'capacity' => $record['capacity'],
                    'hour_capacity' => $record['hour_capacity'],
                    'student_left_age' => $record['student_left_age'],
                    'student_right_age' => $record['student_right_age'],
                    'focus' => $record['focus_id'],
                    'allow_remote' => $record['allow_remote_id'],
                    'actual' => $record['actual'],
                    'certificate_type' => $record['certificat_type_id'],
                    'description' => $record['description'],
                    'key_words' => $record['key_words'],
                    'is_network' => $record['is_network'],
                    'creator_id' => $record['creator_id'],
                    'last_edit_id' => $record['last_update_id'],
                    //'created_at' => ,
                    //'updated_at' => ,
                ]
            );
            //doc_files, edit_doc
            //$this->fileTransferHelper->createFiles();
            $command->execute();
        }
    }
    public function actionCopyThematicPlan(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM thematic_plan");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('thematic_plan',
                [
                    'id' => $record['id'],
                    'theme' => $record['theme'],
                    'training_program_id' => $record['training_program_id'],
                    'control_type' => $record['control_type_id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionCopyAuthorProgram(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM author_program");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('author_program',
                [
                    'id' => $record['id'],
                    'author_id' => $record['author_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['author_id']) : NULL,
                    'training_program_id' => $record['training_program_id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionCopyBranchProgram(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM branch_program");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('branch_program',
                [
                    'id' => $record['id'],
                    'branch' => $record['branch_id'],
                    'training_program_id' => $record['training_program_id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteTrainingProgram(){
        Yii::$app->db->createCommand()->delete('training_program')->execute();
    }
    public function actionDeleteThematicPlan(){
        Yii::$app->db->createCommand()->delete('thematic_plan')->execute();
    }
    public function actionDeleteBranchProgram()
    {
        Yii::$app->db->createCommand()->delete('branch_program')->execute();
    }
    public function actionDeleteAuthorProgram()
    {
        Yii::$app->db->createCommand()->delete('author_program')->execute();
    }
    public function actionCopyAll(){
        $this->actionCopyTrainingProgram();
        $this->actionCopyThematicPlan();
        $this->actionCopyAuthorProgram();
        $this->actionCopyBranchProgram();
    }

    public function actionDeleteAll()
    {
        $this->actionDeleteAuthorProgram();
        $this->actionDeleteBranchProgram();
        $this->actionDeleteThematicPlan();
        $this->actionDeleteTrainingProgram();
    }
}