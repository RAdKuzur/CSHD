<?php

namespace common\repositories\event;

use common\helpers\files\FilesHelper;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\event\DeleteEventBranchEvent;
use frontend\events\event\DeleteEventScopeEvent;
use frontend\events\general\FileDeleteEvent;
use frontend\models\work\event\EventBranchWork;
use frontend\models\work\event\EventScopeWork;
use frontend\models\work\event\EventWork;
use Yii;

class EventRepository
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
        return EventWork::find()->where(['id' => $id])->one();
    }

    public function getAll()
    {
        return EventWork::find()->all();
    }

    public function getBranches($id)
    {
        return EventBranchWork::find()->where(['event_id' => $id])->orderBy(['branch' => SORT_ASC])->all();
    }

    public function getScopes($id)
    {
        return EventScopeWork::find()->where(['event_id' => $id])->orderBy(['participation_scope' => SORT_ASC])->all();
    }

    public function getEventsByBranches(array $branches)
    {
        return EventWork::find()
            ->joinWith(['eventBranchWork eventBranchWork'])
            ->where(['IN', 'eventBranchWork.branch', $branches])
            ->all();
    }

    public function prepareResetBranches($eventId)
    {
        $branches = $this->getBranches($eventId);
        $commands = [];
        foreach ($branches as $branch) {
            $command = Yii::$app->db->createCommand();
            $command->delete(EventBranchWork::tableName(), ['id' => $branch->id]);
            $commands[] = $command->getRawSql();
        }

        return $commands;
    }

    public function prepareConnectBranches($eventId, $branches)
    {
        $commands = [];
        foreach ($branches as $branch) {
            $model = EventBranchWork::fill($eventId, $branch);
            $command = Yii::$app->db->createCommand();
            $command->insert($model::tableName(), $model->getAttributes());
            $commands[] = $command->getRawSql();
        }

        return $commands;
    }

    public function prepareResetScopes($eventId)
    {
        $scopes = $this->getScopes($eventId);
        $commands = [];
        foreach ($scopes as $scope) {
            $command = Yii::$app->db->createCommand();
            $command->delete(EventScopeWork::tableName(), ['id' => $scope->id]);
            $commands[] = $command->getRawSql();
        }

        return $commands;
    }

    public function prepareConnectScopes($eventId, $scopes)
    {
        $commands = [];
        foreach ($scopes as $scope) {
            $model = EventScopeWork::fill($eventId, $scope);
            $command = Yii::$app->db->createCommand();
            $command->insert($model::tableName(), $model->getAttributes());
            $commands[] = $command->getRawSql();
        }

        return $commands;
    }

    public function getEventNumber($object)
    {
        if ($object->id !== null)
            return $object->id;
        $events = EventWork::find()->orderBy(['id' => SORT_DESC])->all();
        return $events[0]->id + 1;
    }

    public function save(EventWork $event)
    {
        if (!$event->save()) {
            throw new DomainException('Ошибка сохранения положения. Проблемы: '.json_encode($event->getErrors()));
        }

        return $event->id;
    }

    public function delete(EventWork $model)
    {
        /** @var EventWork $model */
        $model->recordEvent(new DeleteEventBranchEvent($model->id), get_class($model));
        $model->recordEvent(new DeleteEventScopeEvent($model->id), get_class($model));

        $protocol = $this->filesRepository->get(EventWork::tableName(), $model->id, FilesHelper::TYPE_PROTOCOL);
        $photo = $this->filesRepository->get(EventWork::tableName(), $model->id, FilesHelper::TYPE_PHOTO);
        $reporting = $this->filesRepository->get(EventWork::tableName(), $model->id, FilesHelper::TYPE_REPORT);
        $other = $this->filesRepository->get(EventWork::tableName(), $model->id, FilesHelper::TYPE_OTHER);

        if (is_array($protocol)) {
            foreach ($protocol as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($photo)) {
            foreach ($photo as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($reporting)) {
            foreach ($reporting as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($other)) {
            foreach ($other as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        $model->releaseEvents();

        return $model->delete();
    }
}