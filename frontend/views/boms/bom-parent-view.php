<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Materials */

$this->title = 'BOMS_PARENT';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    th{
        width: 25%;
    }
</style>
<div class="materials-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div style="width: 550px">
        <?= DetailView::widget([
            'options' => ['class' => 'table table-striped table-bordered detail-view table-hover','style'=>'word-wrap: break-word;word-break: break-all;'],
            'model' => $model,
            'attributes' => [
                'material.zc_part_number',
                'material.description',
                'material.pcb_footprint',
                'parent_version',
            ],
        ]);
        ?>
    </div>
</div>
