<?php
use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

echo GridView::widget([
    'dataProvider' => $data,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'hover' => true,
    'columns' => [
        'version',
        [
            'attribute'=>'name',
            'format'=>'raw',
            'value'=>function($model){
                return Html::a($model->name,Url::to(['modify-material/download', 'pathFile' => $model->path,
                    'filename' => $model->name]));
            }
        ],
        [
            'attribute'=>'updated_at',
            'value'=>function($model){
                return date('Y-m-d  H:i:s',$model->updated_at);
            }
        ],
    ]
]);


?>