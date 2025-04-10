<?php

use yii\db\Migration;

class m250410_111444_add_group_to_event extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event', 'training_group_id', $this->integer());

        $this->addForeignKey(
            'fk-event-7',
            'event',
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
        $this->dropForeignKey('fk-event-7', 'event');
        $this->dropColumn('event', 'training_group_id');

        return true;
    }
}
