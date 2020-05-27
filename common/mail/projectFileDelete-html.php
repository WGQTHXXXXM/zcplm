<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

?>
<div class="project-file-delete">
    <p>Hello,</p>

    <?= Html::encode(Yii::$app->user->identity->username) ?> deleted the project file.
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
    <br />
    <br />
    <p><i>Note: This email is sent by <?= Html::encode(Yii::$app->name . ' robot') ?> automatically. Please don't reply this email directly.</i></p>
</div>
