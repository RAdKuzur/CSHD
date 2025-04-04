<?php

use yii\db\Migration;

/**
 * Class m241029_061104_add_foreign_event
 */
class m241029_061104_add_foreign_event extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('foreign_event', [
            'id' => $this->primaryKey(),
            'order_participant_id' => $this->integer()->notNull(),
            'name' => $this->string(128)->notNull(),
            'organizer_id' => $this->integer(),
            'begin_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'city' => $this->string(128),
            'format' => $this->smallInteger(),
            'level' => $this->smallInteger(),
            'minister' => $this->smallInteger(),
            'min_age' => $this->integer(),
            'max_age' => $this->integer(),
            'key_words' => $this->string(128),
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('foreign_event');
        return true;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241029_061104_add_foreign_event cannot be reverted.\n";

        return false;
    }
    */
}
