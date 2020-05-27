<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Modules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="modules-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'project_id')->textInput(['value' => $project->name, 'disabled' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'category')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'milestone')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'produce_qty')->textInput() ?>

    <!--?= $form->field($model, 'date_entered')->textInput() ?-->

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
