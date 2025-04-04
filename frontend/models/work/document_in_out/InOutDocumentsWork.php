<?php

namespace frontend\models\work\document_in_out;

use common\models\scaffold\InOutDocuments;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;

/**
 * @property PeopleStampWork $responsibleWork
 * @property DocumentInWork $documentInWork
 */
class InOutDocumentsWork extends InOutDocuments
{
    public static function fill(
        $documentInId,
        $documentOutId = null,
        $date = null,
        $responsibleId = null
    )
    {
        $entity = new static();
        $entity->document_in_id = $documentInId;
        $entity->document_out_id = $documentOutId;
        $entity->date = $date;
        $entity->responsible_id = $responsibleId;

        return $entity;
    }
    public function linkDocOut(
        $entity,
        $documentInId,
        $documentOutId = null,
        $date = null,
        $responsibleId = null
    )
    {
        $entity->document_out_id = $documentOutId;
        return $entity;
    }
    public function isDocumentOutEmpty()
    {
        return $this->document_out_id == null;
    }
    public function isDocumentInEmpty()
    {
        return $this->document_in_id == null;
    }
    public function isNoPeopleTarget()
    {
        return $this->responsible_id == null;
    }

    public function isNoAnswerDate()
    {
        return $this->date == null;
    }

    public function getRowClass()
    {
        if ($this->document_out_id !== null) {
            return ['class' => 'default'];
        }
        else if ($this->date !== null && $this->date < date("Y-m-d")) {
            return ['class' => 'danger'];
        }
        else {
            return ['class' => 'warning'];
        }
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getIsEmptyDocumentOut()
    {
        return empty($this->document_out_id);
    }

    // --relationships--

    public function getResponsibleWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'responsible_id']);
    }

    public function getDocumentInWork()
    {
        return $this->hasOne(DocumentInWork::class, ['id' => 'document_in_id']);
    }
}