<?php

namespace common\repositories\providers\group_lesson;

use frontend\models\work\educational\training_group\TrainingGroupLessonWork;

interface TrainingGroupLessonProviderInterface
{
    public function get($id);
    public function getLessonsFromGroup($id);
    public function getLessonsByGroupsAndPeriod(array $groupIds,string $startDate, string $endDate): array;
    public function delete(TrainingGroupLessonWork $model);
    public function save(TrainingGroupLessonWork $model);
}