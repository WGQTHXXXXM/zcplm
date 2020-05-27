<?php

namespace frontend\components;

use Yii;
use yii\rbac\Rule;
use frontend\models\FileAttachments;
use frontend\models\Approvals;

class FileAttachmentsDeleteRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'file_attachments_delete_rule';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $file_attachment_id = isset($params['file_attachment_id']) ? $params['file_attachment_id'] : null;
        if (!$file_attachment_id) {
            return false;
        }

        $manager = Yii::$app->getAuthManager();
        $assignments = $manager->getAssignments($user);

        // 超级管理员有权删除文件
        if (array_key_exists('超级管理员', $assignments)) {
            return true;
        }

        $model = FileAttachments::findOne($file_attachment_id);
        // 如果是文件上传人，并且文件审批人中无人完成审批，则上传人有权删除文件，然后系统自动发信通知各审批人
        if ($user === $model->submitter_id) {
            $approvals = Approvals::find()->where(['file_attachment_id' => $file_attachment_id])->all();
            foreach ($approvals as $approval) {
                if ($approval->status === Approvals::STATUS_REJECTED || $approval->status === Approvals::STATUS_APPROVED) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }
}
