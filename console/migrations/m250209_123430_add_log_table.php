<?php

use yii\db\Migration;

/**
 * Class m250209_123430_add_log_table
 */
class m250209_123430_add_log_table extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $this->createTable('log', [
            'id' => $this->primaryKey(),
            'datetime' => $this->dateTime(),
            'level' => $this->smallInteger(),
            'type' => $this->smallInteger(),
            'user_id' => $this->integer(),
            'text' => $this->text(),
            'add_data' => $this->json()
        ]);

        $this->addForeignKey(
            'fk-log-1',
            'log',
            'user_id',
            'user',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-log-1', 'log');
        $this->dropTable('log');

        return true;
    }
}
