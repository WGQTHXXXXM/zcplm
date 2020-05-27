<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "dload_pms_user".
 *
 * @property integer $type
 * @property integer $type_id
 * @property integer $user_id
 */
class DloadPmsUser extends \yii\db\ActiveRecord
{
    const TYPE_QSM=1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dload_pms_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'type_id', 'user_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => Yii::t('material', '对应的类型'),
            'type_id' => Yii::t('material', '对应的类型的id'),
            'user_id' => Yii::t('material', '允许的用户'),
        ];
    }

    /**
     * 根据传进来的属性保存
     */
    public function saveSelf($type,$type_id,$user_id)
    {
        $this->type = $type;
        $this->type_id = $type_id;
        $this->user_id = $user_id;
        if($this->save())
            return true;
        return false;
    }
}
