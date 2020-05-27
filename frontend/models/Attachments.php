<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "attachments".
 *
 * @property integer $attachment_id
 * @property integer $material_id
 * @property string $attachment_url
 * @property integer $version
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Materials $material
 */
class Attachments extends \yii\db\ActiveRecord
{
    public $part_no;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attachments';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_id', 'attachment_url'], 'required'],
            [['material_id', 'version'], 'integer'],
            [['attachment_url'], 'string', 'max' => 100],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => Materials::className(), 'targetAttribute' => ['material_id' => 'material_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'attachment_id' => Yii::t('common', 'Attachment ID'),
            'material_id' => Yii::t('common', 'Part No.'),
            'part_no' => Yii::t('common', 'Part No.'),
            'attachment_url' => Yii::t('common', 'Attachment Url'),
            'version' => Yii::t('common', 'Version'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(Materials::className(), ['material_id' => 'material_id']);
    }
}
