<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VersionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '系统版本';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="version-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <div class="col-md-9">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'hover'=>true,
        'striped'=>false,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '版本',
        ],
        'toolbar'=>[],
        'columns' => [
            [
                'class' => 'kartik\grid\SerialColumn',
                'contentOptions' => ['class' => 'kartik-sheet-style'],
                'width' => '60px',
                'header' => '序号',
                'headerOptions' => ['class' => 'kartik-sheet-style']
            ],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    return Yii::$app->controller->renderPartial('view', ['model' => $model]);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                'expandOneOnly' => true,
                'width' => '60px',
            ],
            [
                'attribute'=>'version_number',
                'width' => '100px',
            ],
            [
                'attribute' => 'created_at',
                'label'=>'版本时间',
                'value' => function($model){
                    return date('Y-m-d  H:i:s',$model->created_at);
                },
            ],
        ],
    ]); ?>
        </div>
    </div>

</div>
