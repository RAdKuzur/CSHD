<?php

use yii\db\Migration;

/**
 * Class m250122_070252_add_certificate
 */
class m250122_070252_add_certificate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('certificate', [
            'id' => $this->primaryKey(),
            'certificate_number' => $this->integer(),
            'certificate_template_id' => $this->integer(),
            'training_group_participant_id' => $this->integer(),
            'status' => $this->smallInteger(),
        ]);

        $this->addForeignKey(
            'fk-certificate-1',
            'certificate',
            'certificate_template_id',
            'certificate_templates',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-certificate-2',
            'certificate',
            'training_group_participant_id',
            'training_group_participant',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-certificate-1', 'certificate');
        $this->dropForeignKey('fk-certificate-2', 'certificate');

        $this->dropTable('certificate');
    }
}
