<?php
use yii\helpers\Html;
use frontend\models\Approvals;

/* @var $this yii\web\View */
/* @var $approval frontend\models\Approvals */

$fileLink = Yii::$app->urlManager->createAbsoluteUrl(['projects/project-manage-view', 'project_id' => $project_id, 'file_id' => $file_id]);
?>
<div class="project-file-approval">
    <p>Hello,</p>

    <?= Html::encode(Yii::$app->user->identity->username) ?> gave the approval result: <b><?= Html::encode($approval->status == Approvals::STATUS_APPROVED? Yii::t('common', 'Approved') : Yii::t('common', 'Rejected')) ?></b>.
    <br />
    The detail information is as follows:
    <br />
    Project: <?= Html::encode($project) ?>
    <br />
    Milestone: <?= Html::encode($milestone) ?>
    <br />
    Task: <?= Html::encode($task) ?>
    <br />
    Filename: <?= Html::encode($filename) ?>
    <br />
    Remark: <?= Html::encode($approval->remark) ?>
    <br />
    Link: <?= Html::a(Html::encode($fileLink), $fileLink) ?>
    <br />
    <br />
    <br />
    <p><i>Note: This email is sent by <?= Html::encode(Yii::$app->name . ' robot') ?> automatically. Please don't reply this email directly.</i></p>
</div>
