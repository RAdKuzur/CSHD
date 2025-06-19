<?php

use yii\db\Migration;

class m250619_050801_add_field_benefits_to_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('foreign_event_participants', 'benefits', $this->integer()->defaultValue(0));
        $this->update('foreign_event_participants', ['benefits' => 0]);
        $this->alterColumn('foreign_event_participants', 'benefits', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('foreign_event_participants',  'benefits');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250619_050801_add_field_benefits_to_participant cannot be reverted.\n";

        return false;
    }
    */
}
