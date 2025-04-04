<?php

use yii\db\Migration;

/**
 * Class m240925_073802_add_training_program
 */
class m240925_073802_add_training_program extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('training_program', [
            'id' => $this->primaryKey(),
            'name' => $this->string(1024),
            'thematic_direction' => $this->smallInteger(),
            'level' => $this->smallInteger(),
            'ped_council_date' => $this->date(),
            'ped_council_number' => $this->string(128),
            'author_id' => $this->integer(),
            'capacity' => $this->smallInteger()->unsigned(),
            'hour_capacity' => $this->smallInteger()->unsigned(),
            'student_left_age' => $this->float(),
            'student_right_age' => $this->smallInteger()->unsigned(),
            'focus' => $this->smallInteger()->unsigned(),
            'allow_remote' => $this->smallInteger()->unsigned(),
            'actual' => $this->boolean(),
            'certificate_type' => $this->smallInteger()->unsigned(),
            'description' => $this->string(1024),
            'key_words' => $this->string(1024),
            'is_network' => $this->boolean(),
            'creator_id' => $this->integer(),
            'last_update_id' => $this->integer(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->addForeignKey(
            'fk-training_program-1',
            'training_program',
            'author_id',
            'people',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-training_program-2',
            'training_program',
            'creator_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-training_program-3',
            'training_program',
            'last_update_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->createTable('thematic_plan', [
            'id' => $this->primaryKey(),
            'theme' => $this->string(256),
            'training_program_id' => $this->integer(),
            'control_type' => $this->smallInteger(),
        ]);

        $this->addForeignKey(
            'fk-thematic_plan-1',
            'thematic_plan',
            'training_program_id',
            'training_program',
            'id',
            'RESTRICT',
        );

        $this->createTable('author_program', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer(),
            'training_program_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-author_program-1',
            'author_program',
            'author_id',
            'people',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-author_program-2',
            'author_program',
            'training_program_id',
            'training_program',
            'id',
            'RESTRICT',
        );

        $this->createTable('branch_program', [
            'id' => $this->primaryKey(),
            'branch' => $this->smallInteger(),
            'training_program_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-branch_program-1',
            'branch_program',
            'training_program_id',
            'training_program',
            'id',
            'RESTRICT',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-author_program-1', 'author_program');
        $this->dropForeignKey('fk-author_program-2', 'author_program');
        $this->dropTable('author_program');

        $this->dropForeignKey('fk-thematic_plan-1', 'thematic_plan');
        $this->dropTable('thematic_plan');

        $this->dropForeignKey('fk-branch_program-1', 'branch_program');
        $this->dropTable('branch_program');

        $this->dropForeignKey('fk-training_program-1', 'training_program');
        $this->dropForeignKey('fk-training_program-2', 'training_program');
        $this->dropForeignKey('fk-training_program-3', 'training_program');
        $this->dropTable('training_program');

        return true;
    }
}
