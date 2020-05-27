<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use frontend\models\UserTask;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\UserTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', '个人审批');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-task-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
        'striped' => true,
        'export' => false,
        'toggleData' => false,
        'hover'=>true,
        'bordered'=>true,
        'panel' => ['type' => 'success', 'heading' => "个人任务清单", 'footer' => false],
        'columns' => [
            [
                'attribute'=>'lvl',
                'group'=>true,  // enable grouping,
                'groupedRow'=>true,                    // move grouped column to a single grouped row
                'groupOddCssClass'=>'kv-group-even',  // configure odd group cell css class
                'groupEvenCssClass'=>'kv-group-even', // configure even group cell css class
                'value'=>function($model){
                    switch ($model->lvl){
                        case 1:
                            return '第一级审批人';
                        case 2:
                            return '第二级审批人';
                        case 3:
                            return '第三级审批人';
                        case 4:
                            return '第四级审批人';
                        case 5:
                            return '第五级审批人';
                        case 6:
                            return '第六级审批人';
                    }
                    return '审批情况';
                }
            ],
            [
                'attribute' => 'user_id',
                'label'=>'审批人',
                'value'=>"user.username"
            ],
            [
                'attribute' => 'status',
                'label'=>'审批状态',
                'value'=>function($model){
                    if($model->approve_able == 0&&$model->status==UserTask::STATUS_UNAPPROVE)
                        return '流程没到';
                    return $model::STATUS_APPROVE[$model->status];
                }
            ],
            'remark',
            [
                'attribute' => 'created_at',
                'label'=>'创建时间',
                'format' => ['date','php:Y-m-d H:i:s']
            ],
            [
                'attribute' => 'updated_at',
                'label'=>'审批时间',
                'format' => ['date','php:Y-m-d H:i:s']
            ],
            [
                'attribute' => 'created_at',
                'label'=>'任务单人用时',
                'value' => function($model){
                    if($model->approve_able == 0&&$model->status==UserTask::STATUS_UNAPPROVE)
                        return '0秒';//如果流程没到显示0；
                    $used = $model->updated_at-$model->created_at;
                    if($model->status==$model::STATUS_UNAPPROVE)
                        $used = time()-$model->created_at;
                    //天86400，小时3600，分60，秒
                    $day=0;$hour=0;$min=0;$str = '';
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
                }
            ],
        ],
    ]); ?>
</div>