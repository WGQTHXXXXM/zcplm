<?php
/***不用了，竹波设计的版本，项目文件查看。。。**/
use kartik\grid\GridView;
use frontend\models\Tasks;
use yii\helpers\Html;


$this->title =Yii::t('common','Project Manage View');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$str = '';//html代码，表格上面每个阶段的按钮
foreach ($mdl as $val)
{
    $str .= '&ensp;'.Html::a($val->name, ['project-manage-view','id'=>$_GET['id'],'lft'=>$val->lft,'rgt'=>$val->rgt], ['class' => 'btn btn-success']);
}

echo GridView::widget([
    'dataProvider'=>$dataProvider,
    'striped'=>true,
    'hover'=>true,
    'panel'=>['type'=>'primary', 'heading'=>$project.'——'.$curMdl->name,
        'before'=>$str,
    ],
    'toolbar' => [
//        [
//            'content'=>
//                Html::button('<i class="glyphicon">TR1</i>', [
//                    'type'=>'button',
//                    'title'=>'Add Book',
//                    'class'=>'btn btn-success'
//                ])
//        ],
//        [
//            'content'=>
//                Html::button('<i class="glyphicon">TR2</i>', [
//                    'type'=>'button',
//                    'title'=>'Add Book',
//                    'class'=>'btn btn-success'
//                ])
//        ],
//        '{toggleData}'
    ],
    'columns'=>[
        ['class'=>'kartik\grid\SerialColumn'],
        [
            'attribute'=>'pid',
            'width'=>'310px',
            'value'=>function ($model)
            {
                return $model->pidname->name;
            },
            'group'=>true,  // enable grouping,
            'groupedRow'=>true,                    // move grouped column to a single grouped row
            'groupOddCssClass'=>'kv-group-even',  // configure odd group cell css class
            'groupEvenCssClass'=>'kv-group-even', // configure even group cell css class
        ],
        'name',
        //上传人
        [
            'header'=>'上传人',
            'format'=>'raw',
            'value'=>function ($model)
            {
                if(empty($model->utime))
                    return '';
                return $model->submitter;
            },
        ],

        [
            'attribute'=>'taskStatus',
            'header'=>'状态',
            'value'=>function ($model){
                if(empty($model->taskStatus))
                    return '未提交';
                return Tasks::STATUS_COMMIT[$model->taskStatus];
            },
            'contentOptions'=> function($model)
            {
                return ($model->taskStatus==Tasks::STATUS_APPROVED)?[]:['class'=>'bg-danger'];
            },
        ],
        [
            'attribute'=>'ctime',
            'header'=>'创建时间',
            'value'=>function ($model){
                if(empty($model->ctime))
                    return '';
                return date('Y-m-d  H:i:s',$model->ctime);
            }
        ],
        [
            'attribute'=>'utime',
            'header'=>'更新时间',
            'value'=>function ($model){
                if(empty($model->utime))
                    return '';
                return date('Y-m-d  H:i:s',$model->utime);
            }
        ],
        //查看文件
        [
            'header'=>'查看文件',
            'format'=>'raw',
            'value'=>function ($model)
            {
                if(empty($model->utime))
                    return '';
                return Html::a('<span class="fa fa-download"></span>',
                    ['ecr/download', 'pathFile' => $model->file->path, 'filename' => $model->file->name]);
            }
        ],

        //上传文件
        [
            'header'=>'上传文件',
            'format'=>'raw',
            'value'=>function ($model)
            {
                if($model->taskStatus == Tasks::STATUS_COMMITED)
                    return '';
                return Html::a("<span class=\"fa fa-upload\"></span>", "/project-attachment/create?id=".$model->id,
                    ['data-method'=>"post"]);
            }
        ],
    ],
]);

?>

<div class="kv-grouped-row">

</div>

