<?php

use yii\db\Migration;

class m250413_135151_change_progect_themes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('project_theme', 'description', $this->string(1024));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('project_theme', 'description', $this->string(256));

        return true;
    }
}
