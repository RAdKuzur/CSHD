<?php

use yii\db\Migration;

class m250330_181449_add_branch_to_error extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('errors', 'branch', $this->smallInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('errors', 'branch');
        return true;
    }
}
