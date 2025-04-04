<?php

namespace common\repositories\regulation;

use common\components\traits\CommonDatabaseFunctions;
use common\helpers\files\FilesHelper;
use common\helpers\SortHelper;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\general\FileDeleteEvent;
use frontend\models\work\general\FilesWork;
use frontend\models\work\regulation\RegulationWork;
use Yii;
use yii\db\ActiveRecord;

class RegulationRepository
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

    /**
     * @param $id
     * @return \yii\db\ActiveRecord|null
     */
    public function get($id)
    {
        return RegulationWork::find()->where(['id' => $id])->one();
    }

    public function getOrderedList(int $orderedType = SortHelper::ORDER_TYPE_ID, int $orderDirection = SORT_DESC, $baseQuery = null)
    {
        $query = $baseQuery ?: RegulationWork::find();
        if (SortHelper::orderedAvailable(Yii::createObject(RegulationWork::class), $orderedType, $orderDirection)) {
            switch ($orderedType) {
                case SortHelper::ORDER_TYPE_ID:
                    $query->orderBy(['id' => $orderDirection]);
                    break;
                default:
                    throw new DomainException('Что-то пошло не так');
            }
        }
        else {
            throw new DomainException('Невозможно произвести сортировку по таблице ' . RegulationWork::tableName());
        }

        return $query->all();
    }

    public function save(RegulationWork $regulation)
    {
        if (!$regulation->save()) {
            throw new DomainException('Ошибка сохранения положения. Проблемы: '.json_encode($regulation->getErrors()));
        }

        return $regulation->id;
    }

    public function delete(ActiveRecord $model)
    {
        /** @var RegulationWork $model */
        $scan = $this->filesRepository->get(RegulationWork::tableName(), $model->id, FilesHelper::TYPE_SCAN);

        if (is_array($scan)) {
            foreach ($scan as $file) {
                /** @var FilesWork $file */
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        $model->releaseEvents();

        return $model->delete();
    }

    public function getAll()
    {
        return RegulationWork::find()->all();
    }
}