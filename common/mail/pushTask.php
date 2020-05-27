<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
//$fileLink = Yii::$app->urlManager->createAbsoluteUrl([$link]);
?>
<div class="project-file-upload">
    <p>Hello,</p>

    <?= Html::encode('PLM系统你的任务任务被退回了，你还没有处理。快去解决吧。') ?>
    <br/>
    <br/>
    <br />
    <br />
    <br />
    <p><i>Note: This email is sent by <?= Html::encode(Yii::$app->name . ' robot') ?> automatically. Please don't reply this email directly.</i></p>
</div>
