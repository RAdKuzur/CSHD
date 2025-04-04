<?php

use yii\db\Migration;

/**
 * Class m250115_172029_add_lesson_theme
 */
class m250115_172029_add_lesson_theme extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('lesson_theme', [
            'id' => $this->primaryKey(),
            'training_group_lesson_id' => $this->integer(),
            'thematic_plan_id' => $this->integer(),
            'teacher_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-lesson_theme-1',
            'lesson_theme',
            'training_group_lesson_id',
            'training_group_lesson',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-lesson_theme-2',
            'lesson_theme',
            'thematic_plan_id',
            'thematic_plan',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-lesson_theme-3',
            'lesson_theme',
            'teacher_id',
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
        $this->dropForeignKey('fk-lesson_theme-1', 'lesson_theme');
        $this->dropForeignKey('fk-lesson_theme-2', 'lesson_theme');
        $this->dropForeignKey('fk-lesson_theme-3', 'lesson_theme');

        $this->dropTable('lesson_theme');

        return true;
    }
}
