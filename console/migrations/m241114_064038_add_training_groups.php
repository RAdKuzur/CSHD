<?php

use yii\db\Migration;

/**
 * Class m241114_064038_add_training_groups
 */
class m241114_064038_add_training_groups extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('training_group', [
            'id' => $this->primaryKey(),
            'number' => $this->string(64),
            'training_program_id' => $this->integer(),
            'teacher_id' => $this->integer(),
            'start_date' => $this->date(),
            'finish_date' => $this->date(),
            'open' => $this->boolean(),
            'budget' => $this->boolean(),
            'branch' => $this->smallInteger(),
            'order_stop' => $this->boolean(),
            'archive' => $this->boolean(),
            'protection_date' => $this->date(),
            'protection_confirm' => $this->boolean(),
            'is_network' => $this->boolean(),
            'state' => $this->smallInteger()->comment('0 - заполнены основные данные, 1 - загружен контингент, 2 - загружено расписание, 3 - заполнены данные о защите, 4 - выданы сертификаты, группа отчислена и архивирована'),
            'creator_id' => $this->integer(),
            'last_edit_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-training_group-1',
            'training_group',
            'training_program_id',
            'training_program',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_group-2',
            'training_group',
            'teacher_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_group-3',
            'training_group',
            'creator_id',
            'user',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_group-4',
            'training_group',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT'
        );

        $this->createTable('teacher_group', [
            'id' => $this->primaryKey(),
            'teacher_id' => $this->integer(),
            'training_group_id' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-teacher_group-1',
            'teacher_group',
            'teacher_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-teacher_group-2',
            'teacher_group',
            'training_group_id',
            'training_group',
            'id',
            'RESTRICT'
        );

        $this->createTable('training_group_expert', [
            'id' => $this->primaryKey(),
            'expert_id' => $this->integer(),
            'training_group_id' => $this->integer(),
            'expert_type' => $this->smallInteger()
        ]);

        $this->addForeignKey(
            'fk-training_group_expert-1',
            'training_group_expert',
            'expert_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_group_expert-2',
            'training_group_expert',
            'training_group_id',
            'training_group',
            'id',
            'RESTRICT'
        );

        $this->createTable('training_group_lesson', [
            'id' => $this->primaryKey(),
            'lesson_date' => $this->date(),
            'lesson_start_time' => $this->time(),
            'lesson_end_time' => $this->time(),
            'duration' => $this->smallInteger(),
            'branch' => $this->smallInteger(),
            'auditorium_id' => $this->integer(),
            'training_group_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-training_group_lesson-1',
            'training_group_lesson',
            'auditorium_id',
            'auditorium',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_group_lesson-2',
            'training_group_lesson',
            'training_group_id',
            'training_group',
            'id',
            'RESTRICT'
        );

        $this->createTable('group_project_themes', [
            'id' => $this->primaryKey(),
            'training_group_id' => $this->integer(),
            'project_theme_id' => $this->integer(),
            'project_type' => $this->smallInteger(),
            'confirm' => $this->boolean(),
        ]);

        $this->addForeignKey(
            'fk-group_project_themes-1',
            'group_project_themes',
            'training_group_id',
            'training_group',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-group_project_themes-2',
            'group_project_themes',
            'project_theme_id',
            'project_theme',
            'id',
            'RESTRICT'
        );

        $this->createTable('training_group_participant', [
            'id' => $this->primaryKey(),
            'participant_id' => $this->integer(),
            'certificat_number' => $this->string(11),
            'send_method' => $this->smallInteger(),
            'training_group_id' => $this->integer(),
            'status' => $this->smallInteger(),
            'success' => $this->boolean(),
            'points' => $this->integer(),
            'group_project_themes_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-training_group_participant-1',
            'training_group_participant',
            'participant_id',
            'people_stamp',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_group_participant-2',
            'training_group_participant',
            'training_group_id',
            'training_group',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_group_participant-3',
            'training_group_participant',
            'group_project_themes_id',
            'group_project_themes',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-training_group-1', 'training_group');
        $this->dropForeignKey('fk-training_group-2', 'training_group');
        $this->dropForeignKey('fk-training_group-3', 'training_group');
        $this->dropForeignKey('fk-training_group-4', 'training_group');

        $this->dropForeignKey('fk-teacher_group-1', 'teacher_group');
        $this->dropForeignKey('fk-teacher_group-2', 'teacher_group');

        $this->dropForeignKey('fk-training_group_expert-1', 'training_group_expert');
        $this->dropForeignKey('fk-training_group_expert-2', 'training_group_expert');

        $this->dropForeignKey('fk-training_group_lesson-1', 'training_group_lesson');
        $this->dropForeignKey('fk-training_group_lesson-2', 'training_group_lesson');

        $this->dropForeignKey('fk-group_project_themes-1', 'group_project_themes');
        $this->dropForeignKey('fk-group_project_themes-2', 'group_project_themes');

        $this->dropForeignKey('fk-training_group_participant-1', 'training_group_participant');
        $this->dropForeignKey('fk-training_group_participant-2', 'training_group_participant');
        $this->dropForeignKey('fk-training_group_participant-3', 'training_group_participant');

        $this->dropTable('training_group');
        $this->dropTable('teacher_group');
        $this->dropTable('training_group_expert');
        $this->dropTable('training_group_lesson');
        $this->dropTable('group_project_themes');
        $this->dropTable('training_group_participant');

        return true;
    }
}
