<?php

use yii\db\Migration;

/**
 * Class m241119_081835_add_last_update_models
 */
class m241119_081835_add_last_update_models extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event', 'last_edit_id', $this->integer());
        $this->addForeignKey(
            'fk-event-6',
            'event',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT'
        );

        $this->addColumn('local_responsibility', 'creator_id', $this->integer());
        $this->addColumn('local_responsibility', 'last_edit_id', $this->integer());
        $this->addForeignKey(
            'fk-local_responsibility-4',
            'local_responsibility',
            'creator_id',
            'user',
            'id',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-local_responsibility-5',
            'local_responsibility',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT'
        );

        $this->renameColumn('training_program', 'last_update_id', 'last_edit_id');

        $this->addColumn('people', 'creator_id', $this->integer());
        $this->addColumn('people', 'last_edit_id', $this->integer());
        $this->addForeignKey(
            'fk-people-3',
            'people',
            'creator_id',
            'user',
            'id',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-people-4',
            'people',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT'
        );

        $this->addColumn('foreign_event_participants', 'creator_id', $this->integer());
        $this->addColumn('foreign_event_participants', 'last_edit_id', $this->integer());
        $this->addForeignKey(
            'fk-foreign_event_participants-1',
            'foreign_event_participants',
            'creator_id',
            'user',
            'id',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-foreign_event_participants-2',
            'foreign_event_participants',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT'
        );

        $this->addCommentOnColumn('training_group', 'state', '0 - заполнены основные данные, 
                                                                                   1 - загружен контингент и расписание, 
                                                                                   2 - заполнены данные о защите, 
                                                                                   3 - выданы сертификаты, 
                                                                                   4 - группа отчислена и архивирована');

        $this->dropForeignKey('fk-training_group_participant-1', 'training_group_participant');
        $this->addForeignKey(
            'fk-training_group_participant-1',
            'training_group_participant',
            'participant_id',
            'foreign_event_participants',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-foreign_event_participants-2', 'foreign_event_participants');
        $this->dropForeignKey('fk-foreign_event_participants-1', 'foreign_event_participants');
        $this->dropColumn('foreign_event_participants', 'last_edit_id');
        $this->dropColumn('foreign_event_participants', 'creator_id');

        $this->dropForeignKey('fk-people-4', 'people');
        $this->dropForeignKey('fk-people-3', 'people');
        $this->dropColumn('people', 'last_edit_id');
        $this->dropColumn('people', 'creator_id');

        $this->dropForeignKey('fk-local_responsibility-5', 'local_responsibility');
        $this->dropForeignKey('fk-local_responsibility-4', 'local_responsibility');
        $this->dropColumn('local_responsibility', 'last_edit_id');
        $this->dropColumn('local_responsibility', 'creator_id');

        $this->dropForeignKey('fk-event-6', 'event');
        $this->dropColumn('event', 'last_edit_id');

        $this->renameColumn('training_program', 'last_edit_id', 'last_update_id');

        return true;
    }
}
