<?php

use yii\db\Migration;

class m250410_111444_add_group_to_event extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('event_group', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer(),
            'training_group_id' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-event_group-1',
            'event_group',
            'event_id',
            'event',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-event_group-2',
            'event_group',
            'training_group_id',
            'training_group',
            'id',
            'RESTRICT',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-event_group-1', 'event_group');
        $this->dropForeignKey('fk-event_group-2', 'event_group');
        $this->dropTable('event_group');

        return true;
    }
}
