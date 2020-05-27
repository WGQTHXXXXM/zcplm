<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use mdm\admin\models\User;

/**
 * This is the model class for table "approvals".
 *
 * @property integer $file_attachment_id
 * @property integer $approver_id
 * @property integer $status
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property FileAttachments $fileAttachment
 * @property User $approver
 */
class Approvals extends \yii\db\ActiveRecord
{
    const STATUS_UNAPPROVED = 0; //未审批
    const STATUS_APPROVING = 10; //审批中
    const STATUS_REJECTED = 20; //审批拒绝
    const STATUS_APPROVED = 30; //审批同意

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'approvals';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_attachment_id', 'approver_id', 'created_at', 'updated_at'], 'required'],
            [['file_attachment_id', 'approver_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['remark'], 'string'],
            [['file_attachment_id'], 'exist', 'skipOnError' => true, 'targetClass' => FileAttachments::className(), 'targetAttribute' => ['file_attachment_id' => 'file_attachment_id']],
            [['approver_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approver_id' => 'id']],
            ['status', 'default', 'value' => self::STATUS_UNAPPROVED],
            ['status', 'in', 'range' => [self::STATUS_UNAPPROVED, self::STATUS_APPROVING, self::STATUS_APPROVED, self::STATUS_REJECTED]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file_attachment_id' => Yii::t('common', 'File Attachment ID'),
            'approver_id' => Yii::t('common', 'Approver'),
            'status' => Yii::t('common', 'Status'),
            'remark' => Yii::t('common', 'Remark'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFileAttachment()
    {
        return $this->hasOne(FileAttachments::className(), ['file_attachment_id' => 'file_attachment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprover()
    {
        return $this->hasOne(User::className(), ['id' => 'approver_id'])->from(['u2' => User::tableName()]);
    }
}
