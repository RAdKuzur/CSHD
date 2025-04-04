<?php

use yii\db\Migration;

/**
 * Class m250218_120925_add_event_training_group
 */
class m250218_120925_add_event_training_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('event_training_group', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer(),
            'training_group_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-event_training_group-1',
            'event_training_group',
            'event_id',
            'event',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-event_training_group-2',
            'event_training_group',
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
        $this->dropForeignKey('fk-event_training_group-1', 'event_training_group');
        $this->dropForeignKey('fk-event_training_group-2', 'event_training_group');

        $this->dropTable('event_training_group');
    }
}
