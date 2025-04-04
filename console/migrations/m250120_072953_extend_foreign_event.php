<?php

use yii\db\Migration;

/**
 * Class m250120_072953_extend_foreign_event
 */
class m250120_072953_extend_foreign_event extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('foreign_event', 'escort_id', $this->integer());
        $this->addColumn('foreign_event', 'add_order_participant_id', $this->integer());
        $this->addColumn('foreign_event', 'order_business_trip_id', $this->integer());

        $this->addForeignKey(
            'fk-foreign_event-1',
            'foreign_event',
            'order_participant_id',
            'document_order',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-foreign_event-2',
            'foreign_event',
            'organizer_id',
            'company',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-foreign_event-3',
            'foreign_event',
            'escort_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-foreign_event-4',
            'foreign_event',
            'add_order_participant_id',
            'document_order',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-foreign_event-5',
            'foreign_event',
            'order_business_trip_id',
            'document_order',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-foreign_event-1', 'foreign_event');
        $this->dropForeignKey('fk-foreign_event-2', 'foreign_event');
        $this->dropForeignKey('fk-foreign_event-3', 'foreign_event');
        $this->dropForeignKey('fk-foreign_event-4', 'foreign_event');
        $this->dropForeignKey('fk-foreign_event-5', 'foreign_event');

        $this->dropColumn('foreign_event', 'order_participant_id');
        $this->dropColumn('foreign_event', 'organizer_id');
        $this->dropColumn('foreign_event', 'escort_id');
        $this->dropColumn('foreign_event', 'add_order_participant_id');
        $this->dropColumn('foreign_event', 'order_business_trip_id');

        return true;
    }
}
