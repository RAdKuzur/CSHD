<?php

use yii\db\Migration;

/**
 * Class m250312_070001_fix_training_group
 */
class m250312_070001_fix_training_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-training_group-2', 'training_group');
        $this->dropColumn('training_group', 'teacher_id');
        $this->alterColumn('training_group', 'archive', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('training_group', 'teacher_id', $this->integer());

        $this->addForeignKey(
            'fk-training_group-2',
            'training_group',
            'teacher_id',
            'people_stamp',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->alterColumn('training_group', 'archive', $this->boolean()->null());

        return true;
    }
}
