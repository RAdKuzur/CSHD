<?php

namespace frontend\services\act_participant;

use common\helpers\ErrorAssociationHelper;
use common\repositories\general\PeopleStampRepository;
use common\services\general\PeopleStampService;
use frontend\models\work\event\ForeignEventWork;
use frontend\services\act_participant\ActParticipantBranchService;
use frontend\services\act_participant\SquadParticipantService;
use frontend\events\act_participant\ActParticipantCreateEvent;
use frontend\models\work\team\ActParticipantWork;
use frontend\services\team\TeamService;
use common\helpers\files\filenames\ActParticipantFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\ActParticipant;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\act_participant\SquadParticipantRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\team\TeamRepository;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\models\forms\ActParticipantForm;
use frontend\models\work\general\FilesWork;
use PHPUnit\Util\Xml\ValidationResult;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

class ActParticipantService
{
    public TeamRepository $teamRepository;
    public TeamService $teamService;
    private ActParticipantFileNameGenerator $filenameGenerator;
    private ActParticipantRepository $actParticipantRepository;
    private FileService $fileService;
    private SquadParticipantService $squadParticipantService;
    private SquadParticipantRepository $squadParticipantRepository;
    private ActParticipantBranchService $actParticipantBranchService;
    private PeopleStampService $peopleStampService;

    public function __construct(
        TeamRepository $teamRepository,
        TeamService $teamService,
        ActParticipantFileNameGenerator $filenameGenerator,
        ActParticipantRepository $actParticipantRepository,
        FileService $fileService,
        SquadParticipantService $squadParticipantService,
        SquadParticipantRepository $squadParticipantRepository,
        ActParticipantBranchService $actParticipantBranchService,
        PeopleStampService $peopleStampService
    )
    {
        $this->teamRepository = $teamRepository;
        $this->teamService = $teamService;
        $this->filenameGenerator = $filenameGenerator;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->fileService = $fileService;
        $this->squadParticipantService = $squadParticipantService;
        $this->squadParticipantRepository = $squadParticipantRepository;
        $this->actParticipantBranchService = $actParticipantBranchService;
        $this->peopleStampService = $peopleStampService;
    }

    public function getFilesInstance(ActParticipantForm $modelActParticipant, $index)
    {
        $modelActParticipant->actFiles = UploadedFile::getInstance($modelActParticipant,  "[{$index}]actFiles");
    }

    public function saveFilesFromModel(ActParticipantWork $model, $index)
    {
        if ($model->actFiles != null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN, ['counter' => $index]);
            $this->fileService->uploadFile(
                $model->actFiles,
                $filename,
                [
                    'tableName' => ActParticipantWork::tableName(),
                    'fileType' => FilesHelper::TYPE_MATERIAL
                ]
            );
            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_MATERIAL,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }
    }

    public function addActParticipant($acts, $foreignEventId){
        $index = 0;

        foreach ($acts as $act){
            if(
                ($act["participant"] != NULL || $act['personalParticipants']) != NULL &&
                $act["nomination"] != NULL &&
                $act["focus"] != NULL &&
                $act["form"] != NULL &&
                ($act["firstTeacher"] != NULL || $act["secondTeacher"] != NULL) &&
                $act["type"] != NULL
            ) {

                if($act["type"] == 0) {
                    $participants = $act['personalParticipants'];
                }
                if($act["type"] == 1) {
                    $participants = $act["participant"];
                }
                $modelActParticipantForm = ActParticipantForm::fill(
                    $participants,
                    $act["firstTeacher"],
                    $act["secondTeacher"],
                    $act["branch"],
                    $act["focus"],
                    $act["type"],
                    NULL,
                    $act["nomination"],
                    $act["form"],
                    $act["team"]
                );
                $modelActParticipantForm->foreignEventId = $foreignEventId;
                $this->getFilesInstance($modelActParticipantForm, $index);
                if ($modelActParticipantForm->type == 1) {
                    $teamNameId = $this->teamService->teamNameCreateEvent($foreignEventId, $act["team"]);
                }
                else {
                    $teamNameId = NULL;
                }
                $modelAct = ActParticipantWork::fill(
                    $modelActParticipantForm->firstTeacher,
                    $modelActParticipantForm->secondTeacher,
                    $teamNameId,
                    $foreignEventId,
                    $modelActParticipantForm->focus,
                    $modelActParticipantForm->type,
                    $modelActParticipantForm->allowRemote,
                    $modelActParticipantForm->nomination,
                    $modelActParticipantForm->form,
                );
                $this->setPeopleStamp($modelAct);
                $modelAct->actFiles = $modelActParticipantForm->actFiles;
                if ($this->actParticipantRepository->checkUniqueAct($foreignEventId, $teamNameId, $modelAct->focus, $modelAct->form, $modelAct->nomination) == 0) {
                    $this->actParticipantRepository->save($modelAct);
                }
                if (!is_null($modelAct->id)) {
                    $this->saveFilesFromModel($modelAct, $index);
                    $modelAct->releaseEvents();
                    $this->squadParticipantService->addSquadParticipantEvent($modelAct, $participants, $modelAct->id);
                    foreach($act["branch"] as $branch){
                        $this->actParticipantBranchService->addActParticipantBranchEvent($modelAct->id, $branch);
                    }
                }
                $index++;
            }
        }

        $this->checkAllActsOnErrors($foreignEventId);
    }

    public function setPeopleStamp(ActParticipantWork $model)
    {
        if ($model->teacher_id != ""){
            $model->teacher_id = $this->peopleStampService->createStampFromPeople($model->teacher_id);
        }
        if ($model->teacher2_id != ""){
            $model->teacher2_id = $this->peopleStampService->createStampFromPeople($model->teacher2_id);
        }
    }

    public function getPeopleStamp(ActParticipantWork $model)
    {
        $model->teacher_id = $model->teacher->people_id;
        $model->teacher2_id = $model->teacher2->people_id;
    }

    public function updateSquadParticipant(ActParticipantWork $model, $participant)
    {
        $this->squadParticipantService->updateSquadParticipantEvent($model, $participant);
    }

    public function createForms($acts)
    {
        /* @var $act ActParticipantWork */
        $forms = [];
        foreach ($acts as $act){
            $participants = $this->getAllPacticipants($act->id);
            $form = ActParticipantForm::fill(
                $participants,
                $act->teacher_id,
                $act->teacher2_id,
                NULL,
                $act->focus,
                $act->type,
                $act->allow_remote,
                $act->nomination,
                $act->form,
                $act->team_name_id
            );
            $form->actId = $act->id;
            $forms[] = $form;
        }
        return $forms;
    }

    public function getAllPacticipants($actId){
        $participants = [];
        $squadParticipants = $this->squadParticipantRepository->getAllByActId($actId);
        foreach($squadParticipants as $squadParticipant){
            $participants[] = $squadParticipant->participant_id;
        }
        return $participants;
    }

    public function createActTable($id)
    {
        $model = $this->actParticipantRepository->getByForeignEventIds([$id]) ;
        return HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['#'], ArrayHelper::getColumn($model, 'id')),
                array_merge(['Тип участия'], ArrayHelper::getColumn($model, 'typeParticipant')),
                array_merge(['Участники'], ArrayHelper::getColumn($model, 'participantString')),
                array_merge(['Учителя'], ArrayHelper::getColumn($model, 'teachers')),
                array_merge(['Направленность'], ArrayHelper::getColumn($model, 'focusName')),
                array_merge(['Отдел'], ArrayHelper::getColumn($model, 'branchName')),
                array_merge(['Номинация'], ArrayHelper::getColumn($model, 'nomination')),
                array_merge(['Команда'], ArrayHelper::getColumn($model, 'teamName')),

            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Редактировать',
                    Url::to('act'),
                    ['id' => ArrayHelper::getColumn($model, 'id')]),
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('act-delete'),
                    ['id' => ArrayHelper::getColumn($model, 'id')]),
            ]
        );
    }

    public function createActFileTable(ActParticipantWork $model)
    {
        $links = $model->getFileLinks(FilesHelper::TYPE_MATERIAL);
        $file = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($links, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($links), $model->foreignEventWork->order_participant_id), 'fileId' => ArrayHelper::getColumn($links, 'id')])
            ]
        );
        return $file;
    }

    public function checkAllActsOnErrors($eventId)
    {
        /** @var ActParticipantWork[] $acts */
        $acts = $this->actParticipantRepository->getByForeignEventIds([$eventId]);
        foreach ($acts as $act) {
            $act->checkModel(ErrorAssociationHelper::getActParticipantErrorsList(), ActParticipantWork::tableName(), $act->id);
        }
    }
}