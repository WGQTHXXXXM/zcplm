<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "pjt_pcs_temp_approver".
 *
 * @property integer $ppt_id
 * @property integer $lvl
 * @property integer $department_id
 */
class PjtPcsTempApprover extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pjt_pcs_temp_approver';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ppt_id', 'lvl', 'department_id'], 'required'],
            [['ppt_id', 'lvl', 'department_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ppt_id' => Yii::t('material', '关联APQP清单的id'),
            'lvl' => Yii::t('material', '审批等级'),
            'department_id' => Yii::t('material', '关联部门的id'),
        ];
    }
}
