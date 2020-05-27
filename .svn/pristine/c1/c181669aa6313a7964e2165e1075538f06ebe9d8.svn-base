<?php

namespace frontend\components;

use Yii;
use yii\rbac\Rule;
use frontend\models\FileAttachments;
use frontend\models\Approvals;
use frontend\models\ProjectProcess;

class FileAttachmentsUploadRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'file_attachments_upload_rule';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;
        $file_id = isset($params['file_id']) ? $params['file_id'] : null;
        if (!$project_id || !$file_id) {
            return false;
        }

        $count = FileAttachments::find()->where(['file_id' => $file_id])->count();
        // 如果还没有上传文件
        if (!$count) {
            // 检索里程碑
            $sql = "SELECT id, root, lft, rgt, lvl, name FROM project_process WHERE root='$project_id' AND lvl=1 ORDER BY lft";
            $project_process_milestones = ProjectProcess::findBySql($sql)->asArray()->all();
            // 检索当前叶子节点的单一路径
            $sql = "SELECT parent.id, parent.lft, parent.name 
                    FROM project_process AS node, 
                    project_process AS parent 
                    WHERE node.root='$project_id' AND parent.root='$project_id' AND node.lft BETWEEN parent.lft AND parent.rgt 
                    AND node.id = '$file_id' 
                    ORDER BY parent.lft";
            $project_process_current = ProjectProcess::findBySql($sql)->asArray()->all();

            // 如果本节点文件属于第一个里程碑,则有权限上传
            $current_node_milestone = next($project_process_current)['name'];
            if ($current_node_milestone === current($project_process_milestones)['name']) {
                return true;
            }

            // 否则找到前一个里程碑，并检索该里程碑所有叶子节点,即文件节点信息
            do {
                next($project_process_milestones);
            } while ($current_node_milestone !== current($project_process_milestones)['name']);
            $prev_milestone_lft = prev($project_process_milestones)['lft'];

            $current_milestone_lft = current($project_process_current)['lft'];
            $sql = "SELECT id, root, lft, rgt, lvl, name FROM project_process WHERE root='$project_id' AND rgt=lft+1 HAVING lft>={$prev_milestone_lft} AND lft<{$current_milestone_lft} ORDER BY lft";
            $project_processes = ProjectProcess::findBySql($sql)->asArray()->all();

            // 判断节点文件
            foreach ($project_processes as $project_process) {
                $current_file_id = $project_process['id'];
                $count = FileAttachments::find()->where(['file_id' => $current_file_id])->count();

                // 如果节点还没有上传文件，则本节点文件没有权限上传
                if (!$count) {
                    return false;
                }

                // 如果节点文件审批人中的任何一人不是审批同意，则本节点文件没有权限上传
                $approvals = Approvals::find()->joinWith('fileAttachment')->where(['file_attachments.file_id' => $current_file_id, 'file_attachments.version' => $count])->all();
                foreach ($approvals as $approval) {
                    if ($approval->status !== Approvals::STATUS_APPROVED) {
                        return false;
                    }
                }
            }

            // 如果前一节点文件已上传，且所以文件审批人均审批同意，则本节点文件有权限上传
            return true;
        }

        // 如果有上传文件，且文件审批人中的任何一人审批拒绝，则可以再次上传新版文件
        $approvals = Approvals::find()->joinWith('fileAttachment')->where(['file_attachments.file_id' => $file_id, 'file_attachments.version' => $count])->all();
        foreach ($approvals as $approval) {
            if ($approval->status === Approvals::STATUS_REJECTED) {
                return true;
            }
        }

        return false;
    }
}
