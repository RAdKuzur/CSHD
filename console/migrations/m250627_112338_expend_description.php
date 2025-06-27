<?php

use yii\db\Migration;

class m250627_112338_expend_description extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('project_theme', 'description', $this->string(2048));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('project_theme', 'description', $this->string(1024));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250627_112338_expend_description cannot be reverted.\n";

        return false;
    }
    */
}
