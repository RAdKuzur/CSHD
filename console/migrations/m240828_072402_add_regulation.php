<?php

use yii\db\Migration;

/**
 * Class m240828_072402_add_regulation
 */
class m240828_072402_add_regulation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('regulation', [
            'id' => $this->primaryKey(),
            'date' => $this->date(),
            'name' => $this->string(512),
            'short_name' => $this->string(256),
            'order_id' => $this->integer(),
            'ped_council_date' => $this->date(),
            'ped_council_number' => $this->integer(),
            'par_council_date' => $this->date(),
            'state' => $this->smallInteger()->comment('0 - утратило силу; 1 - актуально'),
            'regulation_type' => $this->smallInteger()->comment('1 - Положения, инструкции, правила; 2 - Положения о мероприятиях'),
            'scan' => $this->string(512),
            'creator_id' => $this->integer(),
            'last_edit_id' => $this->integer(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp()
        ]);

        $this->addForeignKey(
            'fk-regulation-1',
            'regulation',
            'creator_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-regulation-2',
            'regulation',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT',
        );


        $this->createTable('expire', [
            'id' => $this->primaryKey(),
            'active_regulation_id' => $this->integer(),
            'expire_regulation_id' => $this->integer(),
            'expire_order_id' => $this->integer(),
            'document_type' => $this->integer()->comment('1 - Приказ; 2 - Исходящий; 3 - Входящий; 4 - Положение; 5 - Положение о мероприятии'),
            'expire_type' => $this->integer()->comment('1 - Утратило силу; 2 - Изменено'),
        ]);

        $this->addForeignKey(
            'fk-expire-1',
            'expire',
            'active_regulation_id',
            'regulation',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-expire-2',
            'expire',
            'expire_regulation_id',
            'regulation',
            'id',
            'RESTRICT',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-regulation-1',
            'regulation'
        );

        $this->dropForeignKey(
            'fk-regulation-2',
            'regulation'
        );

        $this->dropTable('regulation');


        $this->dropForeignKey(
            'fk-expire-1',
            'expire'
        );

        $this->dropForeignKey(
            'fk-expire-2',
            'expire'
        );

        $this->dropTable('expire');

        return true;
    }
}
