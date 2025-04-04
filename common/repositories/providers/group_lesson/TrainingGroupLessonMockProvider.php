<?php

namespace common\repositories\providers\group_lesson;

use frontend\models\mock\TrainingGroupLessonMock;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;

class TrainingGroupLessonMockProvider implements TrainingGroupLessonProviderInterface
{
    /** @var TrainingGroupLessonMock[] $data  */
    private array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function get($id)
    {
        return $this->data[$id] ?? null;
    }

    public function getAll()
    {
        return $this->data;
    }

    public function getByIds($ids)
    {
        return array_filter($this->data, function($key) use ($ids) {
            return in_array($key, $ids);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getLessonsFromGroup($id)
    {
        return array_filter($this->data, function($item) use ($id) {
            return $item['training_group_id'] === $id;
        });
    }

    public function delete(TrainingGroupLessonWork $model)
    {
        unset($this->data[$model->id]);
        return true;
    }

    public function save(TrainingGroupLessonWork $model)
    {
        $model->id = count($this->data);
        $this->data[] = $model;
        return $model->id;
    }

    /**
     * Конвертер данных из базового ассоциативного массива
     * @param array $data ассоциативный массив изначальных данных
     * @param string[] $fields свойства (поля) для заполнения
     * @return TrainingGroupLessonMock[]
     */
    public static function convert(array $data, array $fields)
    {
        $result = [];
        foreach ($data as $item) {
            $entity = new TrainingGroupLessonMock();
            foreach ($fields as $field) {
                if (isset($item[$field])) {
                    $entity->$field = $item[$field];
                }
            }
            $result[$item['id']] = $entity;
        }

        return $result;
    }
}