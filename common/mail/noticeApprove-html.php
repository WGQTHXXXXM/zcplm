<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$fileLink = Yii::$app->urlManager->createAbsoluteUrl([$link]);
if($taskUser == null)
    $taskUser = Yii::$app->user->identity->username;
?>
<div class="project-file-upload">
    <p>Hello,</p>
    <?= Html::encode($taskUser.'有个'.$taskType.'任务：'.$taskName.'等待你审批') ?>
    <br/>
    <br/>
    链接: <?= Html::a(Html::encode($fileLink), $fileLink) ?>
    <br />
    <br />
    <br />
    <p><i>Note: This email is sent by <?= Html::encode(Yii::$app->name . ' robot') ?> automatically. Please don't reply this email directly.</i></p>
</div>
