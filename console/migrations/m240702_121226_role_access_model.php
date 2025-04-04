<?php

use yii\db\Migration;

/**
 * Class m240702_121226_role_access_model
 */
class m240702_121226_role_access_model extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('permission_template', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64),
        ]);

        $this->createTable('permission_function', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'short_code' => $this->string(32),
        ]);

        $this->createTable('permission_template_function', [
            'id' => $this->primaryKey(),
            'template_id' => $this->integer(),
            'function_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-permission_template_function-1',
            'permission_template_function',
            'template_id',
            'permission_template',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-permission_template_function-2',
            'permission_template_function',
            'function_id',
            'permission_function',
            'id',
            'RESTRICT',
        );

        $this->createTable('user_permission_function', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'function_id' => $this->integer(),
            'branch' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-user_permission_function-1',
            'user_permission_function',
            'user_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-user_permission_function-2',
            'user_permission_function',
            'function_id',
            'permission_function',
            'id',
            'RESTRICT',
        );

        $this->createTable('permission_token', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'function_id' => $this->integer(),
            'branch' => $this->integer(),
            'start_time' => $this->dateTime(),
            'end_time' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'fk-permission_token-1',
            'permission_token',
            'user_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-permission_token-2',
            'permission_token',
            'function_id',
            'permission_function',
            'id',
            'RESTRICT',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-permission_token-2', 'permission_token');
        $this->dropForeignKey('fk-permission_token-1', 'permission_token');
        $this->dropTable('permission_token');

        $this->dropForeignKey('fk-user_permission_function-2', 'user_permission_function');
        $this->dropForeignKey('fk-user_permission_function-1', 'user_permission_function');
        $this->dropTable('user_permission_function');

        $this->dropForeignKey('fk-permission_template_function-2', 'permission_template_function');
        $this->dropForeignKey('fk-permission_template_function-1', 'permission_template_function');
        $this->dropTable('permission_template_function');

        $this->dropTable('permission_function');
        $this->dropTable('permission_template');

        return true;
    }
}
