<?php

namespace common\models\scaffold;

use common\models\work\UserWork;
use common\repositories\general\PeopleStampRepository;
use frontend\models\work\general\PeopleStampWork;

/**
 * This is the model class for table "document_order".
 *
 * @property int $id
 * @property int|null $order_copy_id
 * @property string|null $order_number
 * @property int|null $order_postfix
 * @property string|null $order_name
 * @property string|null $order_date
 * @property int|null $signed_id
 * @property int|null $bring_id
 * @property int|null $executor_id
 * @property string|null $key_words
 * @property int|null $creator_id
 * @property int|null $last_edit_id
 * @property int|null $type
 * @property int|null $state
 * @property int|null $nomenclature_id
 * @property int|null $study_type
 * @property int|null $preamble
 *
 * @property PeopleStampWork $bring
 * @property UserWork $creator
 * @property PeopleStampWork $executor
 * @property UserWork $lastEdit
 * @property LegacyResponsible[] $legacyResponsibles
 * @property PeopleStampWork $signed
 */
class DocumentOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_copy_id', 'order_postfix', 'signed_id', 'bring_id', 'executor_id', 'creator_id', 'last_edit_id', 'type', 'state', 'nomenclature_id', 'study_type', 'preamble'], 'integer'],
            [['order_date'], 'safe'],
            [['order_number', 'order_name', 'key_words'], 'string', 'max' => 255],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStampWork::class, 'targetAttribute' => ['signed_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStampWork::class, 'targetAttribute' => ['executor_id' => 'id']],
            [['bring_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStampWork::class, 'targetAttribute' => ['bring_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserWork::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserWork::class, 'targetAttribute' => ['last_edit_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_copy_id' => 'Order Copy ID',
            'order_number' => 'Order Number',
            'order_postfix' => 'Order Postfix',
            'order_name' => 'Наименование приказа',
            'order_date' => 'Дата приказа',
            'signed_id' => 'Signed ID',
            'bring_id' => 'Проект вносит',
            'executor_id' => 'Кто исполняет',
            'key_words' => 'Key Words',
            'creator_id' => 'Creator ID',
            'last_edit_id' => 'Last Edit ID',
            'type' => 'Type',
            'state' => 'State',
            'nomenclature_id' => 'Nomenclature ID',
            'study_type' => 'Study Type',
            'preamble' => 'Preamble'
        ];
    }

    /**
     * Gets query for [[Bring]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBring()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'bring_id']);
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(UserWork::class, ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[LastEdit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLastEdit()
    {
        return $this->hasOne(UserWork::class, ['id' => 'last_edit_id']);
    }

    /**
     * Gets query for [[LegacyResponsibles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLegacyResponsibles()
    {
        return $this->hasMany(LegacyResponsible::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[Signed]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSigned()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'signed_id']);
    }
}
