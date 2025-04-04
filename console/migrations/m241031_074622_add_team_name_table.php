<?php

use yii\db\Migration;

/**
 * Class m241031_074622_add_team_name_table
 */
class m241031_074622_add_team_name_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('team_name', [
            'id' => $this->primaryKey(),
            'name' => $this->string(1000)->notNull(),
            'foreign_event_id' => $this->integer()->notNull(),
        ]);

    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('team_name');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241031_074622_add_team_name_table cannot be reverted.\n";

        return false;
    }
    */
}
