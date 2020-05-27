<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use kartik\dialog\Dialog;
use kartik\date\DatePicker;
use frontend\models\ModifyMaterial;

/* @var $searchModel frontend\models\MaterialsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '改采购变更级别';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="materials-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="box table-responsive">
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-striped table-bordered table-hover','style'=>'table-layout:fixed;'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'options'=>['id'=>'adminIndex'],
            'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '','datetimeFormat' => 'php:Y-m-d H:i:s',],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions'=>['width'=>'50px'],
                    "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],

                ],
                //一供智车料号
                [
                    'attribute' => 'zc_part_number',
                ],
                //一供料号
                [
                    'attribute' => 'mfr_part_number',
                    'value' => 'mfr_part_number',
                    "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
                ],
                [
                    'attribute' => 'part_name',
                    'value' => 'part_name',
                    "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
                ],
                //  'description',
                [
                    'attribute' => 'description',
                    'value' => 'description',
                    "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
                ],
                //采购推荐等级
                [
                    'class'=>'kartik\grid\EditableColumn',
                    'attribute'=>'recommend_purchase',
                    'editableOptions' => function ($model, $key, $index)
                    {
                        return [
                            'placement' => \kartik\popover\PopoverX::ALIGN_BOTTOM_RIGHT,
                            'submitButton'=>['icon'=>'<i class="glyphicon glyphicon-floppy-disk"></i>','class'=>'btn btn-sm btn-primary kv-editable-submit'],
                            'size' => 'md',
                            'formOptions' => ['action' => ['/materials/save-recommend-purchase']], // point to the new action
                            'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                            'widgetClass' =>  'kartik\select2\Select2',
                            'displayValue'=>ModifyMaterial::RECOMMEND_PURCHASE[$model->recommend_purchase],
                            'options'=>[
                                'data' => ModifyMaterial::RECOMMEND_PURCHASE,
                            ],
                            'submitOnEnter' => false,
                            'pluginEvents' => [
                                "editableSuccess"=>"function(event, val, form, data) 
                                { 
                                    location.reload();
                                }",
                            ]
                        ];
                    },
                    'filter'=>ModifyMaterial::RECOMMEND_PURCHASE,
                    "filterInputOptions" => ['style'=>"width: 90px",'class'=>'form-control'],//让列宽为90
                ],
                //厂家
                [
                    'class'=>'kartik\grid\EditableColumn',
                    'attribute'=>'manufacturer',
                    'editableOptions' => function ($model)
                    {
                        return [
                            'placement' => \kartik\popover\PopoverX::ALIGN_BOTTOM_RIGHT,
                            'submitButton'=>['icon'=>'<i class="glyphicon glyphicon-floppy-disk"></i>','class'=>'btn btn-sm btn-primary kv-editable-submit'],
                            'size' => 'md',
                            'formOptions' => ['action' => ['/materials/save-manufacturer']], // point to the new action
                            'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                            'widgetClass' =>  'kartik\select2\Select2',
                            'displayValue'=>$model->manufacturer1->name,
                            'options'=>[
                                'data' => \frontend\models\MaterialEncodeRule::findOne($model->manufacturer)
                                    ->parents(1)->one()->children()->select('name,id')->indexBy('id')->column()
                            ],
                            'submitOnEnter' => false,
                            'pluginEvents' => [
                                "editableSuccess"=>"function(event, val, form, data) 
                                { 
                                    location.reload();
                                }",
                            ]
                        ];
                    },
                    "filterInputOptions" => ['style'=>"width: 90px",'class'=>'form-control'],//让列宽为90
                ],


            ],
        ]); ?>
    </div>
</div>
