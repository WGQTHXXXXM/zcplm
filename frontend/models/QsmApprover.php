<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "qsm_approver".
 *
 * @property integer $qsm_id
 * @property integer $lvl
 * @property integer $department_id
 */
class QsmApprover extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qsm_approver';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['qsm_id', 'lvl', 'department_id'], 'required'],
            [['qsm_id', 'lvl', 'department_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'qsm_id' => Yii::t('material', '关联质量体系文件的id'),
            'lvl' => Yii::t('material', '审批等级'),
            'department_id' => Yii::t('material', '关联部门的id'),
        ];
    }

    public function saveSelf($qsm_id)
    {
        $postDate = $_POST['department_id'];
        foreach ($postDate as $key=>$lvl){
            $key++;
            foreach ($lvl as $value){
                $mdl = new QsmApprover();
                $mdl->department_id = $value;
                $mdl->lvl = $key;
                $mdl->qsm_id = $qsm_id;
                if(!$mdl->save())
                    return false;
            }
        }
        return true;
    }


}
