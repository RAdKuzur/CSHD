<?php

use yii\db\Migration;

/**
 * Class m240814_055623_object_states
 */
class m240814_055623_object_states extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('object_states', [
            'id' => $this->primaryKey(),
            'table_name' => $this->string(128),
            'table_row_id' => $this->smallInteger(),
            'state' => $this->smallInteger()->comment('0 - доступен, 1 - открыт на чтение, 2 - открыт на запись'),
            'last_lock_time' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('object_states');

        return true;
    }
}
