<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\ModifyMaterial */

$this->title = '物料升级';
$this->params['breadcrumbs'][] = '物料升级';
?>
<div class="modify-material-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'class1' => $class1,
        'class2' => $class2,
        'manufacturer'=>$manufacturer,
        'dataDropMsg'=>$dataDropMsg,
        'dataUser'=>$dataUser,
        'dataAttachment'=>$dataAttachment,
        'fileClassName'=>$fileClassName,
        'allMtr'=>$allMtr


    ]) ?>

</div>
