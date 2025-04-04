<?php

use yii\db\Migration;

/**
 * Class m250123_060814_add_order_people_table
 */
class m250123_060814_add_order_people_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_people}}', [
            'id' => $this->primaryKey(),
            'people_id' => $this->integer()->notNull(),
            'order_id' => $this->integer()->null(),
        ]);
        $this->addForeignKey(
        '{{%fk-order_people-people_id}}',
        '{{%order_people}}',
        'people_id',
        '{{%people_stamp}}', // Replace with actual table name for `people`
        'id',
        'RESTRICT'
        );

        $this->addForeignKey(
        '{{%fk-order_people-order_id}}',
        '{{%order_people}}',
        'order_id',
        '{{%document_order}}', // Replace with actual table name for `order`
        'id',
        'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-order_people-people_id}}', '{{%order_people}}');
        $this->dropForeignKey('{{%fk-order_people-order_id}}', '{{%order_people}}');
        $this->dropTable('{{%order_people}}');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250123_060814_add_order_people_table cannot be reverted.\n";

        return false;
    }
    */
}
