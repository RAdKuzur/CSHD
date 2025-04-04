<?php

use yii\db\Migration;

class m250401_102518_update_order_event_generate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order_event_generate', 'document_details', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order_event_generate', 'document_details');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250401_102518_update_order_event_generate cannot be reverted.\n";

        return false;
    }
    */
}
