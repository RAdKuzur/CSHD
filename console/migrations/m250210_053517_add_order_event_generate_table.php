<?php

use yii\db\Migration;

/**
 * Class m250210_053517_add_order_event_generate_table
 */
class m250210_053517_add_order_event_generate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('order_event_generate', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'purpose' => $this->integer(),
            'doc_event' => $this->integer(),
            'resp_people_info_id' => $this->integer(),
            'extra_resp_insert_id' => $this->integer(),
            'time_provision_day' => $this->integer(),
            'time_insert_day' => $this->integer(),
            'extra_resp_method_id' => $this->integer(),
            'extra_resp_info_stuff_id' => $this->integer(),
        ]);

        // creates index for column `order_id`
        $this->createIndex(
            'idx-order_event_generate-order_id',
            'order_event_generate',
            'order_id'
        );

        // add foreign key for table `document_order`
        $this->addForeignKey(
            'fk-order_event_generate-order_id',
            'order_event_generate',
            'order_id',
            'document_order',
            'id',
            'RESTRICT' // Изменено на RESTRICT
        );

        // creates index for column `extra_resp_info_stuff_id`
        $this->createIndex(
            'idx-order_event_generate-extra_resp_info_stuff_id',
            'order_event_generate',
            'extra_resp_info_stuff_id'
        );

        // add foreign key for table `people_stamp`
        $this->addForeignKey(
            'fk-order_event_generate-extra_resp_info_stuff_id',
            'order_event_generate',
            'extra_resp_info_stuff_id',
            'people_stamp',
            'id',
            'RESTRICT' // Изменено на RESTRICT
        );

        // creates index for column `extra_resp_insert_id`
        $this->createIndex(
            'idx-order_event_generate-extra_resp_insert_id',
            'order_event_generate',
            'extra_resp_insert_id'
        );

        // add foreign key for table `people_stamp`
        $this->addForeignKey(
            'fk-order_event_generate-extra_resp_insert_id',
            'order_event_generate',
            'extra_resp_insert_id',
            'people_stamp',
            'id',
            'RESTRICT' // Изменено на RESTRICT
        );

        // creates index for column `extra_resp_method_id`
        $this->createIndex(
            'idx-order_event_generate-extra_resp_method_id',
            'order_event_generate',
            'extra_resp_method_id'
        );

        // add foreign key for table `people_stamp`
        $this->addForeignKey(
            'fk-order_event_generate-extra_resp_method_id',
            'order_event_generate',
            'extra_resp_method_id',
            'people_stamp',
            'id',
            'RESTRICT' // Изменено на RESTRICT
        );

        // creates index for column `resp_people_info_id`
        $this->createIndex(
            'idx-order_event_generate-resp_people_info_id',
            'order_event_generate',
            'resp_people_info_id'
        );

        // add foreign key for table `people_stamp`
        $this->addForeignKey(
            'fk-order_event_generate-resp_people_info_id',
            'order_event_generate',
            'resp_people_info_id',
            'people_stamp',
            'id',
            'RESTRICT' // Изменено на RESTRICT
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-order_event_generate-order_id',
            'order_event_generate'
        );

        // drops index for column `order_id`
        $this->dropIndex(
            'idx-order_event_generate-order_id',
            'order_event_generate'
        );

        // drops foreign key for table `people_stamp`
        $this->dropForeignKey(
            'fk-order_event_generate-extra_resp_info_stuff_id',
            'order_event_generate'
        );

        // drops index for column `extra_resp_info_stuff_id`
        $this->dropIndex(
            'idx-order_event_generate-extra_resp_info_stuff_id',
            'order_event_generate'
        );

        // drops foreign key for table `people_stamp`
        $this->dropForeignKey(
            'fk-order_event_generate-extra_resp_insert_id',
            'order_event_generate'
        );

        // drops index for column `extra_resp_insert_id`
        $this->dropIndex(
            'idx-order_event_generate-extra_resp_insert_id',
            'order_event_generate'
        );

        // drops foreign key for table `people_stamp`
        $this->dropForeignKey(
            'fk-order_event_generate-extra_resp_method_id',
            'order_event_generate'
        );

        // drops index for column `extra_resp_method_id`
        $this->dropIndex(
            'idx-order_event_generate-extra_resp_method_id',
            'order_event_generate'
        );

        // drops foreign key for table `people_stamp`
        $this->dropForeignKey(
            'fk-order_event_generate-resp_people_info_id',
            'order_event_generate'
        );

        // drops index for column `resp_people_info_id`
        $this->dropIndex(
            'idx-order_event_generate-resp_people_info_id',
            'order_event_generate'
        );

        $this->dropTable('order_event_generate');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250210_053517_add_order_event_generate_table cannot be reverted.\n";

        return false;
    }
    */
}
