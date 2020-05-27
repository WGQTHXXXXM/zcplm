<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "department_user".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $department_id
 */
class DepartmentUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'department_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'department_id'], 'required'],
            [['user_id', 'department_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'department_id' => 'Department ID',
        ];
    }
}
