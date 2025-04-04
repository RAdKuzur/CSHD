<?php

namespace common\repositories\team;

use frontend\models\work\team\TeamNameWork;
use Yii;

class TeamRepository
{
    public function getById($id)
    {
        return TeamNameWork::findOne($id);
    }
    public function getByNameAndForeignEventId($id, $name)
    {
        return TeamNameWork::find()->andWhere(['foreign_event_id' => $id])->andWhere(['name' => $name])->one();
    }
    public function getNamesByForeignEventId($id)
    {
        return TeamNameWork::find()->where(['foreign_event_id' => $id])->all();
    }
    public function prepareTeamNameCreate($model ,$name, $foreignEventId){
        $model->name = $name;
        $model->foreign_event_id = $foreignEventId;
        $model->save();
        return $model->id;
    }
    public function prepareTeamNameDelete($id){
        $command = Yii::$app->db->createCommand();
        $command->delete(TeamNameWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }

}