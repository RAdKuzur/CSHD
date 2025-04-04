<?php

use yii\db\Migration;

/**
 * Class m250128_123926_add_act_participant_branch_table
 */
class m250128_123926_add_act_participant_branch_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create table
        $this->createTable('{{%act_participant_branch}}', [
            'id' => $this->primaryKey(),
            'act_participant_id' => $this->integer()->notNull(),
            'branch' => $this->integer()->notNull(),
        ]);

        // Add foreign key for table `{{%act_participant}}`
        $this->addForeignKey(
            '{{%fk-act_participant_branch-act_participant_id}}',
            '{{%act_participant_branch}}',
            'act_participant_id',
            '{{%act_participant}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey(
            '{{%fk-act_participant_branch-act_participant_id}}',
            '{{%act_participant_branch}}'
        );
        // Drop table
        $this->dropTable('{{%act_participant_branch}}');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250128_123926_add_act_participant_branch_table cannot be reverted.\n";

        return false;
    }
    */
}
