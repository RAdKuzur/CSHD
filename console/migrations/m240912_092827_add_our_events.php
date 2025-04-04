<?php

use yii\db\Migration;

/**
 * Class m240912_092827_add_our_events
 */
class m240912_092827_add_our_events extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('event', [
            'id' => $this->primaryKey(),
            'name' => $this->string(512),
            'start_date' => $this->date(),
            'finish_date' => $this->date(),
            'event_type' => $this->smallInteger(),
            'event_form' => $this->smallInteger(),
            'event_level' => $this->smallInteger(),
            'event_way' => $this->smallInteger(),
            'address' => $this->string(1024),
            'is_federal' => $this->boolean(),
            'responsible1_id' => $this->integer(),
            'responsible2_id' => $this->integer(),
            'key_words' => $this->string(1024),
            'comment' => $this->string(1024),
            'order_id' => $this->integer(),
            'regulation_id' => $this->integer(),
            'contains_education' => $this->boolean(),
            'participation_scope' => $this->smallInteger(),
            'child_participants_count' => $this->integer(),
            'child_rst_participants_count' => $this->integer(),
            'teacher_participants_count' => $this->integer(),
            'other_participants_count' => $this->integer(),
            'age_left_border' => $this->float(),
            'age_right_border' => $this->smallInteger(),
            'creator_id' => $this->integer(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->addForeignKey(
            'fk-event-1',
            'event',
            'responsible1_id',
            'people',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-event-2',
            'event',
            'responsible2_id',
            'people',
            'id',
            'RESTRICT',
        );

        /*$this->addForeignKey(
            'fk-event-3',
            'event',
            'order_id',
            'document_order',
            'id',
            'RESTRICT',
        );*/

        $this->addForeignKey(
            'fk-event-4',
            'event',
            'regulation_id',
            'regulation',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-event-5',
            'event',
            'creator_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->createTable('event_branch', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer(),
            'branch' => $this->smallInteger(),
        ]);

        $this->addForeignKey(
            'fk-event_branch-1',
            'event_branch',
            'event_id',
            'event',
            'id',
            'RESTRICT',
        );

        $this->createTable('event_scope', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer(),
            'participation_scope' => $this->smallInteger()
        ]);

        $this->addForeignKey(
            'fk-event_scope-1',
            'event_scope',
            'event_id',
            'event',
            'id',
            'RESTRICT',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-event_scope-1', 'event_scope');

        $this->dropTable('event_scope');

        $this->dropForeignKey('fk-event_branch-1', 'event_branch');

        $this->dropTable('event_branch');

        $this->dropForeignKey('fk-event-5', 'event');
        $this->dropForeignKey('fk-event-4', 'event');
        //$this->dropForeignKey('fk-event-3', 'event');
        $this->dropForeignKey('fk-event-2', 'event');
        $this->dropForeignKey('fk-event-1', 'event');

        $this->dropTable('event');

        return true;
    }
}
