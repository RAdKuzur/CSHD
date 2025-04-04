<?php

use yii\db\Migration;

/**
 * Class m241010_061849_add_local_responsibility
 */
class m241010_061849_add_local_responsibility extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('auditorium', [
            'id' => $this->primaryKey(),
            'name' => $this->string(16),
            'square' => $this->double(),
            'text' => $this->string(128),
            'capacity' => $this->integer(),
            'is_education' => $this->boolean(),
            'branch' => $this->smallInteger(),
            'include_square' => $this->boolean(),
            'window_count' => $this->smallInteger(),
            'auditorium_type' => $this->smallInteger()
        ]);

        $this->createTable('local_responsibility', [
            'id' => $this->primaryKey(),
            'responsibility_type' => $this->smallInteger(),
            'branch' => $this->smallInteger(),
            'auditorium_id' => $this->integer(),
            'quant' => $this->smallInteger(),
            'people_stamp_id' => $this->integer(),
            'regulation_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-local_responsibility-1',
            'local_responsibility',
            'auditorium_id',
            'auditorium',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-local_responsibility-2',
            'local_responsibility',
            'people_stamp_id',
            'people_stamp',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-local_responsibility-3',
            'local_responsibility',
            'regulation_id',
            'regulation',
            'id',
            'RESTRICT',
        );

        $this->createTable('legacy_responsible', [
            'id' => $this->primaryKey(),
            'people_stamp_id' => $this->integer(),
            'responsibility_type' => $this->smallInteger(),
            'branch' => $this->smallInteger(),
            'auditorium_id' => $this->integer(),
            'quant' => $this->smallInteger(),
            'start_date' => $this->date(),
            'end_date' => $this->date(),
            'order_id' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-legacy_responsible-1',
            'legacy_responsible',
            'auditorium_id',
            'auditorium',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-legacy_responsible-2',
            'legacy_responsible',
            'people_stamp_id',
            'people_stamp',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-legacy_responsible-3',
            'legacy_responsible',
            'order_id',
            'document_order',
            'id',
            'RESTRICT',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаление внешних ключей для таблицы legacy_responsible
        $this->dropForeignKey('fk-legacy_responsible-3', 'legacy_responsible');
        $this->dropForeignKey('fk-legacy_responsible-2', 'legacy_responsible');
        $this->dropForeignKey('fk-legacy_responsible-1', 'legacy_responsible');

        // Удаление таблицы legacy_responsible
        $this->dropTable('legacy_responsible');

        // Удаление внешних ключей для таблицы local_responsibility
        $this->dropForeignKey('fk-local_responsibility-3', 'local_responsibility');
        $this->dropForeignKey('fk-local_responsibility-2', 'local_responsibility');
        $this->dropForeignKey('fk-local_responsibility-1', 'local_responsibility');

        // Удаление таблицы local_responsibility
        $this->dropTable('local_responsibility');

        // Удаление таблицы auditorium
        $this->dropTable('auditorium');

        return true;
    }
}
