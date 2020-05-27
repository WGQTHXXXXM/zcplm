<?php

use yii\widgets\DetailView;
use frontend\models\ModifyMaterial;
use kartik\grid\GridView;
use yii\helpers\Html;

//用part_name判断是不是电子物料
$isElecMtr = true;
if(!empty($model->part_name))
    $isElecMtr = false;



/* @var $this yii\web\View */
/* @var $model frontend\models\ModifyMaterial */
/* @var $mdlUserTask frontend\models\UserTask */

$this->title = $model->zc_part_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Modify Materials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modify-material-view">

    <h1>
        <?php
        echo $this->title;
        ?>

    </h1>


    <?= DetailView::widget([
        'model' => $model,
        'options'=>['class'=>'table table-striped table-bordered detail-view table-hover'],
        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '','datetimeFormat' => 'php:Y-m-d H:i:s',],
        'attributes' => [
            'mfr_part_number',
            [
                'attribute' => 'manufacturer2_id',
                'label'=>'二供智车料号',
                'value' => empty($model->manufacturer2)?"":$model->manufacturer2->zc_part_number,
            ],
            [
                'attribute' => 'manufacturer2_id',
                'value' => empty($model->manufacturer2)?"":$model->manufacturer2->mfr_part_number,
            ],
            [
                'attribute' => 'manufacturer3_id',
                'label'=>'三供智车料号',
                'value' => empty($model->manufacturer3)?"":$model->manufacturer3->zc_part_number,
            ],
            [
                'attribute' => 'manufacturer3_id',
                'value' => empty($model->manufacturer3)?"":$model->manufacturer3->mfr_part_number,
            ],
            [
                'attribute' => 'is_first_mfr',
                'value' => $model->is_first_mfr==1?"可以是一供":"不可以是一供",
                'visible'=>$isElecMtr?true:false,
            ],
            'description',
            [
                'attribute'=>'part_name',
                'visible'=>$isElecMtr?false:true,
            ],
            [
                'attribute'=>'unit',
                'visible'=>$isElecMtr?false:true,
            ],
            [
                'attribute'=>'car_number',
                'visible'=>$isElecMtr?false:true,
            ],
            [
                'attribute'=>'pcb_footprint',
                'visible'=>$isElecMtr?true:false,
            ],
            'date_entered',
            'purchase_level',

            [
                'attribute' => 'manufacturer',
                'value' => empty($model->manufacturer1)?"":$model->manufacturer1->name,
            ],
            'zc_part_number',
            [
                'attribute' => 'vehicle_standard',
                'value' => key_exists($model->vehicle_standard,ModifyMaterial::VEHICLE_STANDARD)?ModifyMaterial::VEHICLE_STANDARD[$model->vehicle_standard]:"",
                'visible'=>$isElecMtr?true:false,
            ],
            //元件类型
            [
                'attribute' => 'part_type',
                'value' => htmlspecialchars_decode($model->partType->name),
            ],
            [
                'attribute'=>'value',
                'visible'=>$isElecMtr?true:false,
            ],
            [
                'attribute'=>'schematic_part',
                'visible'=>$isElecMtr?true:false,
            ],
            [
                'attribute' => 'recommend_purchase',
                'value' => isset($model->recommend_purchase)?ModifyMaterial::RECOMMEND_PURCHASE[$model->recommend_purchase]:'',
            ],
            'remark',
            [
                'attribute' => 'parent_id',
                'label'=>'系列料号',
                'value' => isset($model->parentId)?$model->parentId->zc_part_number:'',
            ],
//            'price',
//            'lead_time',
//            'minimum_packing_quantity',
            [
                'attribute'=>'approver1',
                'value'=>empty($model->approver1)?'':$model->approver1,

            ],
            [
                'attribute'=>'approver2',
                'value'=>empty($model->approver2)?'':$model->approver2
            ],
            [
                'attribute'=>'approver3dcc',
                'value'=>empty($model->approver3dcc)?'':$model->approver3dcc
            ],
            [
                'attribute'=>'approver3purchase',
                'value'=>empty($model->approver3purchase)?'':$model->approver3purchase
            ]
        ],
    ]) ?>

    <div>
        <h1>附件</h1>
        <?php
        echo '<h3>已有的</h3>';
        echo GridView::widget([
            'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
            'dataProvider' => $dataAttachmentOld,
            'striped'=>true,
            'bordered'=>true,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn','headerOptions'=>['style'=>"width: 3.41%;"],],
                [
                    'attribute' => 'file_class_name',
                    'headerOptions'=>['style'=>"width: 15.76%;"],
                ],
                [
                    'attribute' => 'name',
                    'value' => function($model) {
                        $filename = $model->name;
                        return Html::a($filename, ['modify-material/download', 'pathFile' => $model->path, 'filename' => $filename]);
                    },
                    'format'=>'raw',
                    'headerOptions'=>['style'=>"width: 32.17%;"],
                ],
                [
                    'attribute' => 'version',
                    'headerOptions'=>['style'=>"width: 5.07%;"],
                ],
                [
                    'attribute' => 'remark',
                    'headerOptions'=>['style'=>"width: 35.03%;"],
                ],
                [
                    'attribute' => 'updated_at',
                    'value' => function($model)
                    {
                        return date('Y-m-d H:i:s',$model->updated_at);
                    },
                    'headerOptions'=>['style'=>"width: 12.26%;"],
                ],
            ],
        ]);
        if(!empty($dataAttachmentNew)){
            echo '<h3>新上传的</h3>';
            echo GridView::widget([
                'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
                'dataProvider' => $dataAttachmentNew,
                'striped'=>true,
                'bordered'=>true,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn','headerOptions'=>['style'=>"width: 3.41%;"],],
                    [
                        'attribute' => 'file_class_name',
                        'headerOptions'=>['style'=>"width: 15.76%;"],
                    ],
                    [
                        'attribute' => 'name',
                        'value' => function($model) {
                            $filename = $model->name;
                            return \yii\helpers\Html::a($filename, ['modify-material/download', 'pathFile' => $model->path, 'filename' => $filename]);
                        },
                        'format'=>'raw',
                        'headerOptions'=>['style'=>"width: 32.17%;"],
                    ],
                    [
                        'attribute' => 'version',
                        'headerOptions'=>['style'=>"width: 5.07%;"],
                    ],
                    [
                        'attribute' => 'remark',
                        'headerOptions'=>['style'=>"width: 30%;"],
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function($model)
                        {
                            return date('Y-m-d H:i:s',$model->updated_at);
                        },
                        'headerOptions'=>['style'=>"width: 12.26%;"],
                    ],
                ],
            ]);

        }

        ?>
    </div>

    <?php
    require('../views/layouts/view-approve.php');

    ?>
</div>