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
class Aaa extends \yii\db\ActiveRecord
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
        return 'aaa';
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

}
