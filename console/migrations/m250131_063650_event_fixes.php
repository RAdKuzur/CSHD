<?php

use yii\db\Migration;

/**
 * Class m250131_063650_event_fixes
 */
class m250131_063650_event_fixes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('act_participant', 'branch');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('act_participant', 'branch', $this->integer());
        return true;
    }
}
