<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\editable\Editable;
use frontend\models\UserTask;
use kartik\popover\PopoverX;
use frontend\models\Tasks;
use yii\bootstrap\Modal;

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
        //'filterModel' => $searchModel,
        'striped' => true,
        'export' => false,
        'toggleData' => false,
        'options'=>['id'=>'adminIndex'],
        'hover'=>true,
        'bordered'=>true,
        'panel' => ['type' => 'success', 'heading' => "个人任务清单"],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'task_id',
                'label'=>'任务名',
                'format'=>'raw',
                'value'=>'alinkTask'
            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => 'remark',
                'header'=>'任务备注',
                //  'width'=>'250px',
                'editableOptions' => function ($model) {
                    if(!isset($model->tasks))
                    {var_dump($model);die;}
                    $taskRemark = $model->tasks->remark;
                    return [
                        'placement' => PopoverX::ALIGN_BOTTOM_RIGHT,
                        'size' => 'md',
                        'inputType' => Editable::INPUT_TEXTAREA,
                        'displayValue' => $taskRemark? (strlen($taskRemark)<=30? $taskRemark : substr($taskRemark,0,30).'...') : '',
                        'options' => ['rows' => 5],
                        'submitOnEnter' => false,
                        'readonly' => true,
                        'buttonsTemplate' => '',
                        'valueIfNull'=>false,
                        'editableValueOptions'=>['style' => 'border-bottom:0px;'],
                    ];
                }
            ],
            [
                'attribute' => 'taskUser',
                'label'=>'任务人',
                'value'=>"tasks.user.username"
            ],
            [
                'attribute' => 'dateApprove',
                'label'=>'提交时间',
                'value' => function ($model){
                    return date("Y-m-d H:i:s",$model->tasks->date);
                },//搜索功能不好使
                //'filter'=>Html::input('text', 'UserTaskSearch[task]', $searchModel->date, ['class'=>'form-control col-lg-6']),
            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => 'userTaskStatus',
                'label'=>'我的审批',
                'value' => function($model) {
                    if($model->approve_able == 0&&$model->userTaskStatus==UserTask::STATUS_UNAPPROVE)
                        return '流程没到';
                    return $model::STATUS_APPROVE[$model->userTaskStatus];
                },
                'refreshGrid' => true,
                'editableOptions' => function ($model, $key, $index){
                    $statusApprove = UserTask::STATUS_APPROVE;
                    unset($statusApprove[0]);//去掉未审批
                    asort($statusApprove);
                    return [
                        'editableValueOptions'=> ($model->taskStatus==Tasks::STATUS_COMMITED&&
                            $model->userTaskStatus==UserTask::STATUS_UNAPPROVE&&$model->approve_able==1)? []:['style' => 'color:black;cursor:pointer;pointer-events: none;'],
                        'placement' => PopoverX::ALIGN_BOTTOM_RIGHT,
                        'submitButton'=>['icon'=>'<i class="glyphicon glyphicon-ok"></i>','class'=>'btn btn-sm btn-primary kv-editable-submit'],
                        'resetButton'=>['icon'=>'<i class="glyphicon glyphicon-remove"></i>','class'=>'hide btn btn-sm btn-default kv-editable-reset'],

                        'size' => 'md',
                        'formOptions' => ['action' => ['/user-task/do-approve']], // point to the new action
                        'inputType' => Editable::INPUT_DROPDOWN_LIST,
                        'submitOnEnter' => false,
                        'data' => $statusApprove,
                        'pluginEvents' => [
                            "editableSuccess"=>"function(event, val, form, data) 
                            { 
                                location.reload();
                            }",
                        ],
                        'afterInput' => function ($form, $widget) use ($model, $index) {
                            $strTemp ='';
                            if(strpos($model->tasks->name,'物料三审') !== false
                                &&Yii::$app->user->id == $model->tasks->modifyMaterial->materialApprover->approver3purchase)
                            {//当为物料时，看是不是采购，如果是采购要加个采购推荐级别。
                                $model->recommend_purchase = $model->tasks->modifyMaterial->recommend_purchase;
                                $strTemp .= $form->field($model, "[$index]recommend_purchase")->label('采购推荐级别')
                                    ->dropDownList(\frontend\models\ModifyMaterial::RECOMMEND_PURCHASE);
                            }
                            $strTemp .= $form->field($model, "[$index]userTaskRemark")->label('备注')->textarea([
                                    'displayValue' => 'more...',
                                    'inputType' => Editable::INPUT_TEXTAREA,
                                    //  'value' => "Raw denim you...",
                                    'submitOnEnter' => false,
                                    'size' => 'md',
                                    'rows' => 5,
                                ]);
                            return $strTemp;
                        },
                    ];
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'任务状态',
                'template' => '{/user-task/view}',
                'buttons' => [
                    '/user-task/view' => function ($url, $model, $key) {
                        return Html::a(Tasks::STATUS_COMMIT[$model->taskStatus], $url,
                            ['data-toggle'=>'modal','data-target'=>'#approve-modal','class'=>'approve-detail','taskid'=>$model->tasks->id]);
                    },
                ],
            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => 'userTaskRemark',
                'label'=>'审批备注',
                //  'width'=>'250px',
                'editableOptions' => function ($model, $key, $index) {
                    return [
                        'editableValueOptions'=>['style' => 'border-bottom:0px;'],
                        'size' => 'md',
                        'placement' => PopoverX::ALIGN_BOTTOM_RIGHT,
                        'inputType' => Editable::INPUT_TEXTAREA,
                        'displayValue' => $model->userTaskRemark? (strlen($model->userTaskRemark)<=30? $model->userTaskRemark : substr($model->userTaskRemark,0,30).'...') : '',
                        'options' => ['rows' => 5],
                        'submitOnEnter' => false,
                        'readonly' => true,
                        'buttonsTemplate' => '',
                        'valueIfNull'=>' '
                    ];
                }
            ],
            [
                'attribute' => 'taskSub',
                'label'=>'任务总用时',
                'value' => function($model){
                    $used = $model->taskSub;
                    if($model->taskStatus==Tasks::STATUS_COMMITED)
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
            ]
        ],
    ]); ?>
</div>
<?php
//弹出的审批详情
$approveDetail = \yii\helpers\Url::toRoute('/user-task/view');
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
    $.get('{$approveDetail}', { id: $(this).attr('taskid') },
        function (data) {
            $('.modal-body').html(data);
        } 
    );
});
JS;
$this->registerJs($Js);
Modal::end();
?>