<?php

use yii\db\Migration;

/**
 * Class m241007_091939_add_document_order
 */
class m241007_091939_add_document_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('document_order', [
            'id' => $this->primaryKey(),
            'order_copy_id' => $this->integer(),
            'order_number' => $this->string(),
            'order_postfix' => $this->integer(),
            'order_name' => $this->string(),
            'order_date' => $this->date(),
            'signed_id' =>$this->integer(),
            'bring_id' => $this->integer(),
            'executor_id' => $this->integer(),
            'key_words' => $this->string(),
            'creator_id' => $this->integer(),
            'last_edit_id' => $this->integer(),
            'type' => $this->smallInteger(),
            'state' => $this->smallInteger(),
            'nomenclature_id' => $this->integer(),
            'study_type' => $this->smallInteger(),
        ]);
        $this->addForeignKey(
            'fk-event-3',
            'event',
            'order_id',
            'document_order',
            'id',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk-document_order-1',
            'document_order',
            'signed_id',
            'people_stamp',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_order-2',
            'document_order',
            'executor_id',
            'people_stamp',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_order-3',
            'document_order',
            'bring_id',
            'people_stamp',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_order-4',
            'document_order',
            'creator_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_order-5',
            'document_order',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-event-3', 'event');
        $this->dropForeignKey('fk-document_order-1', 'document_order');
        $this->dropForeignKey('fk-document_order-2', 'document_order');
        $this->dropForeignKey('fk-document_order-3', 'document_order');
        $this->dropForeignKey('fk-document_order-4', 'document_order');
        $this->dropForeignKey('fk-document_order-5', 'document_order');
        $this->dropTable('document_order');
        return true;
    }
}
