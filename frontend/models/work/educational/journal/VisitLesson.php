<?php


namespace frontend\models\work\educational\journal;

use common\helpers\files\FilePaths;
use common\helpers\html\HtmlBuilder;
use common\Model;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\providers\group_lesson\TrainingGroupLessonProvider;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use InvalidArgumentException;
use Yii;

class VisitLesson extends Model
{
    //private TrainingGroupLessonRepository $repository;

    public int $lessonId;
    public int $status;
    public ?TrainingGroupLessonWork $lesson;

    public function __construct(
        int $lessonId,
        int $status,
        TrainingGroupLessonWork $lesson = null,
        $config = []
    )
    {
        parent::__construct($config);
        $this->lessonId = $lessonId;
        $this->status = $status;
        $this->lesson = $lesson;
    }

    public function rules()
    {
        return [
            [['lessonId', 'status'], 'integer']
        ];
    }

    public function getLessonId()
    {
        return $this->lessonId;
    }

    /**
     * Раскладывает json-строку на массив VisitLesson
     * @param string $json
     * @param TrainingGroupLessonRepository $repository
     * @return VisitLesson[]
     */
    public static function fromString(string $json, TrainingGroupLessonRepository $repository) : array
    {
        $lessonsArray = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException("Invalid JSON string.");
        }

        $visitLessons = [];
        foreach ($lessonsArray as $item) {
            $visitLessons[] = new VisitLesson(
                $item['lesson_id'],
                $item['status'],
                $repository->get($item['lesson_id'])
            );
        }

        return $visitLessons;
    }

    /**
     * Склеивает массив VisitLesson json-строку
     * @param VisitLesson[] $visitLessons
     */
    public static function toString(array $visitLessons)
    {
        $newLessons = [];
        foreach ($visitLessons as $visitLesson) {
            $newLessons[] = (string)$visitLesson;
        }

        return '['.(implode(',', $newLessons)).']';
    }

    /**
     * @param VisitLesson[] $lessons
     * @return int[]
     */
    public static function getLessonIds(array $lessons)
    {
        return array_map(fn($obj) => $obj->getLessonId(), $lessons);
    }

    /**
     * @param VisitLesson[] $lessons
     * @param $lessonId
     * @return false|VisitLesson
     */
    public static function getLesson(array $lessons, $lessonId)
    {
        foreach ($lessons as $lesson) {
            if ($lesson->lessonId == $lessonId) {
                return $lesson;
            }
        }

        return false;
    }

    /**
     * Сравнивает два массива класса VisitLesson
     * @param VisitLesson[] $arr1
     * @param VisitLesson[] $arr2
     */
    public static function equalArrays(array $arr1, array $arr2)
    {
        $lessonIds1 = array_map(fn($lesson) => $lesson->lessonId, $arr1);
        $lessonIds2 = array_map(fn($lesson) => $lesson->lessonId, $arr2);

        $uniqueLessonIds1 = array_unique($lessonIds1);
        $uniqueLessonIds2 = array_unique($lessonIds2);

        return count($uniqueLessonIds1) === count($uniqueLessonIds2) &&
            count(array_intersect($uniqueLessonIds1, $uniqueLessonIds2)) === count($uniqueLessonIds1);
    }

    public function __toString()
    {
        return "{\"lesson_id\":$this->lessonId,\"status\":$this->status}";
    }

    public function getPrettyStatus()
    {
        switch ($this->status) {
            case VisitWork::NONE:
                return HtmlBuilder::createTooltipIcon('--', FilePaths::SVG_DROPPED);
            case VisitWork::ATTENDANCE:
                return HtmlBuilder::createTooltipIcon('Явка', FilePaths::SVG_TURNOUT);
            case VisitWork::NO_ATTENDANCE:
                return HtmlBuilder::createTooltipIcon('Неявка', FilePaths::SVG_NON_APPEARANCE);
            case VisitWork::DISTANCE:
                return HtmlBuilder::createTooltipIcon('Дистант', FilePaths::SVG_DISTANT);
            default:
                return '?';
        }
    }

    /**
     * Функция, определяющая, присутствовал ли на занятии обучающийся
     *
     * @return bool
     */
    public function isPresence() : bool
    {
        return
            $this->status == VisitWork::ATTENDANCE ||
            $this->status == VisitWork::DISTANCE;
    }
}