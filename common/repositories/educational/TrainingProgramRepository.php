<?php

namespace common\repositories\educational;

use common\components\traits\CommonDatabaseFunctions;
use common\helpers\files\FilesHelper;
use common\models\scaffold\AuthorProgram;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\educational\training_program\DeleteAuthorsEvent;
use frontend\events\educational\training_program\DeleteTrainingProgramBranchEvent;
use frontend\events\educational\training_program\ResetThematicPlanEvent;
use frontend\events\general\FileDeleteEvent;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\AuthorProgramWork;
use frontend\models\work\educational\training_program\BranchProgramWork;
use frontend\models\work\educational\training_program\ThematicPlanWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use Yii;

class TrainingProgramRepository
{
    use CommonDatabaseFunctions;

    private FileService $fileService;
    private FilesRepository $filesRepository;

    public function __construct(
        FileService $fileService,
        FilesRepository $filesRepository
    )
    {
        $this->fileService = $fileService;
        $this->filesRepository = $filesRepository;
    }

    public function get($id)
    {
        return TrainingProgramWork::find()->where(['id' => $id])->one();
    }

    public function getAll()
    {
        return TrainingProgramWork::find()->all();
    }

    public function getAllActual()
    {
        return TrainingProgramWork::find()->where(['actual' => TrainingProgramWork::ACTUAL])->all();
    }

    public function getByFocuses(array $focuses)
    {
        return TrainingProgramWork::find()->where(['IN', 'focus', $focuses])->all();
    }

    public function getByAllowRemotes(array $allowRemotes)
    {
        return TrainingProgramWork::find()->where(['IN', 'allow_remote', $allowRemotes])->all();
    }

    public function getBranches($id)
    {
        return BranchProgramWork::find()->where(['training_program_id' => $id])->orderBy(['branch' => SORT_ASC])->all();
    }

    public function getByBranches(array $branches)
    {
        return TrainingProgramWork::find()
            ->joinWith(['branchProgramWork branchProgramWork'])
            ->where(['IN', 'branchProgramWork.branch', $branches])
            ->all();
    }

    public function prepareResetBranches($eventId)
    {
        $branches = $this->getBranches($eventId);
        $commands = [];
        foreach ($branches as $branch) {
            $command = Yii::$app->db->createCommand();
            $command->delete(BranchProgramWork::tableName(), ['id' => $branch->id]);
            $commands[] = $command->getRawSql();
        }

        return $commands;
    }

    public function prepareConnectBranches($eventId, $branches)
    {
        $commands = [];
        foreach ($branches as $branch) {
            $model = BranchProgramWork::fill($eventId, $branch);
            $command = Yii::$app->db->createCommand();
            $command->insert($model::tableName(), $model->getAttributes());
            $commands[] = $command->getRawSql();
        }

        return $commands;
    }

    public function save(TrainingProgramWork $program)
    {
        if (!$program->save()) {
            throw new DomainException('Ошибка сохранения образовательной программы. Проблемы: '.json_encode($program->getErrors()));
        }
        return $program->id;
    }

    public function getThematicPlan($programId)
    {
        return ThematicPlanWork::find()
            ->where(['training_program_id' => $programId])
            ->orderBy(['id' => SORT_ASC])
            ->all();
    }

    public function getAuthors($programId)
    {
        return AuthorProgramWork::find()->joinWith('authorWork authorWork')->where(['training_program_id' => $programId])->all();
    }

    public function prepareCreateAuthorProgram($programId, $authorId)
    {
        $model = AuthorProgramWork::fill($programId, $authorId);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareCreateTheme($theme, $programId, $controlType)
    {
        $model = ThematicPlanWork::fill($theme, $programId, $controlType);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDeleteTheme($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(ThematicPlanWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }

    public function prepareDeleteAuthor($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(AuthorProgram::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }

    public function deleteTheme($themeId)
    {
        return (ThematicPlanWork::find()->where(['id' => $themeId])->one())->delete();
    }

    public function delete(TrainingProgramWork $model)
    {
        /** @var TrainingProgramWork $model */
        $model->recordEvent(new DeleteTrainingProgramBranchEvent($model->id), get_class($model));
        $model->recordEvent(new ResetThematicPlanEvent($model->id), get_class($model));
        $model->recordEvent(new DeleteAuthorsEvent($model->id), get_class($model));

        $main = $this->filesRepository->get(TrainingProgramWork::tableName(), $model->id, FilesHelper::TYPE_MAIN);
        $doc = $this->filesRepository->get(TrainingProgramWork::tableName(), $model->id, FilesHelper::TYPE_DOC);
        $contract = $this->filesRepository->get(TrainingProgramWork::tableName(), $model->id, FilesHelper::TYPE_CONTRACT);

        if (is_array($main)) {
            foreach ($main as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($doc)) {
            foreach ($doc as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($contract)) {
            foreach ($contract as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        $model->releaseEvents();

        return $model->delete();
    }

    public function getTheme($themeId)
    {
        return ThematicPlanWork::find()->where(['id' => $themeId])->one();
    }

    public function saveTheme(ThematicPlanWork $theme)
    {
        if (!$theme->save()) {
            throw new DomainException('Ошибка сохранения образовательной программы. Проблемы: '.json_encode($theme->getErrors()));
        }
        return $theme->id;
    }

    public function deleteAuthor($id)
    {
        return (AuthorProgramWork::find()->where(['id' => $id])->one())->delete();
    }

    public function getTrainingGroups($programId)
    {
        return TrainingGroupWork::find()->where(['training_program_id' => $programId])->all();
    }
}