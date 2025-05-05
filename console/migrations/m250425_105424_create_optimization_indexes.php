<?php

use yii\db\Migration;

class m250425_105424_create_optimization_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Индексы для foreign_event
        $this->createIndex(
            'idx_organizer_dates',
            'foreign_event',
            ['organizer_id', 'begin_date', 'end_date']
        );

        // Индексы для act_participant
        $this->createIndex(
            'idx_act_foreign_event',
            'act_participant',
            'foreign_event_id'
        );

        $this->createIndex(
            'idx_teachers',
            'act_participant',
            ['teacher_id', 'teacher2_id']
        );

        // Индекс для act_participant_branch
        $this->createIndex(
            'idx_branch_act',
            'act_participant_branch',
            'act_participant_id'
        );

        // Индекс для squad_participant
        $this->createIndex(
            'idx_squad_act',
            'squad_participant',
            'act_participant_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250425_105424_create_optimization_indexes cannot be reverted.\n";
        // Удаление индексов в обратном порядке
        $this->dropIndex('idx_squad_act', 'squad_participant');
        $this->dropIndex('idx_branch_act', 'act_participant_branch');
        $this->dropIndex('idx_teachers', 'act_participant');
        $this->dropIndex('idx_act_foreign_event', 'act_participant');
        $this->dropIndex('idx_organizer_dates', 'foreign_event');
        $this->dropIndex('idx_foreign_event_organizer', 'foreign_event');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250425_105424_create_optimization_indexes cannot be reverted.\n";

        return false;
    }
    */
}
