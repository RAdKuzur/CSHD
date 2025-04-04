<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "auditorium".
 *
 * @property int $id
 * @property string|null $name
 * @property float|null $square
 * @property string|null $text
 * @property int|null $capacity
 * @property int|null $is_education
 * @property int|null $branch
 * @property int|null $include_square
 * @property int|null $window_count
 * @property int|null $auditorium_type
 *
 * @property LegacyResponsible[] $legacyResponsibles
 * @property LocalResponsibility[] $localResponsibilities
 */
class Auditorium extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auditorium';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['square'], 'number'],
            [['capacity', 'is_education', 'branch', 'include_square', 'window_count', 'auditorium_type'], 'integer'],
            [['name'], 'string', 'max' => 16],
            [['text'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Уникальный глобальный номер',
            'square' => 'Площадь (кв.м.)',
            'text' => 'Имя',
            'capacity' => 'Capacity',
            'is_education' => 'Предназначен для обр. деят.',
            'branch' => 'Название отдела',
            'include_square' => 'Учитывать при подсчете общей площади',
            'window_count' => 'Количество оконных проемов',
            'auditorium_type' => 'Auditorium Type',
        ];
    }

    /**
     * Gets query for [[LegacyResponsibles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLegacyResponsibles()
    {
        return $this->hasMany(LegacyResponsible::class, ['auditorium_id' => 'id']);
    }

    /**
     * Gets query for [[LocalResponsibilities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocalResponsibilities()
    {
        return $this->hasMany(LocalResponsibility::class, ['auditorium_id' => 'id']);
    }
}
