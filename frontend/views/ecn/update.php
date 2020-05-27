<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Ecn */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'ECN',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Ecns'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="ecn-update">

    <h1><?php //echo Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'changeSet'=>$changeSet,
        'dataUser'=>$dataUser,
    ]) ?>

</div>
