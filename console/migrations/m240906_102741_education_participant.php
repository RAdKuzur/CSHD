<?php

use yii\db\Migration;

/**
 * Class m240906_102741_education_participant
 */
class m240906_102741_education_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('personal_data_participant', [
            'id' => $this->primaryKey(),
            'participant_id' => $this->integer(),
            'personal_data' => $this->smallInteger(),
            'status' => $this->tinyInteger()
        ]);

        $this->addForeignKey(
            'fk-personal_data_participant-1',
            'personal_data_participant',
            'participant_id',
            'foreign_event_participants',
            'id',
            'RESTRICT',
        );

        $this->renameColumn('foreign_event_participants', 'secondname', 'surname');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('foreign_event_participants', 'surname', 'secondname');
        $this->dropForeignKey('fk-personal_data_participant-1', 'personal_data_participant');
        $this->dropTable('personal_data_participant');

        return true;
    }
}
