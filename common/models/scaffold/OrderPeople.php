<?php

namespace common\models\scaffold;

use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\order\DocumentOrderWork;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_people".
 * @property int $id
 * @property int $people_id
 * @property int|null $order_id
 *
 * @property PeopleStampWork $people
 * @property DocumentOrderWork $order
 */
class OrderPeople extends ActiveRecord
{
    public static function tableName()
    {
        return 'order_people';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['people_id', 'order_id'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'people_id' => 'People ID',
        ];
    }

    public function getOrder(){
        return $this->hasOne(DocumentOrderWork::class, ['id' => 'order_id']);
    }
    public function getPeople(){
        return $this->hasOne(PeopleStampWork::class, ['id' => 'people_id']);
    }
}