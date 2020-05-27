<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserTask */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'User Task',
]) . $model->Id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'User Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Id, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="user-task-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
