<?php

use yii\widgets\DetailView;
use \kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $mdlQsm frontend\models\QualitySystemManage */

$this->title = '质量体系文件查看';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('/css/self/self.css');

?>
<div class="quality-system-manage-view">

    <div class="row">
        <div class="container-sw col-md-5">
            <span class="title-sw">基本信息</span><br>
            <?= DetailView::widget([
                'model' => $mdlQsm,
                'attributes' => [
                    'name',
                    'parent_name',
                    'son_name',
                    [
                        'attribute'=>'department_belong_id',
                        'value'=>$mdlQsm->belongDepart->name,
                    ],
                    'file_code',
                    [
                        'attribute'=>'file_class',
                        'value'=>$mdlQsm::FILE_CLASS[$mdlQsm->file_class]
                    ],
                    [
                        'attribute'=>'status_submit',
                        'value'=>$mdlQsm::FILE_STATUS[$mdlQsm->status_submit]
                    ],

                    [
                        'attribute'=>'visible',
                        'value'=>$mdlQsm->visible?'是':'否'
                    ],

                ],
            ]) ?>
        </div>

        <?php if(!empty($mdlMaxVsnA)){ ?>
            <div class="container-sw col-md-5">
                <span class="title-sw">文件信息</span><br>
                <?= DetailView::widget([
                    'model' => $mdlMaxVsnA,
                    'attributes' => [
                        //文件名
                        [
                            'attribute' => 'name',
                            'format'=>'raw',
                            'value' => \yii\helpers\Html::a($mdlMaxVsnA->name,['/quality-system-manage/download',
                                'pathFile'=>$mdlMaxVsnA->path,'filename'=>$mdlMaxVsnA->name]),
                            'headerOptions'=>['width'=>'150px'],
                        ],
                        'version',
                        'remark:ntext',
                    ],
                ]) ?>

            </div>

            <div class="container-sw col-md-8">
                <span class="title-sw">审批信息</span><br>
                <?= GridView::widget([
                    'tableOptions' => ['style'=>'table-layout:fixed;'],
                    'hover'=>true,
                    'panel' => [ 'heading' => "文件查看",'before'=>false,'after'=>false,'footer'=>false,'type'=>GridView::TYPE_INFO],
                    'dataProvider' => $mdlApprove,
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
                        [
                            'attribute' => 'lvl',
                            'label'=>'级别',
                            'value' => function($model){
                                $str = '超级';
                                switch ($model->lvl){
                                    case 1:
                                        $str = '第一级审批';
                                        break;
                                    case 2:
                                        $str = '第二级审批';
                                        break;
                                    case 3:
                                        $str = '第三级审批';
                                        break;
                                    case 4:
                                        $str = '第四级审批';
                                        break;
                                    case 5:
                                        $str = '第五级审批';
                                        break;
                                    case 6:
                                        $str = '第六级审批';
                                        break;
                                }
                                return $str;
                            },
                            'headerOptions'=>['width'=>'100px'],
                            'group'=>true,  // enable grouping,
                            'groupedRow'=>true,                    // move grouped column to a single grouped row
                            'groupOddCssClass'=>'kv-group-even',  // configure odd group cell css class
                            'groupEvenCssClass'=>'kv-group-even', // configure even group cell css class

                        ],

                        //文件名
                        [
                            'attribute' => 'user_id',
                            'value' => function($model){
                                return $model->user->username;
                            },
                            'headerOptions'=>['width'=>'150px'],
                        ],
                        [
                            'attribute' => 'status',
                            'label'=>'审批结论',
                            'value' => function($model){
                                if($model->approve_able == 0&&$model->status==$model::STATUS_UNAPPROVE)
                                    return '流程没到';
                                return $model::STATUS_APPROVE[$model->status];
                            },

                            'headerOptions'=>['width'=>'150px'],
                        ],
                        [
                            'attribute' => 'remark',
                            'label'=>'审批说明',
                            "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
                            'headerOptions'=>['width'=>'350px'],
                        ],

                        [
                            'attribute' => 'updated_at',
                            'label'=>'审批用时',
                            'value' => function($model){
                                if($model->approve_able == 0&&$model->status==$model::STATUS_UNAPPROVE)
                                    return '0秒';//如果流程没到显示0；
                                $used = $model->updated_at-$model->created_at;
                                if($model->status==$model::STATUS_UNAPPROVE)
                                    $used = time()-$model->created_at;
                                //天86400，小时3600，分60，秒
                                $str = '';
                                if($used>=86400){
                                    $day = intval($used/86400);
                                    $str .= $day.'天';
                                    $used = $used%86400;
                                }
                                if($used>=3600){
                                    $hour = intval($used/3600);
                                    $str .= $hour.'小时';
                                    $used = $used%3600;
                                }
                                if($used>=60){
                                    $min = intval($used/60);
                                    $str .= $min.'分';
                                    $used = $used%60;
                                }
                                $str .= $used.'秒';

                                return $str;
                            },
                            'headerOptions'=>['width'=>'150px'],
                        ],


//                [
//                    'class' => 'yii\grid\ActionColumn',
//                    'template' => '{view}&emsp;{update}&emsp;{upload}',
//                    'header'=>'操作',
//                    'buttons' => [
//                        'upload'=>function ($url,$model,$key){
//                            return Html::a('<span class="glyphicon glyphicon-cloud-upload"></span>',$url,
//                                ['title'=>'上传']);
//                        },
//                    ]
//                ],
                    ],
                ]); ?>

            </div>
        <?php }else{ ?>
            <div class="col-md-5">
                <h3>还没有上传文件</h3>
            </div>
        <?php } ?>

    </div>

</div>
