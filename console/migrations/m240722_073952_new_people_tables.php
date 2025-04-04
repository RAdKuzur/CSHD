<?php

use yii\db\Migration;

/**
 * Class m240722_073952_new_people_tables
 */
class m240722_073952_new_people_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('people_position_company_branch', [
            'id' => $this->primaryKey(),
            'people_id' => $this->integer()->notNull(),
            'position_id' => $this->integer()->notNull(),
            'company_id' => $this->integer()->notNull(),
            'branch' => $this->smallInteger()->null(),
        ]);

        $this->addForeignKey(
            'fk-people_position_company_branch-1',
            'people_position_company_branch',
            'people_id',
            'people',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-people_position_company_branch-2',
            'people_position_company_branch',
            'position_id',
            'position',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-people_position_company_branch-3',
            'people_position_company_branch',
            'company_id',
            'company',
            'id',
            'RESTRICT',
        );


        $this->createTable('people_stamp', [
            'id' => $this->primaryKey(),
            'people_id' => $this->integer()->notNull(),
            'surname' => $this->string(256),
            'genitive_surname' => $this->string(256),
            'position_id' => $this->integer(),
            'company_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-people_stamp-1',
            'people_stamp',
            'people_id',
            'people',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-people_stamp-2',
            'people_stamp',
            'position_id',
            'position',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-people_stamp-3',
            'people_stamp',
            'company_id',
            'company',
            'id',
            'RESTRICT',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-people_stamp-3', 'people_stamp');
        $this->dropForeignKey('fk-people_stamp-2', 'people_stamp');
        $this->dropForeignKey('fk-people_stamp-1', 'people_stamp');

        $this->dropTable('people_stamp');

        $this->dropForeignKey('fk-people_position_company_branch-3', 'people_position_company_branch');
        $this->dropForeignKey('fk-people_position_company_branch-2', 'people_position_company_branch');
        $this->dropForeignKey('fk-people_position_company_branch-1', 'people_position_company_branch');

        $this->dropTable('people_position_company_branch');

        return true;
    }
}
