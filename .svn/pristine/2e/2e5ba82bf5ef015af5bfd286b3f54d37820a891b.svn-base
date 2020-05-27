<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use kartik\dialog\Dialog;
use kartik\date\DatePicker;
use frontend\models\ModifyMaterial;

/* @var $searchModel frontend\models\MaterialsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if(empty($_GET['attach']))
    $this->title = '全部物料';
else
    $this->title = '没上传的物料';


$this->params['breadcrumbs'][] = $this->title;
?>
<div class="materials-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    //  echo $this->render('_search', ['model' => $searchModel]);
    ?>

    <p>
        <?php //echo Html::a(Yii::t('material', 'Create Materials'), ['create'], ['class' => 'btn btn-success']); ?>
        <?= Html::a(Yii::t('material', 'Create Materials'), ['/modify-material/create'], ['class' => 'btn btn-success']) ?>
    </p>

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
                    'headerOptions'=>['width'=>'30px'],
                    "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],

                ],
                //操作
                [
                    'class' => 'yii\grid\ActionColumn',
                    'headerOptions'=>['width'=>'80px'],
                    "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
                    //'template' => '{/boms/index} {/modify-material/get-material-status} {/modify-material/update} {/attachments/view}',
                    'template' => '{/modify-material/upload-attachment}',
                    'header'=>'操作',
                    'buttons' => [
                        '/modify-material/upload-attachment'=>function ($url){
                            return Html::a('<span class="glyphicon glyphicon-cloud-upload"></span>', $url,
                                ['title' => '只有DCC可以用','data-method'=>"post"]);
                        }

                    ],
                ],

                //一供智车料号
                [
                    'attribute' => 'zc_part_number',
                    'format'=>'raw',
                    'headerOptions'=>['width'=>'140px'],
                    "contentOptions" => ['style'=>'overflow: hidden;text-overflow: ellipsis;white-space:nowrap;'],
                    'value' => function($model){
                        return Html::a($model->zc_part_number,'#', ['data-toggle'=>'modal','data-target'=>'#mtrview-modal','class'=>'material-view1']);
                    },
                ],
                //一供智车料号
                [
                    'attribute' => 'mfr_part_number',
                    'format'=>'raw',
                    'headerOptions'=>['width'=>'140px'],
                ],
            ],
        ]); ?>
    </div>
</div>
<?php

//模态对话框下载datasheet
Modal::begin([
    'id' => 'download-modal',
    'header' => '<h3 class="modal-title">'.Yii::t('common','Download').'</h3>'.'<h6>（点击文件即可下载）</h6>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]);
Modal::end();
//模态对话框查看物料详情
Modal::begin([
    'id' => 'mtrview-modal',
    'header' => '<h3 class="modal-title">查看</h3>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]);
Modal::end();
$requestUpdateUrl = Url::toRoute('/attachments/download-dlg?id=');
$materialView = Url::toRoute('/materials/view');
$materialStat = Url::toRoute('/modify-material/get-material-stat');
$Js = <<<JS
        $('.forbidden').on('click',function() {
krajeeDialog.alert('您没有执行此操作的权限');
});

$('.data-download').on('click', function () {
var id = $(this).attr('index');
$.get('{$requestUpdateUrl}'+id, { id: $(this).closest('tr').data('key') },
function (data) {
$('.modal-body').html(data);
}
);
});

$('.material-view1').on('click', function () {
$.get('{$materialView}', { id: $(this).closest('tr').data('key'),modal:1 },
function (data) {
$('.modal-body').html(data);
}
);
});

$('.material-view').on('click', function () {
var id = $(this).attr('index');
$.get('{$materialView}', { id: id,modal:1 },
function (data) {
$('.modal-body').html(data);
}
);
});

$('.material-stat').on('click',function() {
var id = $(this).attr('key');
$.get('{$materialStat}',{id:id},function(data) {
$('.modal-body').html(data);
});
});

//$("#mtrview-modal").draggable();//移动模态框
//自适应屏幕高度
var h = document.documentElement.clientHeight || document.body.clientHeight;//屏幕的高
$('.table-responsive').attr('style','width: 100%;height: '+(h-300)+'px;');//屏幕高减去table上面那些高度
JS;
$this->registerJs($Js);

//检测这颗料是否可以被更新
$checkMaterial = Url::toRoute('/modify-material/check-material');
$updateMaterial = Url::toRoute('/modify-material/update?id=');
echo Dialog::widget();
$Js = <<<JS
        function materialUpdate(key) {
        $.get('$checkMaterial',{id:key},function(json) {
if(json.status == 0)
krajeeDialog.alert(json.message);
else
window.location.href='$updateMaterial'+key+'&material=1';
},'json');
}
JS;
$this->registerJs($Js,\yii\web\View::POS_BEGIN);
//$this->registerJsFile('https://cdn.bootcss.com/jqueryui/1.12.0/jquery-ui.min.js', ['depends'=>[
//        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
//        'yii\bootstrap\BootstrapPluginAsset',]]);

?>
