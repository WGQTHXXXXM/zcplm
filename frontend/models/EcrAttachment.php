<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "ecr_attachment".
 *
 * @property integer $id
 * @property integer $ecr_id
 * @property string $path
 * @property string $name
 * @property integer $updated_at
 */
class EcrAttachment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecr_attachment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ecr_id', 'path', 'name', 'updated_at'], 'required'],
            [['ecr_id', 'updated_at'], 'integer'],
            [['path', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('material', 'ID'),
            'ecr_id' => Yii::t('material', 'ecr的id号'),
            'path' => Yii::t('material', '存储路径'),
            'name' => Yii::t('material', '文件名'),
            'updated_at' => Yii::t('material', '更新时间'),
        ];
    }
}
