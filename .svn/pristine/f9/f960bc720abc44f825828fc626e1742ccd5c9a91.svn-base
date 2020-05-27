<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VersionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '系统版本';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="version-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('增加版本', ['create'], ['class' => 'btn btn-success']).'&emsp;&emsp;&emsp;&emsp;' ?>
        <?= Html::a('群发邮件通知', ['notice'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'version_number',
            'content:ntext',
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date('Y-m-d  H:i:s',$model->created_at);
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'操作',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
