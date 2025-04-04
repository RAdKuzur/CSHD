<?php

use yii\db\Migration;

/**
 * Class m250114_143731_change_visits
 */
class m250114_143731_change_visits extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-visit-1', 'visit');
        $this->dropForeignKey('fk-visit-2', 'visit');

        $this->dropColumn('visit', 'training_group_id');
        $this->dropColumn('visit', 'participant_id');

        $this->addColumn('visit', 'training_group_participant_id', $this->integer());

        $this->addForeignKey(
            'fk-visit-1',
            'visit',
            'training_group_participant_id',
            'training_group_participant',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-visit-1', 'visit');

        $this->addColumn('visit', 'participant_id', $this->integer());
        $this->addColumn('visit', 'training_group_id', $this->integer());

        $this->dropColumn('visit', 'training_group_participant_id');

        $this->addForeignKey(
            'fk-visit-1',
            'visit',
            'participant_id',
            'foreign_event_participants',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-visit-2',
            'visit',
            'training_group_id',
            'training_group',
            'id',
            'RESTRICT'
        );

        return true;
    }
}
