<?php
use yii\db\Migration;
/**
 * Class m241119_061236_update_act_participant
 */
class m241119_061236_update_act_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%act_participant}}', [
            'id' => $this->primaryKey(),
            'teacher_id' => $this->integer()->null(),
            'teacher2_id' => $this->integer()->null(),
            'branch' => $this->integer()->notNull(),
            'focus' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull(),
            'nomination' => $this->string(1000)->notNull(),
            'team_name_id' => $this->integer()->null(),
            'form' => $this->integer()->null(),
            'foreign_event_id' => $this->integer()->notNull(),
            'allow_remote' => $this->integer()->null(),
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%act_participant}}');
        return true;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }
    public function down()
    {
        echo "m241119_061236_update_act_participant cannot be reverted.\n";

        return false;
    }
    */
}
