<?php

use yii\db\Migration;

/**
 * Class m240620_110736_first_wave
 */
class m240620_110736_first_wave extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('bot_message', [
            'id' => $this->primaryKey(),
            'text' => $this->text()->notNull(),
        ]);

        $this->createTable('certificate_templates', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'path' => $this->string(256)->notNull(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->createTable('characteristic_object', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'value_type' => $this->smallInteger()->comment('1 - целое, 2 - дробное, 3 - строковое, 4 - булево, 5 - дата, 6 - файл, 7 - выпадающий список')->notNull(),
        ]);

        $this->createTable('complex', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256)->notNull(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->createTable('entry', [
            'id' => $this->primaryKey(),
            'amount' => $this->integer()->notNull(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->createTable('errors', [
            'id' => $this->primaryKey(),
            'number' => $this->string(16)->notNull(),
            'description' => $this->string(128),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->createTable('foreign_event_participants', [
            'id' => $this->primaryKey(),
            'firstname' => $this->string(64)->notNull(),
            'secondname' => $this->string(64)->notNull(),
            'patronymic' => $this->string(64),
            'birthdate' => $this->string(256)->notNull(),
            'sex' => $this->smallInteger()->comment('0 - мужской, 1 - женский')->notNull(),
            'is_true' => $this->smallInteger(),
            'guaranteed_true' => $this->smallInteger(),
            'email' => $this->string(256)->notNull(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->createTable('patchnotes', [
            'id' => $this->primaryKey(),
            'first_number' => $this->integer()->notNull(),
            'second_number' => $this->integer()->notNull(),
            'date' => $this->date()->notNull(),
            'text' => $this->text()->notNull(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->createTable('position', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->createTable('product_union', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256)->notNull(),
            'count' => $this->integer()->notNull(),
            'average_price' => $this->double(),
            'average_cost' => $this->double(),
            'date' => $this->date(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->createTable('project_theme', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'description' => $this->string(256)->notNull(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->createTable('russian_names', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'sex' => $this->string(3),
            'peoples_count' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('bot_message');
        $this->dropTable('certificate_templates');
        $this->dropTable('characteristic_object');
        $this->dropTable('complex');
        $this->dropTable('entry');
        $this->dropTable('errors');
        $this->dropTable('event_external');
        $this->dropTable('foreign_event_participants');

        $this->dropTable('patchnotes');
        $this->dropTable('position');
        $this->dropTable('product_union');
        $this->dropTable('project_theme');
        $this->dropTable('russian_names');

        return true;
    }
}
