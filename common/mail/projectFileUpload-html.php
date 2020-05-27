<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$fileLink = Yii::$app->urlManager->createAbsoluteUrl(['projects/project-manage-view', 'project_id' => $project_id, 'file_id' => $file_id]);
?>
<div class="project-file-upload">
    <p>Hello,</p>

    <?= Html::encode(Yii::$app->user->identity->username) ?> uploaded project file for your approval.
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
    Link: <?= Html::a(Html::encode($fileLink), $fileLink) ?>
    <br />
    <br />
    <br />
    <p><i>Note: This email is sent by <?= Html::encode(Yii::$app->name . ' robot') ?> automatically. Please don't reply this email directly.</i></p>
</div>
