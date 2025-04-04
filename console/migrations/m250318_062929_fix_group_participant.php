<?php

use yii\db\Migration;

/**
 * Class m250318_062929_fix_group_participant
 */
class m250318_062929_fix_group_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('training_group_participant', 'certificat_number');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('training_group_participant', 'certificat_number', $this->string(8));
        return true;
    }
}
