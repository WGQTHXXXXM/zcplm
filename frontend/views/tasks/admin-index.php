<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\editable\Editable;
use kartik\dialog\Dialog;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\popover\PopoverX;
use kartik\date\DatePicker;
use frontend\models\Tasks;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\TasksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', '任务管理');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasks-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'=>$searchModel,
        'striped' => true,
        'options'=>['id'=>'adminIndex'],
        'export' => false,
        'toggleData' => false,
        'hover'=>true,
        'bordered'=>true,
        'panel' => ['type' => 'success', 'heading' => "个人任务清单"],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'user_id',
                'editableOptions' => function ($model, $key, $index)use($dataUser)
                {
                    return [
                        'editableValueOptions'=> ($model->status==$model::STATUS_APPROVED)? ['style' => 'color:black;cursor:pointer;pointer-events: none;']:[],

                        'placement' => PopoverX::ALIGN_BOTTOM_RIGHT,
                        'submitButton'=>['icon'=>'<i class="glyphicon glyphicon-floppy-disk"></i>','class'=>'btn btn-sm btn-primary kv-editable-submit'],
                        'size' => 'md',
                        'formOptions' => ['action' => ['/tasks/change-user']], // point to the new action
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        'widgetClass' =>  'kartik\select2\Select2',
                        'displayValue'=>$model->user->username,
                        'options'=>[
                            'data' => $dataUser,
                        ],
                        'submitOnEnter' => false,
                        'pluginEvents' => [
                            "editableSuccess"=>"function(event, val, form, data) 
                            { 
                                location.reload();
                            }",
                        ]
                    ];
                }

            ],
            [
                'attribute'=>'name',
                'format'=>'raw',
                'value'=>'alinkTask'
            ],
            [
                'attribute'=>'status',
                'format'=>'raw',
                'filter'=>Tasks::STATUS_COMMIT,
                'contentOptions'=> function($model)
                {
                    return ($model->status==$model::STATUS_UNCOMMIT||$model->status==$model::STATUS_REJECTED
                        ||$model->status==$model::STATUS_CREATE_ECN)?['class'=>'bg-danger']:[];
                },
                'value'=>function($model,$key){
                    //return $model::STATUS_COMMIT[$model->status];
//                    if ($model->type == $model::TASK_TYPE_ECR4&&$model->status==$model::STATUS_CREATE_ECN)
//                    {
//                        return Html::a($model::STATUS_COMMIT[$model->status], 'javascript:;',['onclick'=>"createEcn($key)"]);
//                    }
//                    else if($model->type == $model::TASK_TYPE_ECN3||$model->type == $model::TASK_TYPE_ECN2
//                        ||$model->type == $model::TASK_TYPE_ECN1)//没有稍后提交，所以不可以在外面提交
//                        return Html::a($model::STATUS_COMMIT[$model->status], 'javascript:;',[]);
//                    return Html::a($model::STATUS_COMMIT[$model->status], 'javascript:;',
//                        ($model->status==$model::STATUS_UNCOMMIT||$model->status==$model::STATUS_REJECTED)?['onclick'=>"stat($key)"]:[]);
                    if($model->status==$model::STATUS_UNCOMMIT||$model->status==$model::STATUS_REJECTED){
                        if($model->type == $model::TASK_TYPE_BOM_UPLOAD){
                            return Html::a($model::STATUS_COMMIT[$model->status], 'javascript:;',['onclick'=>"stat($key)"]);
                        }
                    }
                    return $model::STATUS_COMMIT[$model->status];
                }
            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'remark',
                'width'=>'180px',
                'editableOptions'=> function ($model)
                {
                    return [
                        'submitButton'=>['icon'=>'<i class="glyphicon glyphicon-floppy-disk"></i>','class'=>'btn btn-sm btn-primary kv-editable-submit'],
                        'size' => 'md',
                        'placement' => PopoverX::ALIGN_BOTTOM_RIGHT,
                        'formOptions' => ['action' => ['/tasks/do-remark']], // point to the new action
                        'inputType' => Editable::INPUT_TEXTAREA,
                        'displayValue' => $model->remark? (strlen($model->remark)<=30? $model->remark : substr($model->remark,0,30).'...') : '',
                        'submitOnEnter' => false,
                        'buttonsTemplate' => ($model->status==$model::STATUS_UNCOMMIT||$model->status==$model::STATUS_REJECTED)?'{reset}{submit}':'',
                        'readonly'=>($model->status==$model::STATUS_UNCOMMIT||$model->status==$model::STATUS_REJECTED)?false:true,
                        'valueIfNull'=>false,
                        'editableValueOptions'=>['style' => 'border-bottom:0px;'],
                        //'rows' => 5,
                    ];
                }
            ],
            //操作
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'审批详情',
                'template' => '{/user-task/view}',
                'buttons' => [
                    '/user-task/view' => function ($url, $model, $key) {
                        //if($model->status==$model::STATUS_COMMITED||$model->status==$model::STATUS_REJECTED)
                            return Html::a('<span class="fa fa-mail-forward"></span>', $url,
                                ['data-toggle'=>'modal','data-target'=>'#approve-modal','class'=>'approve-detail']);
                    },
                ],
            ],
            [
                'attribute' => 'date',
                'format' => ['date','php:Y-m-d H:i:s'],
                'filter'=>DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date',
                    'value' => $searchModel->date,
                    'options' => ['readonly' => true],
                ]),
            ],
            [
                'attribute' => 'taskSub',
                'label'=>'用时',
                'value' => function($model){
                    $used = $model->taskSub;
                    if($model->status==$model::STATUS_COMMITED)
                        $used = time()-$model->date;
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
            [
                'class' => 'yii\grid\ActionColumn',
//                'template' => '{/tasks/delete} {/tasks/undo}',
                'template' => '{/tasks/delete}',
                'header'=>'操作',
                'buttons' => [
//                    '/tasks/undo'=>function ($url,$model,$key){
//                        return Html::a('<span class="fa fa-reply-all"></span>',$url,
//                            ['title'=>'撤消任务','class'=>$model->status == Tasks::STATUS_COMMITED?'':'hide']);
//                    },
                    '/tasks/delete'=>function ($url,$model,$key){

                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',$url,
                            ['class'=>$model->status==Tasks::STATUS_APPROVED?'hide':'',
                                'title'=>'删除任务','data-confirm'=>"您确定要删除此项吗？",'data-method'=>'post']);
                    },
                ]
            ]

//            [
//                'class' => 'yii\grid\ActionColumn',
//                'template' => '{delete}',
//                'header'=>'删除',
//                'visible' => Yii::$app->user->identity->username == 'songwei'?1:0,
//                //'visible' => Helper::checkRoute('/tasks/delete') == 'admin'?1:0,
//            ],

        ],
    ]); ?>
</div>

<?php
//弹出的审批详情
$approveDetail = Url::toRoute('/user-task/view');
//模态对话框
Modal::begin([
    'id' => 'approve-modal',
    'size'=>"modal-lg",
    'header' => '<h3 class="modal-title">审批详情'.'</h3>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]);
$Js = <<<JS
$('.approve-detail').on('click', function () {
    //var id = $(this).attr('index');
    $.get('{$approveDetail}', { id: $(this).closest('tr').data('key') },
        function (data) {
            $('.modal-body').html(data);
        } 
    );
});
JS;
$this->registerJs($Js);
Modal::end();
?>


<?php
//弹出的confirm框
echo Dialog::widget();
//弹框控件，其它默认，prompt框改下默认的配置
echo Dialog::widget([
    'libName' => 'submitprompt',
    'dialogDefaults'=>[

        Dialog::DIALOG_CONFIRM => [
            'type' => Dialog::TYPE_WARNING,
            'closable' => true,
            'title' => Yii::t('kvdialog', 'Confirmation'),
            'btnOKClass' => 'btn-warning',
            'draggable' => true,
            'btnOKLabel' => '是',
            'btnCancelLabel' => '否'
        ],

    ]
]);



$doCommit = Url::toRoute('/tasks/do-commit');//提交路径
$noCreateEcn = Url::toRoute('/tasks/no-create-ecn');
$js = <<< JS
function stat(key)
{
    submitprompt.confirm("确定提交任务吗？", function (result) 
    {
        if (result) 
        {
            $.get('$doCommit',{id:key},function(json) {
                if(json.status){
                    location.reload();
                } 
                else
                    krajeeDialog.alert("提交失败");
                location.reload();
            },'json');
            
        }
    });
}

function createEcn(key) 
{
    submitprompt.confirm("是否继续ECN？继续ECN点“是”，结束ECN点“否”。", function (result) 
    {
        if (result) 
        {
            $.post('/ecn/check-create',{},function(json) {
                if(json.status == 1)
                    location.href = '/ecn/create?TaskId='+key;
                else 
                    submitprompt.alert('请先把未完成的ECN流程走完');
            },'json');
        }
        else
        {
            $.get('$noCreateEcn',{id:key},function(json) {
                if(json.status){
                    location.reload();
                } 
            },'json');
        }
    });
    
}
JS;

// register your javascript
$this->registerJs($js, \yii\web\View::POS_BEGIN);