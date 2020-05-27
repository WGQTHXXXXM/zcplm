<?php

use yii\helpers\Html;
use frontend\models\Tasks;
use yii\grid\GridView;
use kartik\date\DatePicker;


/* @var $this yii\web\View */
/* @var $searchModel frontend\models\EcrSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'ECR');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecr-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('common', '新建ECR'), ['create'], ['class' => 'btn btn-success create-ecr']) ?>
    </p>
    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'serial_number',
            'projectName',
            'projectProcessName',
            'bom',
            [
                'attribute'=>'reason',
                'value'=>'cutReason'
            ],
            [
                'attribute'=>'detail',
                'value'=>'cutDetail'
            ],
            [
                'attribute'=>'created_at',
                'value'=>function($model)
                {
                    return date('Y-m-d H:i:s',$model->created_at);
                },
                'filter'=>DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'value' => $searchModel->created_at,
                    'options' => ['readonly' => true],
                ]),

            ],
            [
                'attribute'=>'updated_at',
                'value'=>function($model)
                {
                    return date('Y-m-d H:i:s',$model->updated_at);
                },
                'filter'=>DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated_at',
                    'value' => $searchModel->updated_at,
                    'options' => ['readonly' => true],
                ]),


            ],
            [
                'attribute'=>'user',
                'label'=>'创建者',
                'value'=>'tasks.user.username'
            ],
            [
                'attribute'=>'status',
                'label'=>'审批情况',
                'value'=>function($model)
                {
                    return Tasks::STATUS_COMMIT[$model->tasks->status];
                },
                'filter'=>Tasks::STATUS_COMMIT
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'操作',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
<?php
$js = <<<JS
$(".create-ecr").on('click',function() {
    $.post('/ecr/create-check',function(obj) {
        if(obj==true)
            location.href = '/ecr/create';
        else
            alert('有未处理完的BOM，请处理完再新建');
    });   
    
    return false;  
})

JS;

$this->registerJs($js);

?>

