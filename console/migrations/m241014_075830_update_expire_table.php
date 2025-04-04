<?php

use yii\db\Migration;

/**
 * Class m241014_075830_update_expire_table
 */
class m241014_075830_update_expire_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey("fk-expire-1", "expire");
        $this->addForeignKey(
            'fk-expire-1',
            'expire',
            'active_regulation_id',
            'document_order',
            'id',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk-expire-3',
            'expire',
            'expire_order_id',
            'document_order',
            'id',
            'RESTRICT',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //echo "m241014_075830_update_expire_table cannot be reverted.\n";
        $this->dropForeignKey("fk-expire-1", "expire");
        $this->dropForeignKey("fk-expire-3", "expire");
        $this->addForeignKey(
            'fk-expire-1',
            'expire',
            'active_regulation_id',
            'regulation',
            'id',
            'RESTRICT',
        );

        return true;
    }
}
