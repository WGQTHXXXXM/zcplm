<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model frontend\models\Materials */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Materials',
]) . $model->material_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Materials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->material_id, 'url' => ['view', 'id' => $model->material_id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="materials-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'class1' => $class1,
        'class2' => $class2,
        'manufacturer'=>$manufacturer,
        'dataDropMsg'=>$dataDropMsg,
    ]) ?>

</div>
