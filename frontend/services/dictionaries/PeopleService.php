<?php

namespace frontend\services\dictionaries;

use common\helpers\DateFormatter;
use common\helpers\StringFormatter;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\event\ParticipantAchievementRepository;
use common\repositories\responsibility\LocalResponsibilityRepository;
use frontend\events\dictionaries\PeoplePositionCompanyBranchEventCreate;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\DocumentIn;
use common\models\scaffold\DocumentOut;
use common\models\scaffold\People;
use common\models\scaffold\Regulation;
use common\models\User;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\document_in_out\DocumentOutRepository;
use common\repositories\general\UserRepository;
use common\repositories\regulation\RegulationRepository;
use common\services\DatabaseServiceInterface;
use DomainException;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\responsibility\LocalResponsibilityWork;
use PHPUnit\Util\Exception;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class PeopleService implements DatabaseServiceInterface
{
    private TrainingGroupRepository $groupRepository;
    private ParticipantAchievementRepository $achievementRepository;
    private LocalResponsibilityRepository $responsibilityRepository;
    private DocumentInRepository $documentInRepository;
    private DocumentOutRepository $documentOutRepository;
    private RegulationRepository $regulationRepository;
    private UserRepository $userRepository;

    public function __construct(
        TrainingGroupRepository $groupRepository,
        ParticipantAchievementRepository $achievementRepository,
        LocalResponsibilityRepository $responsibilityRepository,
        DocumentInRepository $documentInRepository,
        DocumentOutRepository $documentOutRepository,
        RegulationRepository $regulationRepository,
        UserRepository $userRepository
    )
    {
        $this->groupRepository = $groupRepository;
        $this->achievementRepository = $achievementRepository;
        $this->responsibilityRepository = $responsibilityRepository;
        $this->documentInRepository = $documentInRepository;
        $this->documentOutRepository = $documentOutRepository;
        $this->regulationRepository = $regulationRepository;
        $this->userRepository = $userRepository;
    }

    public function createPositionsCompaniesArray(array $data)
    {
        $result = [];
        foreach ($data as $item) {
            /** @var PeoplePositionCompanyBranchWork $item */
            $result[] = $item->companyWork->name . " (" . $item->positionWork->name . ")";
        }

        return $result;
    }

    public function attachPositionCompanyBranch(PeopleWork $model, array $positions, array $companies, array $branches)
    {
        if (!(count($positions) == count($companies) && count($companies) == count($branches))) {
            throw new DomainException('Размеры массивов $positions, $companies и $branches не совпадают');
        }

        for ($i = 0; $i < count($companies); $i++) {
            if ($positions[$i] !== "" && $companies[$i] !== "") {
                $model->recordEvent(new PeoplePositionCompanyBranchEventCreate($model->id, (int)$positions[$i],
                    (int)$companies[$i], (int)$branches[$i]),
                    PeoplePositionCompanyBranchWork::class);
            }
        }
    }

    public function getGroupsList(PeopleWork $model)
    {
        $groups = $this->groupRepository->getByTeacher($model->id);

        return implode('<br>', array_map(function (TrainingGroupWork $group) {
            return StringFormatter::stringAsLink(
                $group->number,
                Url::to(['/educational/training-group/view', 'id' => $group->id])
            );
        }, $groups));
    }

    public function getStudentAchievements(PeopleWork $model)
    {
        $result = '';
        $achievements = $this->achievementRepository->getByTeacherId($model->id);
        foreach ($achievements as $achievement) {
            /** @var ParticipantAchievementWork $achievement */
            $participants = $achievement->actParticipantWork->squadParticipantsWork;
            foreach ($participants as $participant) {
                $result .=
                    StringFormatter::stringAsLink(
                        $participant->participantWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS),
                        Url::to(['/dictionaries/foreign-event-participants/view', 'id' => $participant->id])
                    ) . ' &mdash; ' .
                    $achievement->achievement . ' ' .
                    StringFormatter::stringAsLink(
                        $achievement->actParticipantWork->foreignEventWork->name,
                        Url::to(['/event/foreign-event/view', 'id' => $achievement->actParticipantWork->foreign_event_id])
                    ) . ' (' .
                    DateFormatter::format(
                        $achievement->actParticipantWork->foreignEventWork->begin_date,
                        DateFormatter::Ymd_dash,
                        DateFormatter::dmY_dot
                    ) . ')<br>';
            }
        }

        return $result;
    }

    public function getResponsibilities(PeopleWork $model)
    {
        $responsibilities = $this->responsibilityRepository->getPeopleResponsibilities($model->id);
        return implode('<br>', array_map(function (LocalResponsibilityWork $responsible) {
            return StringFormatter::stringAsLink(
                Yii::$app->responsibilityType->get($responsible->responsibility_type) . ' ' .
                Yii::$app->branches->get($responsible->branch) . '  ' .
                $responsible->auditoriumWork->name,
                Url::to(['/responsibility/local-responsibility/view', 'id' => $responsible->id])
            );
        }, $responsibilities));
    }

    /**
     * Возвращает список ошибок, если список пуст - проблем нет
     * @param $entityId
     * @return array
     */
    public function isAvailableDelete($entityId)
    {
        $docsIn = $this->documentInRepository->checkDeleteAvailable(DocumentIn::tableName(), People::tableName(), $entityId);
        $docsOut = $this->documentOutRepository->checkDeleteAvailable(DocumentOut::tableName(), People::tableName(), $entityId);
        $regulation = $this->regulationRepository->checkDeleteAvailable(Regulation::tableName(), People::tableName(), $entityId);
        $user = $this->userRepository->checkDeleteAvailable(User::tableName(), People::tableName(), $entityId);

        return array_merge($docsIn, $docsOut, $regulation, $user);
    }

    public function getPositionCompanyBranchTable(PeopleRepository $repository, int $modelId)
    {
        $branchNames = Yii::$app->branches->getList();
        $positionCompanyBranches = $repository->getPositionsCompanies($modelId);
        return HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Организация'], ArrayHelper::getColumn($positionCompanyBranches, 'companyWork.name')),
                array_merge(['Должность'], ArrayHelper::getColumn($positionCompanyBranches, 'positionWork.name')),
                array_merge(['Отдел (при наличии)'], array_map(function ($number) use ($branchNames) {
                    return $branchNames[$number] ?? null;
                }, ArrayHelper::getColumn($positionCompanyBranches, 'branch'))),
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-position'),
                    ['id' => ArrayHelper::getColumn($positionCompanyBranches, 'id'), 'modelId' => array_fill(0, count($positionCompanyBranches), $modelId)])
            ]
        );
    }
}