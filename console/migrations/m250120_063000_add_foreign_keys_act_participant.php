<?php

use yii\db\Migration;

/**
 * Class m250120_063000_add_foreign_keys_act_participant
 */
class m250120_063000_add_foreign_keys_act_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey(
            'fk-act_participant-1',
            'act_participant',
            'teacher_id',
            'people_stamp',
            'id',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk-act_participant-2',
            'act_participant',
            'teacher2_id',
            'people_stamp',
            'id',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk-act_participant-3',
            'act_participant',
            'team_name_id',
            'team_name',
            'id',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk-act_participant-4',
            'act_participant',
            'foreign_event_id',
            'foreign_event',
            'id',
            'RESTRICT',
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-act_participant-1', 'act_participant');
        $this->dropForeignKey('fk-act_participant-2', 'act_participant');
        $this->dropForeignKey('fk-act_participant-3', 'act_participant');
        $this->dropForeignKey('fk-act_participant-4', 'act_participant');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250120_063000_add_foreign_keys_act_participant cannot be reverted.\n";

        return false;
    }
    */
}
