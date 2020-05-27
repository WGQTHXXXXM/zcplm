<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\ModifyMaterial */

$this->title = Yii::t('material', 'Create Materials');
$this->params['breadcrumbs'][] = ['label' => Yii::t('material', 'Materials'), 'url' => ['/materials/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modify-material-create">

    <h1 style="display: inline">    <div class="row">
            <div class="col-md-3">
                <?= Html::encode($this->title) ?>
            </div>
            <div class="col-md-6">
                <input type="button" class="btn btn-success download-temp" value="下载模板"><br>
            </div>
        </div> </h1>

    <?= $this->render('_form', [
        'model' => $model,
        'class1' => $class1,
        'dataUser'=>$dataUser,
        'fileClassName'=>$fileClassName,
        'allMtr'=>$allMtr,
        'dataAttachment'=>'""',
    ]) ?>

</div>
