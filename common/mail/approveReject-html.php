<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$fileLink = Yii::$app->urlManager->createAbsoluteUrl([$link]);
?>
<div class="project-file-upload">
    <p>Hello,</p>

    <?php
    if($author == 1)
        echo Html::encode(Yii::$app->user->identity->username.'拒绝了你的任务'.$taskType.$taskName);
    else
        echo Html::encode(Yii::$app->user->identity->username.'审批拒绝'.$taskType.$taskName.'这个任务，你不用再审批了');
    ?>
    <br/>
    <br/>
    <?php
    if($author == 1)
        echo '链接:'.Html::a(Html::encode($fileLink), $fileLink);
    ?>
    <br />
    <br />
    <br />
    <p><i>Note: This email is sent by <?= Html::encode(Yii::$app->name . ' robot') ?> automatically. Please don't reply this email directly.</i></p>
</div>