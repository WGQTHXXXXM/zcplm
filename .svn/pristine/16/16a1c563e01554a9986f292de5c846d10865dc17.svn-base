<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\QualitySystemManageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quality-system-manage-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'parent_name') ?>

    <?= $form->field($model, 'son_name') ?>

    <?= $form->field($model, 'department_belong_id') ?>

    <?= $form->field($model, 'file_code') ?>

    <?php // echo $form->field($model, 'file_class') ?>

    <?php // echo $form->field($model, 'status_submit') ?>

    <?php // echo $form->field($model, 'visible') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
