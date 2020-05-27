<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\Projects;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model frontend\models\Modules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="modules-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
//    $session = Yii::$app->session;
//    if (!$session->isActive) $session->open();
    
    $data = Projects::find()->select(['name', 'project_id'])->indexBy('project_id')->all();
//    if(isset($session['project_id'])) $model->project_id = $session['project_id'];
    ?>
    <?= $form->field($model, 'project_id')->dropDownList(ArrayHelper::map($data, 'project_id', 'name'),['prompt'=>'请选择项目名称 ...']) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'category')->dropDownList([
        '电子' => '电子',
        '结构' => '结构'
    ]) ?>

    <?= $form->field($model, 'milestone')->textInput(['maxlength' => true]) ?>

    <!--?= $form->field($model, 'produce_qty')->textInput() ?-->

    <!--?= $form->field($model, 'date_entered')->textInput() ?-->

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
