<?php

use yii\db\Migration;

class m250327_123043_add_errors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = $this->db->tablePrefix . 'errors';
        if ($this->db->getTableSchema($tableName, true) !== null) {
            $this->dropTable('errors');
        }

        $this->createTable('errors', [
            'id' => $this->primaryKey(),
            'error' => $this->integer(),
            'table_name' => $this->string(128),
            'table_row_id' => $this->integer(),
            'create_datetime' => $this->dateTime(),
            'was_amnesty' => $this->boolean()->defaultValue(false)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('errors');
        return true;
    }
}
