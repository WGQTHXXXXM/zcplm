<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Department */

$this->title = '更新部门: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Departments', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="department-update">

    <br>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
