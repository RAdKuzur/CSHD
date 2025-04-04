<?php

namespace frontend\services\educational;

use common\repositories\educational\TrainingGroupExpertRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
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

    public function __construct(
        TrainingGroupExpertRepository $expertRepository,
        TrainingGroupParticipantRepository $participantRepository
    )
    {
        $this->expertRepository = $expertRepository;
        $this->participantRepository = $participantRepository;
    }

    public function generateProtocol(ProtocolForm $form) : PhpWord
    {
        $experts = $this->expertRepository->getExpertsFromGroup($form->group->id, [TrainingGroupExpertWork::TYPE_EXTERNAL]);
        $participants = $this->participantRepository->getByIds($form->participants);
        return WordCreator::createProtocol($form->group, $participants, $experts, $form->name);
    }

    public function generateJournal(int $groupId) : Spreadsheet
    {
        return ExcelCreator::createJournal($groupId);
    }
}