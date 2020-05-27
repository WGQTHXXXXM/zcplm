<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model frontend\models\Attachments */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attachments-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'material_id')->dropDownList(ArrayHelper::map($materials, 'material_id', 'mfr_part_number')) ?>

    <?= $form->field($model, 'attachment_url')->textInput(['maxlength' => true]) ?>

    <!--?= $form->field($model, 'version')->textInput() ?-->

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
