<?php

namespace common\repositories\providers\training_group;

use frontend\models\work\educational\training_group\TrainingGroupWork;

interface TrainingGroupProviderInterface
{
    public function get($id);
    public function getBetweenDates(string $date1, string $date2, array $groupIds = []);
    public function getStartBeforeFinishInDates(string $date1, string $date2, array $groupIds = []);
    public function getStartInFinishAfterDates(string $date1, string $date2, array $groupIds = []);
    public function getStartInFinishInDates(string $date1, string $date2, array $groupIds = []);
    public function getStartBeforeFinishAfterDates(string $date1, string $date2, array $groupIds = []);
    public function getParticipants($id);
    public function getLessons($id);
    public function getExperts($id);
    public function getThemes($id);
    public function save(TrainingGroupWork $model);
    public function delete(TrainingGroupWork $model);
}