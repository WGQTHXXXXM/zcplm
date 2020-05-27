<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Approver */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="col-md-6">
<div class="approver-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'department')->textInput(['maxlength' => true]) ?>

    <div class="hide">
        <?php
            if(empty($_GET['typeid']))
                $arrTemp = [];
            else
                $arrTemp = ['value'=>$_GET['typeid']];


            echo $form->field($model, 'type')->textInput($arrTemp);
        ?>
    </div>

    <?= $form->field($model, 'user_id')->widget(Select2::className(),[
        'data' => $userDate,
        'options' => ['placeholder' => '请选择审批人 ...'],
        'pluginOptions' => [
            //硬件
            'tokenSeparators' => [',', ' '],
            'maximumInputLength' => 10
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '保存' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>