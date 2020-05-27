<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "ec_approval".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $ec_id
 * @property integer $approver1
 * @property integer $approver2
 * @property integer $approver3
 * @property integer $approver4dcc
 */
class EcApproval extends \yii\db\ActiveRecord
{
    const TYPE_ECR = 1;
    const TYPE_ECN = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ec_approval';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'ec_id', 'approver1', 'approver2', 'approver3', 'approver4dcc'], 'required'],
            [['type', 'ec_id', 'approver1', 'approver2', 'approver3', 'approver4dcc'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('material', 'ID'),
            'type' => Yii::t('material', '是ECR还是ECN'),
            'ec_id' => Yii::t('material', 'ecr和ecn的id'),
            'approver1' => Yii::t('material', '部门内一级审批人'),
            'approver2' => Yii::t('material', '部门内二级审批人'),
            'approver3' => Yii::t('material', '审批团'),
            'approver4dcc' => Yii::t('material', 'dcc审批'),
        ];
    }
}
