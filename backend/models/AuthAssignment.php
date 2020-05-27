<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "auth_assignment".
 *
 * @property string $item_name
 * @property string $user_id
 * @property integer $created_at
 *
 * @property AuthItem $itemName
 */
class AuthAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'],
            [['created_at'], 'integer'],
            [['item_name', 'user_id'], 'string', 'max' => 64],
            [['item_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['item_name' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_name' => Yii::t('user', 'Item Name'),
            'user_id' => Yii::t('user', 'User ID'),
            'created_at' => Yii::t('user', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemName()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'item_name']);
    }
    public function getUser()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    /**
     * 获得角色的所有人
     */
    public static function getRoleUser($id)
    {
        $arrUser = AuthAssignment::find()->leftJoin('user','user.id=auth_assignment.user_id')
            ->where(['auth_assignment.item_name'=>$id,'user.status'=>User::STATUS_ACTIVE])
            ->select('user.username as username,user.id as id')->indexBy('id')->column();

        return $arrUser;
    }

}
