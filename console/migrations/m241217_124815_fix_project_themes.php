<?php

use yii\db\Migration;

/**
 * Class m241217_124815_fix_project_themes
 */
class m241217_124815_fix_project_themes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('group_project_themes', 'project_type');
        $this->addColumn('project_theme', 'project_type', $this->smallInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('group_project_themes', 'project_type', $this->smallInteger());
        $this->dropColumn('project_theme', 'project_type');

        return true;
    }
}
