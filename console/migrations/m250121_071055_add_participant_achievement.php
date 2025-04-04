<?php

use yii\db\Migration;

/**
 * Class m250121_071055_add_participant_achievement
 */
class m250121_071055_add_participant_achievement extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('participant_achievement', [
            'id' => $this->primaryKey(),
            'act_participant_id' => $this->integer(),
            'achievement' => $this->string(1024),
            'type' => $this->smallInteger(),
            'cert_number' => $this->string(256),
            'nomination' => $this->string(512),
            'date' => $this->date(),
        ]);

        $this->addForeignKey(
            'fk-participant_achievement-1',
            'participant_achievement',
            'act_participant_id',
            'act_participant',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-participant_achievement-1', 'participant_achievement');
        $this->dropTable('participant_achievement');

        return true;
    }
}
