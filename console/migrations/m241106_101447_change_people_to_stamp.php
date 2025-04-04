<?php

use yii\db\Migration;

/**
 * Class m241106_101447_change_people_to_stamp
 */
class m241106_101447_change_people_to_stamp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('SET foreign_key_checks = 0;');
        $this->truncateTable('in_out_documents');
        $this->truncateTable('document_in');
        $this->truncateTable('document_out');
        $this->truncateTable('event');
        $this->truncateTable('training_program');
        $this->truncateTable('author_program');
        $this->execute('SET foreign_key_checks = 1;');

        $this->dropForeignKey('fk-document_in-1', 'document_in');
        $this->dropForeignKey('fk-document_in-4', 'document_in');

        $this->addForeignKey(
            'fk-document_in-1',
            'document_in',
            'correspondent_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-document_in-4',
            'document_in',
            'signed_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->dropForeignKey('fk-document_out-1', 'document_out');
        $this->dropForeignKey('fk-document_out-4', 'document_out');
        $this->dropForeignKey('fk-document_out-5', 'document_out');

        $this->addForeignKey(
            'fk-document_out-1',
            'document_out',
            'correspondent_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-document_out-4',
            'document_out',
            'signed_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-document_out-5',
            'document_out',
            'executor_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->dropForeignKey('fk-event-1', 'event');
        $this->dropForeignKey('fk-event-2', 'event');

        $this->addForeignKey(
            'fk-event-1',
            'event',
            'responsible1_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-event-2',
            'event',
            'responsible2_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->dropForeignKey('fk-training_program-1', 'training_program');

        $this->addForeignKey(
            'fk-training_program-1',
            'training_program',
            'author_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->dropForeignKey('fk-in_out_documents-3', 'in_out_documents');

        $this->addForeignKey(
            'fk-in_out_documents-3',
            'in_out_documents',
            'responsible_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->dropForeignKey('fk-author_program-1', 'author_program');

        $this->addForeignKey(
            'fk-author_program-1',
            'author_program',
            'author_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
