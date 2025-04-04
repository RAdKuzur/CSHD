<?php


namespace frontend\models\work\educational\journal;


use common\helpers\DateFormatter;
use common\Model;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\providers\group_participant\TrainingGroupParticipantProvider;
use common\repositories\providers\group_project_themes\GroupProjectThemesProvider;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use Yii;

class ParticipantLessons extends Model
{
    private TrainingGroupParticipantRepository $repository;
    private GroupProjectThemesRepository $groupProjectThemesRepository;

    public $participant;
    public int $trainingGroupParticipantId;
    /** @var VisitLesson[] $lessonIds */
    public array $lessonIds;

    public ?int $groupProjectThemeId;
    public ?int $points;
    public ?int $successFinishing;

    public ?GroupProjectThemesWork $groupProjectThemesWork;

    public function __construct(
        int $trainingGroupParticipantId,
        array $lessonIds,
        int $groupProjectThemeId = null,
        int $points = null,
        int $successFinishing = null,
        TrainingGroupParticipantRepository $repository = null,
        GroupProjectThemesRepository $groupProjectThemesRepository = null,
        $config = []
    )
    {
        parent::__construct($config);
        $this->trainingGroupParticipantId = $trainingGroupParticipantId;
        $this->lessonIds = $lessonIds;
        $this->groupProjectThemeId = $groupProjectThemeId;
        $this->points = $points;
        $this->successFinishing = $successFinishing;

        if (!$repository) {
            $repository = Yii::createObject(
                TrainingGroupParticipantRepository::class,
                ['provider' => Yii::createObject(TrainingGroupParticipantProvider::class)]
            );
        }

        if (!$groupProjectThemesRepository) {
            $groupProjectThemesRepository = Yii::createObject(
                GroupProjectThemesRepository::class,
                ['provider' => Yii::createObject(GroupProjectThemesProvider::class)]
            );
        }

        /** @var TrainingGroupParticipantRepository $repository */
        $this->repository = $repository;

        /** @var GroupProjectThemesRepository $groupProjectThemesRepository */
        $this->groupProjectThemesRepository = $groupProjectThemesRepository;

        $participantWork = $this->repository->get($this->trainingGroupParticipantId);
        $this->participant = $participantWork ? $participantWork->participantWork : null;
        $this->groupProjectThemesWork = $this->groupProjectThemesRepository->get($this->groupProjectThemeId);
    }

    public function rules()
    {
        return [
            [['groupProjectThemeId', 'points', 'successFinishing'], 'integer']
        ];
    }

    public function sortLessons()
    {
        usort($this->lessonIds, function(VisitLesson $a, VisitLesson $b) {
            $dateComparison = strtotime($a->lesson->lesson_date) <=> strtotime($b->lesson->lesson_date);
            if ($dateComparison === 0) {
                return strtotime($a->lesson->lesson_start_time) <=> strtotime($b->lesson->lesson_start_time);
            }
            return $dateComparison;
        });
    }

    /**
     * Возвращает дату и время занятий в указанном формате
     * @param string $dateFormat
     * @param string $timeDateFormat
     * @return array
     */
    public function getLessonsDate(string $dateFormat = DateFormatter::dm_dot, string $timeDateFormat = DateFormatter::Hi_colon) : array
    {
        $date = [];
        foreach ($this->lessonIds as $oneLesson) {
            $date[] = DateFormatter::format($oneLesson->lesson->lesson_date, DateFormatter::Ymd_dash, $dateFormat)
                        . '<br><span class="fnt-wght-4">' . DateFormatter::format($oneLesson->lesson->lesson_start_time, DateFormatter::His_colon, $timeDateFormat) .  '</span>';
        }
        return $date;
    }

    /**
     * Возвращает количество занятий
     * @return int
     */
    public function getLessonsCount()
    {
        return count($this->lessonIds);
    }
}