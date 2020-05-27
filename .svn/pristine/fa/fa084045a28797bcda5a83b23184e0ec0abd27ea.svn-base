<?php

namespace backend\models;

use frontend\models\Tasks;
use Yii;

/**
 * This is the model class for table "approver".
 *
 * @property integer $user_id
 * @property integer $type
 * @property string $department
 * @property integer $id
 */
class Approver extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'approver';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'department'], 'required'],
            [['user_id', 'type'], 'integer'],
            [['department'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '审批人',
            'type' => 'Type',
            'department' => '部门',
            'id' => 'ID',
        ];
    }


////////////关联表/////////////////
    public function getUserName()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }




}
