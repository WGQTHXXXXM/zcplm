<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "version".
 *
 * @property integer $id
 * @property string $version_number
 * @property string $content
 * @property integer $created_at
 */
class Version extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version_number', 'content', 'created_at'], 'required'],
            [['content'], 'string'],
            [['created_at'], 'integer'],
            [['version_number'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version_number' => '版本号',
            'content' => '升级内容',
            'created_at' => '版本时间',
        ];
    }
}
