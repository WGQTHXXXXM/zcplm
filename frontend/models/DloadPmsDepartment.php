<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "dload_pms_department".
 *
 * @property integer $type
 * @property integer $type_id
 * @property integer $department_id
 */
class DloadPmsDepartment extends \yii\db\ActiveRecord
{
    const TYPE_QSM=1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dload_pms_department';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'type_id', 'department_id'], 'integer'],
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
            'department_id' => Yii::t('material', '允许的部门'),
        ];
    }

    /**
     * 根据传进来的属性保存
     */
    public function saveSelf($type,$type_id,$department_id)
    {
        $this->type = $type;
        $this->type_id = $type_id;
        $this->department_id = $department_id;
        if($this->save())
            return true;
        return false;
    }

}
