<?php

use yii\db\Migration;

/**
 * Class m250210_075912_add_preamble_document_order
 */
class m250210_075912_add_preamble_document_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('document_order', 'preamble', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('document_order', 'preamble');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250210_075912_add_preamble_document_order cannot be reverted.\n";

        return false;
    }
    */
}
