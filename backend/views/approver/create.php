<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Approver */

$this->title = '创建审批人';
$this->params['breadcrumbs'][] = ['label' => '审批人', 'url' => ['index?typeid='.$_GET['typeid']]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="approver-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'userDate'=>$userDate,
    ]) ?>

</div>
