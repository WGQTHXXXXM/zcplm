<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $mdl frontend\models\QualitySystemManageSearch */

$this->title = '查看历史';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-7">
        <?= GridView::widget([
            'tableOptions' => ['style'=>'table-layout:fixed;'],
            'hover'=>true,
            'panel' => [ 'heading' => "文件查看",'before'=>false,'after'=>false,'footer'=>false,'type'=>GridView::TYPE_INFO],
            'dataProvider' => $mdl,
            'options'=>['id'=>'adminIndex'],
            'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '','datetimeFormat' => 'php:Y-m-d H:i:s',],
            'pager' => [
                'class' => \yii\widgets\LinkPager::className(),
                'nextPageLabel' => '下一页',
                'prevPageLabel' => '上一页',
                'maxButtonCount' => 10,//显示的页数32.28
            ],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions'=>['width'=>'30px'],
                ],
                [
                    'attribute'=>'name',
                    'format'=>'raw',
                    'value'=>function($model){
                        return Html::a($model->name,['/quality-system-manage/download',
                            'pathFile'=>$model->path,'filename'=>$model->name]);
                    }
                ],
                [
                    'attribute'=>'version',
                ],
                [
                    'header'=>'审批记录',
                    'format'=>'raw',
                    'value'=>function($model){
                        return Html::button('审批记录',
                            ['data-toggle'=>'modal','data-target'=>'#approve-modal','class'=>'approve-detail','qsm_id'=>$model->id]);
                    }
                ],

            ]
        ]);
        ?>
    </div>
</div>
<?php
//弹出的审批详情
//模态对话框
Modal::begin([
    'id' => 'approve-modal',
    'size'=>"modal-lg",
    'header' => '<h3 class="modal-title">审批详情'.'</h3>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]);
Modal::end();
$Js = <<<JS
$('.approve-detail').on('click', function () {
    //var id = $(this).attr('index');
    $.get('/quality-system-manage/approve-detail', { id: $(this).attr('qsm_id') },
        function (data) {
            $('.modal-body').html(data);
        } 
    );
});
JS;
$this->registerJs($Js);
?>
