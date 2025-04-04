<?php

use yii\db\Migration;

class m250331_095718_add_state_to_errors extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('errors', 'state', $this->smallInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('errors', 'state');
        return true;
    }
}
