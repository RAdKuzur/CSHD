<?php

namespace frontend\forms\participants;

use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;


class MergeNewParticipantForm extends \yii\base\Model
{
    public $selected = []; // Массив выбранных ID для изменения

    /** @var ForeignEventParticipantsWork[] */
    public $originals;

    /** @var ForeignEventParticipantsWork[] */
    public $duplicates;

    private $repository;

    public function __construct(
        ForeignEventParticipantsRepository $repository,
        array $originals,
        array $duplicates,
                                           $config = []
    ) {
        parent::__construct($config);
        $this->repository = $repository;
        $this->originals = $originals;
        $this->duplicates = $duplicates;
    }

    public function rules()
    {
        return [
            ['selected', 'each', 'rule' => ['integer']],
            ['selected', 'required', 'message' => 'Выберите хотя бы одного участника для изменения'],
        ];
    }

    public function changeSelectedEmails()
    {
        foreach ($this->selected as $index => $selectedId) {
            if ($selectedId) {
                $original = $this->findOriginalById($selectedId);
                $newEmail = $this->findNewEmailForOriginal($original);

                if ($original && $newEmail) {
                    $original->setEmail($newEmail);
                    $this->repository->save($original);
                }
            }
        }
    }

    protected function findOriginalById($id)
    {
        foreach ($this->originals as $original) {
            if ($original->id == $id) {
                return $original;
            }
        }
        return null;
    }

    protected function findNewEmailForOriginal($original)
    {
        foreach ($this->duplicates as $duplicate) {
            if ($this->isSameParticipant($original, $duplicate)) {
                return $duplicate->email;
            }
        }
        return null;
    }

    protected function isSameParticipant($original, $duplicate)
    {
        return $original->firstname === $duplicate->firstname
            && $original->surname === $duplicate->surname
            && $original->birthdate === $duplicate->birthdate
            && $original->patronymic === $duplicate->patronymic;
    }
}