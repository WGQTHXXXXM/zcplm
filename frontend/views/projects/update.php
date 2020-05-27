<?php


use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Projects */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Projects',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="projects-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
