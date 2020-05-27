<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\ProjectAttachment */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Project Attachment',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Project Attachments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="project-attachment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
