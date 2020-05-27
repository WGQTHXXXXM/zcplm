<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Ecr */
/* @var $dataUser frontend\models\Ecr */
/* @var $preview frontend\models\Ecr */
/* @var $previewCfg frontend\models\Ecr */


$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'ECR',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Ecrs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="ecr-update">

    <h1><?php //echo Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'preview'=>$preview,
        'previewCfg'=>$previewCfg,
        'dataMtrDescription'=>$dataMtrDescription,
        'dataMtrPartNo'=>$dataMtrPartNo,
        'pjtName'=>$pjtName,
        'projectProcess'=>$projectProcess,
        'dataUser'=>$dataUser,
        'arrEffectRange'=>$arrEffectRange
    ]) ?>

</div>
