<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "document_in".
 *
 * @property int $id
 * @property int $local_number
 * @property int|null $local_postfix
 * @property string $local_date
 * @property string|null $real_number
 * @property string $real_date
 * @property int|null $correspondent_id
 * @property int|null $position_id
 * @property int|null $company_id
 * @property string $document_theme
 * @property int|null $signed_id
 * @property string|null $target
 * @property int|null $get_id
 * @property int|null $send_method
 * @property int $creator_id
 * @property int|null $last_edit_id
 * @property string|null $key_words
 * @property int|null $need_answer
 *
 * @property Company $company
 * @property PeopleStamp $correspondent
 * @property User $creator
 * @property User $get
 * @property User $lastEdit
 * @property Position $position
 * @property PeopleStamp $signed
 */
class DocumentIn extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_in';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['local_number', 'local_date', 'real_date', 'document_theme'], 'required'],
            [['local_number', 'local_postfix', 'correspondent_id', 'position_id', 'company_id', 'signed_id', 'get_id', 'send_method', 'creator_id', 'last_edit_id', 'need_answer'], 'integer'],
            [['local_date', 'real_date'], 'safe'],
            [['real_number'], 'string', 'max' => 64],
            [['document_theme', 'target'], 'string', 'max' => 256],
            [['key_words'], 'string', 'max' => 512],
            [['correspondent_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['correspondent_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::class, 'targetAttribute' => ['position_id' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['signed_id' => 'id']],
            [['get_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['get_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['last_edit_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'local_number' => 'Local Number',
            'local_postfix' => 'Local Postfix',
            'local_date' => 'Дата документа',
            'real_number' => 'Real Number',
            'real_date' => 'Дата входящего документа',
            'correspondent_id' => 'Correspondent ID',
            'position_id' => 'Position ID',
            'company_id' => 'Company ID',
            'document_theme' => 'Тема документа',
            'signed_id' => 'Signed ID',
            'target' => 'Target',
            'get_id' => 'Get ID',
            'send_method' => 'Send Method',
            'creator_id' => 'Creator ID',
            'last_edit_id' => 'Last Edit ID',
            'key_words' => 'Key Words',
            'need_answer' => 'Need Answer',
        ];
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    /**
     * Gets query for [[Correspondent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCorrespondent()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'correspondent_id']);
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[Get]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGet()
    {
        return $this->hasOne(User::class, ['id' => 'get_id']);
    }

    /**
     * Gets query for [[LastEdit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLastEdit()
    {
        return $this->hasOne(User::class, ['id' => 'last_edit_id']);
    }

    /**
     * Gets query for [[Position]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosition()
    {
        return $this->hasOne(Position::class, ['id' => 'position_id']);
    }

    /**
     * Gets query for [[Signed]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSigned()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'signed_id']);
    }

    /**
     * * Gets query for [[InOutDocument]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInOutDocument()
    {
        return $this->hasOne(InOutDocuments::class, ['document_in_id' => 'id']);
    }
}
