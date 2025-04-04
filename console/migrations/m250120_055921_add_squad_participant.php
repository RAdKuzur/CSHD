<?php

use yii\db\Migration;

/**
 * Class m250120_055921_add_squad_participant
 */
class m250120_055921_add_squad_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('squad_participant', [
            'id' => $this->primaryKey(),
            'act_participant_id' => $this->integer()->notNull(),
            'participant_id' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('squad_participant');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250120_055921_add_squad_participant cannot be reverted.\n";

        return false;
    }
    */
}
