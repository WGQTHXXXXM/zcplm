<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$fileLink = Yii::$app->urlManager->createAbsoluteUrl(['tasks/index']);
?>
<div class="project-file-upload">
    <p>Hello,</p>

    <?= Html::encode(Yii::$app->user->identity->username) ?> 拒绝了你的任务<?= Html::encode($task) ?>.
    <br/>
    <br/>
    链接: <?= Html::a(Html::encode($fileLink), $fileLink) ?>
    <br />
    <br />
    <br />
    <p><i>Note: This email is sent by <?= Html::encode(Yii::$app->name . ' robot') ?> automatically. Please don't reply this email directly.</i></p>
</div>
