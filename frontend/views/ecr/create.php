<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Ecr */

$this->title = Yii::t('common', '新建ECR');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'ECR'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecr-create">

    <h1><?php //echo Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'dataMtrDescription'=>$dataMtrDescription,
        'dataMtrPartNo'=>$dataMtrPartNo,
        'pjtName'=>$pjtName,
        'dataUser'=>$dataUser,
        'arrEffectRange'=>$arrEffectRange

    ]) ?>

</div>
