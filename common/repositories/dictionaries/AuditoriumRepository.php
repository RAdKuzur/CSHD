<?php

namespace common\repositories\dictionaries;

use common\helpers\files\FilesHelper;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\general\FileDeleteEvent;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\general\FilesWork;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class AuditoriumRepository
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

    public function findAll(ActiveQuery $query)
    {
        return $query->all();
    }

    public function findOne(ActiveQuery $query)
    {
        return $query->one();
    }

    public function get($id)
    {
        return AuditoriumWork::find()->where(['id' => $id])->one();
    }

    public function getAll()
    {
        return AuditoriumWork::find()->all();
    }

    public function getByBranch($branch)
    {
        return AuditoriumWork::find()->where(['branch' => $branch])->all();
    }

    public function delete(ActiveRecord $model)
    {
        /** @var AuditoriumWork $model */
        $other = $this->filesRepository->get(AuditoriumWork::tableName(), $model->id, FilesHelper::TYPE_OTHER);

        if (is_array($other)) {
            foreach ($other as $file) {
                /** @var FilesWork $file */
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        $model->releaseEvents();

        return $model->delete();
    }

    public function save(AuditoriumWork $aud)
    {
        if (!$aud->save()) {
            throw new DomainException('Ошибка создания помещения. Проблемы: '.json_encode($aud->getErrors()));
        }

        return $aud->id;
    }
}