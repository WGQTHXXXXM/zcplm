<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Modules */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Modules',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Modules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->module_id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="modules-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'project' => $project,
    ]) ?>

</div>
