<?php

namespace frontend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "project_attachment".
 *
 * @property integer $id
 * @property string $path
 * @property string $name
 * @property integer $file_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $user_id
 */
class ProjectAttachment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_attachment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['path', 'name', 'file_id', 'created_at', 'updated_at', 'user_id'], 'required'],
            [['file_id', 'created_at', 'updated_at', 'user_id'], 'integer'],
            [['path'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('material', 'ID'),
            'path' => Yii::t('material', '保存的路径'),
            'name' => Yii::t('material', '文件名'),
            'file_id' => Yii::t('material', '文件id'),
            'created_at' => Yii::t('material', '创建时间'),
            'updated_at' => Yii::t('material', '更新时间'),
            'user_id' => Yii::t('material', '上传人'),
        ];
    }

    ///////////////////关联表///////////////////

    public function getUser()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getAttachment()
    {
        return $this->hasOne(ProjectProcess::className(),['id'=>'file_id']);
    }


}
