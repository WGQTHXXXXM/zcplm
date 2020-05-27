<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use frontend\models\QualitySystemManage;
use kartik\dialog\Dialog;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\QualitySystemManageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '文件查看';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quality-system-manage-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php
        $str = 'btn btn-success ';
        $str .= Yii::$app->authManager->checkAccess(Yii::$app->user->id, '/quality-system-manage/create')?'':'hide';
        echo Html::a('新建', ['create'], ['class' => $str]);
        $authUpdate = Yii::$app->authManager->checkAccess(Yii::$app->user->id, '/quality-system-manage/update')?'':'hide';
        $authAllVersion=Yii::$app->authManager->checkAccess(Yii::$app->user->id, '/quality-system-manage/all-version')?'':'hide';
        ?>
    </p>
    <?= GridView::widget([
        'tableOptions' => ['style'=>'table-layout:fixed;'],
        'hover'=>true,
        'panel' => [ 'heading' => "文件查看",'before'=>false,'after'=>false,'type'=>GridView::TYPE_INFO],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options'=>['id'=>'adminIndex'],
        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '','datetimeFormat' => 'php:Y-m-d H:i:s',],
        'pager' => [
            'class' => \yii\widgets\LinkPager::className(),
            'nextPageLabel' => '下一页',
            'prevPageLabel' => '上一页',
            'maxButtonCount' => 10,//显示的页数
        ],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions'=>['width'=>'30px'],
            ],

            //文件名
            [
                'attribute' => 'name',
                'value' => 'name',
                "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
                'headerOptions'=>['width'=>'250px'],
            ],
            [
                'attribute' => 'visible',
                'value' => function($model){
                    if($model->visible == 1)
                        return '显示';
                    else
                        return '不显示';
                },
                'headerOptions'=>['width'=>'100px'],
                'filter'=>[0=>'不显示',1=>'显示'],
                'visible'=>!$authUpdate
            ],
//            [
//                'attribute' => 'parent_name',
//                'value' => 'parent_name',
//                "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
//                'headerOptions'=>['width'=>'150px'],
//            ],
//            [
//                'attribute' => 'son_name',
//                'value' => 'son_name',
//                "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
//                'headerOptions'=>['width'=>'150px'],
//            ],
            [
                'attribute' => 'department_belong_id',
                'value' => function($model) use ($arrDepartment){
                    return $arrDepartment[$model->department_belong_id];
                },
                "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
                'headerOptions'=>['width'=>'180px'],
                'filter'=>$arrDepartment,
            ],
            [
                'attribute' => 'file_code',
                'value' => 'file_code',
                "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
                'headerOptions'=>['width'=>'150px'],
            ],
            [
                'attribute' => 'file_class',
                'value' => function($model){
                    return QualitySystemManage::FILE_CLASS[$model->file_class];
                },
                "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
                'headerOptions'=>['width'=>'100px'],
                'filter'=>QualitySystemManage::FILE_CLASS,
            ],
            [
                'attribute' => 'status_submit',
                'value' => function($model){
                    return QualitySystemManage::FILE_STATUS[$model->status_submit];
                },
                "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
                'headerOptions'=>['width'=>'100px'],
                'filter'=>QualitySystemManage::FILE_STATUS,
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}&emsp;{upload}&emsp;{update}&emsp;{all-version}',
                'header'=>'操作',
                'buttons' => [
                    'update'=>function ($url)use($authUpdate){
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',$url,
                            ['title'=>'更新','class'=>$authUpdate]);
                    },
                    'all-version'=>function ($url)use($authAllVersion){
                        return Html::a('<span class=" glyphicon glyphicon-sort-by-order-alt "></span>',$url,
                            ['title'=>'查看历史版本','class'=>$authAllVersion]);
                    },
                    'upload'=>function ($url,$model){
                        if($model->status_submit == QualitySystemManage::FILE_STATUS_NEED)
                            return Html::a('<span class="glyphicon glyphicon-cloud-upload upload-check"></span>',$url,
                                ['title'=>'上传']);
                        return Html::a('<span class="glyphicon glyphicon-cloud-upload upload-check"></span>','#',
                            ['title'=>'上传','onclick'=>"krajeeDialog.alert('只有需提交的状态才可以上传');"]);
                    },
                ]
            ],
        ],
    ]); ?>
</div>


