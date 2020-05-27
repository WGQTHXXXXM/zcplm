<?php

namespace frontend\components;

use Yii;
use yii\rbac\Rule;
use frontend\models\FileAttachments;

class ApprovalsEditApprovalRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'approvals_edit_approval_rule';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        if (!isset($_POST['hasEditable'])) {
            return false;
        }

        // instantiate Approvals model for saving
        $ids = Yii::$app->request->post('editableKey');
        $ids = json_decode($ids, true);

        // 如果提交审批结果的人不是预先设定的审批人，则没有权限提交
        if ($user !== $ids['approver_id']) {
            return false;
        }

        // 如果审批人提交的审批不是当前最新版本的审批，则没有权限提交
        $model = FileAttachments::findOne($ids['file_attachment_id']);
        $count = FileAttachments::find()->where(['file_id' => $model->file_id])->count();
        if ($model->version != $count) {
            return false;
        }

        return true;
    }
}
