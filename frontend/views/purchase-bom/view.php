<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */

$this->title = Yii::t('common', 'Purchase-bom/view');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Purchase-bom'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="Purchase-bom-view">
    <h1><!--?= Html::encode($this->title) ?--></h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
      //  'headerRowOptions'=>['class'=>'kartik-sheet-style'],
      //  'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'showPageSummary'=>true,
        'floatHeader' => true,
      //  'floatHeaderOptions'=>['scrollingTop'=>'0'],
      //  'perfectScrollbar' => true,
      //  'striped' => true,
        'export' => false,
        'toggleData' => false,
        'hover'=>true,
        'responsive'=>false,
      //  'pjax' => true,
        'resizableColumns'=>true,
        'persistResize'=>true,
      //  'resizeStorageKey'=>Yii::$app->user->id . '-' . date("m"),
        'panel' => ['type' => 'success', 'heading' => $heading, 'footer' => false],
    ]); ?>
</div>
