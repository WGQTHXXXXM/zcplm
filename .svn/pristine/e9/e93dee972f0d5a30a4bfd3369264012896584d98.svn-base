<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "material_approver".
 *
 * @property integer $id
 * @property integer $material_id
 * @property integer $approver1
 * @property integer $approver2
 * @property integer $approver3dcc
 * @property integer $approver3purchase
 */
class MaterialApprover extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material_approver';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_id', 'approver1', 'approver2', 'approver3dcc', 'approver3purchase'], 'required'],
            [['material_id', 'approver1', 'approver2', 'approver3dcc', 'approver3purchase'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('material', 'ID'),
            'material_id' => Yii::t('material', '对应的物料id'),
            'approver1' => Yii::t('material', '一级审批人'),
            'approver2' => Yii::t('material', '二级审批人'),
            'approver3dcc' => Yii::t('material', 'DCC审批'),
            'approver3purchase' => Yii::t('material', '采购审批'),
        ];
    }
}
