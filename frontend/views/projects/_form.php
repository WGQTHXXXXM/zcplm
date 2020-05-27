<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model frontend\models\Projects */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="row">
    <div class="col-md-6 projects-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?php
        if(!empty($model->created_at)){
            $model->created_at = date('Y年m月d日',$model->created_at);
            $model->end_at = date('Y年m月d日',$model->end_at);
        }
        echo $form->field($model, 'created_at')->widget(DatePicker::classname(),[
            'options' => ['placeholder' => '选择创建时间 ...'],
            'readonly'=>true,
            'pluginOptions' => [
                'todayHighlight' => true,
                'autoclose'=>true,
            ],
        ]);
        echo $form->field($model, 'end_at')->widget(DatePicker::classname(),[
            'options' => ['placeholder' => '选择结束时间 ...'],
            'readonly'=>true,
            'pluginOptions' => [
                'todayHighlight' => true,
                'autoclose'=>true,
            ]
        ]);

        ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
