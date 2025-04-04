<?php

use yii\db\Migration;

/**
 * Class m250128_121318_alter_foreign_event
 */
class m250128_121318_alter_foreign_event extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('foreign_event', 'creator_id', $this->integer());
        $this->addColumn('foreign_event', 'last_edit_id', $this->integer());

        $this->addForeignKey(
            'fk-foreign_event-6',
            'foreign_event',
            'creator_id',
            'user',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-foreign_event-7',
            'foreign_event',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-foreign_event-6', 'foreign_event');
        $this->dropForeignKey('fk-foreign_event-7', 'foreign_event');

        $this->dropColumn('foreign_event', 'creator_id');
        $this->dropColumn('foreign_event', 'last_edit_id');

        return true;
    }
}
