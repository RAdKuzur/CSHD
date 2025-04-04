<?php

use yii\db\Migration;

/**
 * Class m250123_112154_add_relations_in_squad
 */
class m250123_112154_add_relations_in_squad extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey(
            'fk-squad_participant-1',
            'squad_participant',
            'act_participant_id',
            'act_participant',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-squad_participant-2',
            'squad_participant',
            'participant_id',
            'foreign_event_participants',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-squad_participant-1', 'squad_participant');
        $this->dropForeignKey('fk-squad_participant-2', 'squad_participant');

        return true;
    }
}
