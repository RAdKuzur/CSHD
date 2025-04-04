<?php


namespace common\repositories\providers\training_group;


use common\helpers\files\FilesHelper;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\educational\training_group\DeleteTeachersFromGroupEvent;
use frontend\events\general\FileDeleteEvent;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\ProjectThemeWork;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class TrainingGroupProvider implements TrainingGroupProviderInterface
{
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
        return TrainingGroupWork::find()->where(['id' => $id])->one();
    }

    public function getAll()
    {
        return TrainingGroupWork::find()->all();
    }

    public function getByTeacher($teacherId)
    {
        return TrainingGroupWork::find()
            ->joinWith(['teachersWork teachersWork'])
            ->where(['teachersWork.teacher_id' => $teacherId])
            ->orderBy(['finish_date' => SORT_DESC, 'start_date' => SORT_DESC])
            ->all();
    }

    public function getBetweenDates(string $date1, string $date2, array $groupIds = [])
    {
        $query = TrainingGroupWork::find()
            ->where(['BETWEEN', 'start_date', $date1, $date2])
            ->orWhere(['BETWEEN', 'finish_date', $date1, $date2])
            ->orWhere(['and',
                ['<', 'start_data', $date1],
                ['>', 'finish_date', $date2]
            ]);

        if (count($groupIds) > 0) {
            $query = $this->filterQueryByIds($query, $groupIds);
        }

        return $query->all();
    }

    public function getStartBeforeFinishInDates(string $date1, string $date2, array $groupIds = [])
    {
        $query = TrainingGroupWork::find()
            ->where(['>', 'start_date', $date1])
            ->andWhere(['BETWEEN', 'finish_date', $date1, $date2]);

        if (count($groupIds) > 0) {
            $query = $this->filterQueryByIds($query, $groupIds);
        }

        return $query->all();
    }

    public function getStartInFinishAfterDates(string $date1, string $date2, array $groupIds = [])
    {
        $query = TrainingGroupWork::find()
            ->where(['BETWEEN', 'start_date', $date1, $date2])
            ->andWhere(['<', 'finish_date', $date2]);

        if (count($groupIds) > 0) {
            $query = $this->filterQueryByIds($query, $groupIds);
        }

        return $query->all();
    }

    public function getStartInFinishInDates(string $date1, string $date2, array $groupIds = [])
    {
        $query = TrainingGroupWork::find()
            ->where(['BETWEEN', 'start_date', $date1, $date2])
            ->andWhere(['BETWEEN', 'finish_date', $date1, $date2]);

        if (count($groupIds) > 0) {
            $query = $this->filterQueryByIds($query, $groupIds);
        }

        return $query->all();
    }

    public function getStartBeforeFinishAfterDates(string $date1, string $date2, array $groupIds = [])
    {
        $query = TrainingGroupWork::find()
            ->where(['>', 'start_date', $date1])
            ->andWhere(['<', 'finish_date', $date2]);

        if (count($groupIds) > 0) {
            $query = $this->filterQueryByIds($query, $groupIds);
        }

        return $query->all();
    }

    public function getGroupsForCertificates()
    {
        $date = date("Y-m-d", strtotime('+3 days'));
        return TrainingGroupWork::find()->where(['<=','finish_date', $date])->andWhere(['archive' => 0])->orderBy(['id' => SORT_DESC])->all();
    }

    public function getParticipants($id)
    {
        return TrainingGroupParticipantWork::find()->where(['training_group_id' => $id])->all();
    }

    public function getLessons($id)
    {
        return TrainingGroupLessonWork::find()->where(['training_group_id' => $id])->orderBy(['lesson_date' => SORT_ASC, 'lesson_start_time' => SORT_ASC])->all();
    }

    public function getExperts($id)
    {
        return TrainingGroupExpertWork::find()->where(['training_group_id' => $id])->all();
    }

    public function getThemes($id)
    {
        $groupProjects = GroupProjectThemesWork::find()->where(['training_group_id' => $id])->all();
        return ProjectThemeWork::find()->where(['IN', 'id', ArrayHelper::getColumn($groupProjects, 'project_theme_id')])->all();
    }

    public function save(TrainingGroupWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения учебной группы. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }

    public function getByBranchQuery($branch)
    {
        return TrainingGroupWork::find()->where(['branch' => $branch]);
    }

    public function getByBranch(array $branches)
    {
        return TrainingGroupWork::find()->where(['IN', 'branch', $branches])->all();
    }

    public function delete(TrainingGroupWork $model)
    {
        /** @var TrainingGroupWork $model */
        $model->recordEvent(new DeleteTeachersFromGroupEvent($model->id), get_class($model));

        $photos = $this->filesRepository->get(TrainingGroupWork::tableName(), $model->id, FilesHelper::TYPE_PHOTO);
        $presentations = $this->filesRepository->get(TrainingGroupWork::tableName(), $model->id, FilesHelper::TYPE_PRESENTATION);
        $works = $this->filesRepository->get(TrainingGroupWork::tableName(), $model->id, FilesHelper::TYPE_WORK);

        if (is_array($photos)) {
            foreach ($photos as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($presentations)) {
            foreach ($presentations as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($works)) {
            foreach ($works as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        $model->releaseEvents();

        return $model->delete();
    }

    //--Приватные вспомогательные методы--

    private function filterQueryByIds(ActiveQuery $query, array $groupIds)
    {
        return $query->andWhere(['IN', 'id', $groupIds]);
    }
}