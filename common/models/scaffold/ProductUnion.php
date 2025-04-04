<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "product_union".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $count
 * @property float|null $average_price
 * @property float|null $average_cost
 * @property string|null $date
 */
class ProductUnion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_union';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['count'], 'integer'],
            [['average_price', 'average_cost'], 'number'],
            [['date'], 'safe'],
            [['name'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'count' => 'Count',
            'average_price' => 'Average Price',
            'average_cost' => 'Average Cost',
            'date' => 'Date',
        ];
    }
}
