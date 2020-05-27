<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use mdm\admin\models\User;

/**
 * This is the model class for table "file_attachments".
 *
 * @property integer $file_attachment_id
 * @property integer $file_id
 * @property integer $submitter_id
 * @property string $attachment_url
 * @property integer $version
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Approvals[] $approvals
 * @property User[] $approvers
 * @property ProjectProcess $file
 * @property User $submitter
 */
class FileAttachments extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'file_attachments';
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
            [['file_id', 'submitter_id', 'attachment_url', 'created_at', 'updated_at'], 'required'],
            [['file_id', 'submitter_id', 'version', 'created_at', 'updated_at'], 'integer'],
            [['attachment_url'], 'string', 'max' => 100],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProcess::className(), 'targetAttribute' => ['file_id' => 'id']],
            [['submitter_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['submitter_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file_attachment_id' => Yii::t('common', 'File Attachment ID'),
            'file_id' => Yii::t('common', 'File ID'),
            'submitter_id' => Yii::t('common', 'Submitter'),
            'attachment_url' => Yii::t('common', 'Attachment Url'),
            'version' => Yii::t('common', 'Version'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprovals()
    {
        return $this->hasMany(Approvals::className(), ['file_attachment_id' => 'file_attachment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprovers()
    {
        return $this->hasMany(User::className(), ['id' => 'approver_id'])->viaTable('approvals', ['file_attachment_id' => 'file_attachment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(ProjectProcess::className(), ['id' => 'file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubmitter()
    {
        return $this->hasOne(User::className(), ['id' => 'submitter_id'])->from(['u1' => User::tableName()]);
    }
}
