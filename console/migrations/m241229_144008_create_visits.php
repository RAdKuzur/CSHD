<?php

use yii\db\Migration;

/**
 * Class m241229_144008_create_visits
 */
class m241229_144008_create_visits extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('visit', [
            'id' => $this->primaryKey(),
            'training_group_id' => $this->integer(),
            'participant_id' => $this->integer(),
            'lessons' => $this->json(),
        ]);

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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-visit-2', 'visit');
        $this->dropForeignKey('fk-visit-1', 'visit');
        $this->dropTable('visit');

        return true;
    }
}
