<?php

use yii\db\Migration;

/**
 * Class m240621_081719_document_in_out
 */
class m240621_081719_document_in_out extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('company', [
            'id' => $this->primaryKey(),
            'company_type' => $this->smallInteger()->comment('1 - образовательное учреждение, 2 - государственное учреждение, 3 - частная организация/ИП'),
            'name' => $this->string(128)->notNull(),
            'short_name' => $this->string(128)->notNull(),
            'is_contractor' => $this->boolean()->notNull(),
            'inn' => $this->string(15),
            'category_smsp' => $this->smallInteger()->comment('1 - микропредприятие, 2 - малое предприятие, 3 - среднее предприятие, 4 - самозанятый, 5 - НЕ СМСП'),
            'comment' => $this->string(256),
            'last_edit_id' => $this->integer(),
            'phone_number' => $this->string(12),
            'email' => $this->string(256),
            'site' => $this->string(256),
            'ownership_type' => $this->smallInteger()->comment('1 - бюджетное, 2 - автономное, 3 - казённое, 4 - унитарное, 5 - НКО, 6 - нетиповое, 7 - ООО, 8 - ИП, 9 - ПАО, 10 - АО, 11 - ЗАО, 12 - физлицо, 13 - прочее'),
            'okved' => $this->string(12),
            'head_fio' => $this->string(256),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->createTable('people', [
            'id' => $this->primaryKey(),
            'firstname' => $this->string(256)->notNull(),
            'surname' => $this->string(256)->notNull(),
            'patronymic' => $this->string(256),
            'company_id' => $this->integer(),
            'position_id' => $this->integer(),
            'short' => $this->string(10),
            'branch' => $this->smallInteger(),
            'birthdate' => $this->date(),
            'sex' => $this->smallInteger(),
            'genitive_surname' => $this->string(256),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->addForeignKey(
            'fk-people-1',
            'people',
            'company_id',
            'company',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-people-2',
            'people',
            'position_id',
            'position',
            'id',
            'RESTRICT',
        );

        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'firstname' => $this->string(256)->notNull(),
            'surname' => $this->string(256)->notNull(),
            'patronymic' => $this->string(256),
            'username' => $this->string(256)->notNull(),
            'auth_key' => $this->string(32),
            'password_hash' => $this->string(256)->notNull(),
            'password_reset_token' => $this->string(256),
            'email' => $this->string(256)->notNull(),
            'aka' => $this->integer(),
            'status' => $this->integer()->defaultValue(10),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'creator_id' => $this->integer(),
            'last_edit_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-company-1',
            'company',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-user-1',
            'user',
            'aka',
            'people',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-user-2',
            'user',
            'creator_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-user-3',
            'user',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->createTable('document_in', [
            'id' => $this->primaryKey(),
            'local_number' => $this->integer()->notNull(),
            'local_postfix' => $this->smallInteger(),
            'local_date' => $this->date()->notNull(),
            'real_number' => $this->string(64),
            'real_date' => $this->date()->notNull(),
            'correspondent_id' => $this->integer(),
            'position_id' => $this->integer(),
            'company_id' => $this->integer(),
            'document_theme' => $this->string(256)->notNull(),
            'signed_id' => $this->integer(),
            'target' => $this->string(256),
            'get_id' => $this->integer(),
            'send_method' => $this->smallInteger(),
            'creator_id' => $this->integer()->notNull(),
            'last_edit_id' => $this->integer(),
            'key_words' => $this->string(512),
            'need_answer' => $this->boolean(),
        ]);

        $this->addForeignKey(
            'fk-document_in-1',
            'document_in',
            'correspondent_id',
            'people',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_in-2',
            'document_in',
            'position_id',
            'position',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_in-3',
            'document_in',
            'company_id',
            'company',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_in-4',
            'document_in',
            'signed_id',
            'people',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_in-5',
            'document_in',
            'get_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_in-6',
            'document_in',
            'creator_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_in-7',
            'document_in',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->createTable('files', [
            'id' => $this->primaryKey(),
            'table_name' => $this->string(64),
            'table_row_id' => $this->integer(),
            'file_type' => $this->string(32),
            'filepath' => $this->string(256),
        ]);

        $this->createTable('document_out', [
            'id' => $this->primaryKey(),
            'document_number' => $this->integer()->notNull(),
            'document_postfix' => $this->smallInteger(),
            'document_date' => $this->date()->notNull(),
            'document_name' => $this->string(64)->notNull(),
            'document_theme' => $this->string(256)->notNull(),
            'correspondent_id' => $this->integer(),
            'position_id' => $this->integer(),
            'company_id' => $this->integer(),
            'signed_id' => $this->integer(),
            'executor_id' => $this->integer(),
            'send_method' => $this->smallInteger(),
            'sent_date' => $this->date()->notNull(),
            'creator_id' => $this->integer()->notNull(),
            'last_edit_id' => $this->integer(),
            'key_words' => $this->string(512),
            'is_answer' => $this->boolean(),
        ]);

        $this->addForeignKey(
            'fk-document_out-1',
            'document_out',
            'correspondent_id',
            'people',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_out-2',
            'document_out',
            'position_id',
            'position',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_out-3',
            'document_out',
            'company_id',
            'company',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_out-4',
            'document_out',
            'signed_id',
            'people',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_out-5',
            'document_out',
            'executor_id',
            'people',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_out-6',
            'document_out',
            'creator_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-document_out-7',
            'document_out',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT',
        );


        $this->createTable('in_out_documents', [
            'id' => $this->primaryKey(),
            'document_in_id' => $this->integer()->notNull(),
            'document_out_id' => $this->integer(),
            'date' => $this->date(),
            'responsible_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-in_out_documents-1',
            'in_out_documents',
            'document_in_id',
            'document_in',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-in_out_documents-2',
            'in_out_documents',
            'document_out_id',
            'document_out',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-in_out_documents-3',
            'in_out_documents',
            'responsible_id',
            'people',
            'id',
            'RESTRICT',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-document_in-7', 'document_in');
        $this->dropForeignKey('fk-document_in-6', 'document_in');
        $this->dropForeignKey('fk-document_in-5', 'document_in');
        $this->dropForeignKey('fk-document_in-4', 'document_in');
        $this->dropForeignKey('fk-document_in-3', 'document_in');
        $this->dropForeignKey('fk-document_in-2', 'document_in');
        $this->dropForeignKey('fk-document_in-1', 'document_in');

        $this->dropTable('files');

        $this->dropForeignKey('fk-company-1', 'company');
        $this->dropForeignKey('fk-people-2', 'people');
        $this->dropForeignKey('fk-people-1', 'people');

        $this->dropForeignKey('fk-document_out-1', 'document_out');
        $this->dropForeignKey('fk-document_out-2', 'document_out');
        $this->dropForeignKey('fk-document_out-3', 'document_out');
        $this->dropForeignKey('fk-document_out-4', 'document_out');
        $this->dropForeignKey('fk-document_out-5', 'document_out');
        $this->dropForeignKey('fk-document_out-6', 'document_out');
        $this->dropForeignKey('fk-document_out-7', 'document_out');

        $this->dropTable('document_out');
        $this->dropTable('user');
        $this->dropTable('document_in');
        $this->dropTable('people');
        $this->dropTable('company');

        $this->dropForeignKey('fk-in_out_documents-1', 'in_out_documents');
        $this->dropForeignKey('fk-in_out_documents-2', 'in_out_documents');
        $this->dropForeignKey('fk-in_out_documents-3', 'in_out_documents');

        $this->dropTable('in_out_documents');

        return true;
    }
}
