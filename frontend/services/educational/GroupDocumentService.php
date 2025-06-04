<?php

namespace frontend\services\educational;

use common\models\scaffold\TrainingGroup;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use frontend\components\creators\ExcelCreator;
use frontend\components\creators\WordCreator;
use frontend\forms\journal\JournalForm;
use frontend\forms\training_group\ProtocolForm;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpWord\PhpWord;
use Vtiful\Kernel\Excel;

class GroupDocumentService
{
    private TrainingGroupExpertRepository $expertRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private TeacherGroupRepository  $teacherRepository;
    private PeopleRepository $peopleRepository;

    public function __construct(
        TrainingGroupExpertRepository $expertRepository,
        TrainingGroupParticipantRepository $participantRepository,
        TeacherGroupRepository  $teacherRepository,
        PeopleRepository $peopleRepository
    )
    {
        $this->expertRepository = $expertRepository;
        $this->participantRepository = $participantRepository;
        $this->teacherRepository = $teacherRepository;
        $this->peopleRepository = $peopleRepository;
    }

    public function generateProtocol(ProtocolForm $form) : PhpWord
    {
        $experts = $this->expertRepository->getExpertsFromGroup($form->group->id, [TrainingGroupExpertWork::TYPE_EXTERNAL]);

        $formParticipants = (array)$form->participants;
        $participants = $this->participantRepository->getByIds($formParticipants);

        $formTeachers = (array)$form->teachers;
        $formBosses = (array)$form->bosses;
        $formResponsible = (array)$form->responsiblepeople;

        $resultIds = array_merge($formTeachers, $formBosses, $formResponsible);
        $resultIds = array_unique($resultIds);

        $resultTeachers = $this->peopleRepository->getByIds($resultIds);
        return WordCreator::createProtocol($form->group, $resultTeachers, $participants, $experts, $form->name);
    }

    public function generateJournal(int $groupId) : Spreadsheet
    {
        return ExcelCreator::createJournal($groupId);
    }
}