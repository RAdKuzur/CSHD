<?php

use yii\db\Migration;

/**
 * Class m250122_141830_add_order_training_group_participant
 */
class m250122_141830_add_order_training_group_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_training_group_participant}}', [
            'id' => $this->primaryKey(),
            'training_group_participant_out_id' => $this->integer()->null(),
            'training_group_participant_in_id' => $this->integer()->null(),
            'order_id' => $this->integer()->notNull(),
        ]);

        // Add foreign key for table `order_main`
        $this->addForeignKey(
            'fk-order_training_group_participant-order_id',
            '{{%order_training_group_participant}}',
            'order_id',
            '{{%document_order}}',
            'id',
            'RESTRICT'
        );

        // Add foreign key for table `training_group_participant` (in)
        $this->addForeignKey(
            'fk-training_group_participant_in_id',
            '{{%order_training_group_participant}}',
            'training_group_participant_in_id',
            '{{%training_group_participant}}',
            'id',
            'RESTRICT'
        );

        // Add foreign key for table `training_group_participant` (out)
        $this->addForeignKey(
            'fk-training_group_participant_out_id',
            '{{%order_training_group_participant}}',
            'training_group_participant_out_id',
            '{{%training_group_participant}}',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-order_training_group_participant-order_id',
            '{{%order_training_group_participant}}'
        );

        $this->dropForeignKey(
            'fk-training_group_participant_in_id',
            '{{%order_training_group_participant}}'
        );

        $this->dropForeignKey(
            'fk-training_group_participant_out_id',
            '{{%order_training_group_participant}}'
        );

        // Drop table
        $this->dropTable('{{%order_training_group_participant}}');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250122_141830_add_order_training_group_participant cannot be reverted.\n";

        return false;
    }
    */
}
