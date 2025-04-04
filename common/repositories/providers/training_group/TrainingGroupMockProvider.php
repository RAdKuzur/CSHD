<?php

namespace common\repositories\providers\training_group;

use frontend\models\work\educational\training_group\TrainingGroupWork;

class TrainingGroupMockProvider implements TrainingGroupProviderInterface
{
    private array $dataStore = [];
    private array $participantsData = [];
    private array $lessonsData = [];
    private array $expertsData = [];
    private array $themesData = [];

    public function __construct(
        array $dataStore = [],
        array $participantsData = [],
        array $lessonsData = [],
        array $expertsData = [],
        array $themesData = []
    )
    {
        $this->dataStore = $dataStore;
        $this->participantsData = $participantsData;
        $this->lessonsData = $lessonsData;
        $this->expertsData = $expertsData;
        $this->themesData = $themesData;
    }

    public function get($id)
    {
        return $this->dataStore[$id] ?? null;
    }

    public function getAll()
    {
        return $this->dataStore;
    }

    public function getParticipants($id)
    {
        return $this->participantsData[$id] ?? [];
    }

    public function setParticipants(array $participantsData)
    {
        $this->participantsData = $participantsData;
    }

    public function getGroupsForCertificates()
    {
        $date = date("Y-m-d", strtotime('+3 days'));
        return array_filter($this->dataStore, function($item) use ($date) {
            return $item['archive'] === 0 && $item['finish_date'] <= $date;
        });
    }

    public function getLessons($id)
    {
        return $this->lessonsData[$id] ?? [];
    }

    public function setLessons(array $lessonsData)
    {
        $this->lessonsData = $lessonsData;
    }

    public function getExperts($id)
    {
        return $this->expertsData[$id] ?? [];
    }

    public function setExperts(array $expertsData)
    {
        $this->expertsData = $expertsData;
    }

    public function getThemes($id)
    {
        return $this->themesData[$id] ?? [];
    }

    public function setThemes(array $themesData)
    {
        $this->themesData = $themesData;
    }

    public function save(TrainingGroupWork $model)
    {
        $model->id = count($this->dataStore);
        $this->dataStore[] = $model;
        return $model->id;
    }

    public function delete(TrainingGroupWork $model)
    {
        unset($this->dataStore[$model->id]);
        return true;
    }

    public function getBetweenDates(string $date1, string $date2, array $groupIds = [])
    {
        return array_filter($this->dataStore, function($item) use ($date1, $date2, $groupIds) {
            $conditionIds = true;
            if (count($groupIds) > 0) {
                $conditionIds = in_array($item['id'], $groupIds);
            }
            return
                $item['start_date'] >= $date1 && $item['start_date'] <= $date2 ||
                $item['finish_date'] >= $date1 && $item['finish_date'] <= $date2 ||
                $item['start_date'] < $date1 && $item['finish_date'] > $date2 &&
                $conditionIds;
        });
    }

    public function getStartBeforeFinishInDates(string $date1, string $date2, array $groupIds = [])
    {
        return array_filter($this->dataStore, function($item) use ($date1, $date2, $groupIds) {
            $conditionIds = true;
            if (count($groupIds) > 0) {
                $conditionIds = in_array($item['id'], $groupIds);
            }
            return
                $item['start_date'] > $date1 &&
                $item['finish_date'] >= $date1 && $item['finish_date'] <= $date2 &&
                $conditionIds;
        });
    }

    public function getStartInFinishAfterDates(string $date1, string $date2, array $groupIds = [])
    {
        return array_filter($this->dataStore, function($item) use ($date1, $date2, $groupIds) {
            $conditionIds = true;
            if (count($groupIds) > 0) {
                $conditionIds = in_array($item['id'], $groupIds);
            }
            return
                $item['start_date'] >= $date1 && $item['start_date'] <= $date2 ||
                $item['finish_date'] < $date2 &&
                $conditionIds;
        });
    }

    public function getStartInFinishInDates(string $date1, string $date2, array $groupIds = [])
    {
        return array_filter($this->dataStore, function($item) use ($date1, $date2, $groupIds) {
            $conditionIds = true;
            if (count($groupIds) > 0) {
                $conditionIds = in_array($item['id'], $groupIds);
            }
            return
                $item['start_date'] >= $date1 && $item['start_date'] <= $date2 &&
                $item['finish_date'] >= $date1 && $item['finish_date'] <= $date2 &&
                $conditionIds;
        });
    }

    public function getStartBeforeFinishAfterDates(string $date1, string $date2, array $groupIds = [])
    {
        return array_filter($this->dataStore, function($item) use ($date1, $date2, $groupIds) {
            $conditionIds = true;
            if (count($groupIds) > 0) {
                $conditionIds = in_array($item['id'], $groupIds);
            }
            return
                $item['start_date'] > $date1 &&
                $item['finish_date'] < $date2 &&
                $conditionIds;
        });
    }
}