<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\EcnSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecn-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'serial_number') ?>

    <?= $form->field($model, 'reason') ?>

    <?= $form->field($model, 'detail') ?>

    <?= $form->field($model, 'module') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'change_now') ?>

    <?php // echo $form->field($model, 'stock_processing') ?>

    <?php // echo $form->field($model, 'affect_stock') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'ecr_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
