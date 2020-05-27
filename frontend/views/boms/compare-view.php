<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model frontend\models\Boms */

$this->title = Yii::t('bom', 'CompareResult');
$this->params['breadcrumbs'][] = ['label' => Yii::t('bom', 'Compare'), 'url' => ['compare']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="boms-view">

    <h1><?php
            echo Html::encode($this->title);
            echo '&ensp;&ensp;&ensp;&ensp;';
            echo Html::a('下载比较的结果',['download-csv',
                        'pid1'=>$_POST['BomsParent'][0]['real_material'],

                        'pid2'=>$_POST['BomsParent'][1]['real_material'],
                ], ['class' => 'btn btn-success csv-download']);
        ?>
    </h1>

    <div class="box table-responsive">
        <h3><?= $models[0]->zc_part_number.' : '.$models[0]->parent_version.'（左）&nbsp;VS&nbsp;&nbsp;'.$models[1]->zc_part_number.' : '.$models[1]->parent_version.'（右）' ?></h3>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
         //   'options' => ['style' => 'word-wrap: break-word; word-break: break-all'],
            'dataProvider' => $provider_bom1diffbom2,
            'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '','datetimeFormat' => 'php:Y-m-d H:i:s',],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                // 显示第一个BOM版本的差异列信息
                [
                    'attribute' => Yii::t('material', 'Zhiche Part Number'),
                    'value' => function($model){
                        if (!empty($model['child_id'])) {
                            return Html::a($model['zc_part_number'], '#', ['data-toggle' => 'modal', 'data-target' => '#mtrview-modal', 'onclick' => "itemClick({$model['child_id']})"]);
                        }
                    },
                    'format'=>'raw',
                 //   'contentOptions' => ['style' => 'width:130px; white-space: normal;'],
                ],
                [
                    'attribute' => Yii::t('material', 'Manufacturer Part Number'),
                    'value' => 'mfr_part_number',
                    //   'contentOptions' => ['style' => 'width:130px; white-space: normal;'],
                ],
                [
                    'attribute' => Yii::t('bom', 'Qty'),
                    'value' => 'qty',
                ],
                [
                    'attribute' => Yii::t('bom', 'Reference No.'),
                    'value' => 'ref_no',
                    'contentOptions' => ['style' => 'width:20%; word-wrap: break-word; word-break: break-all; white-space: normal;'],
                ],
                [
                    'attribute' => Yii::t('material', 'Second Zhiche Part Number'),
                    'value' => function($model){
                        if (!empty($model['zc_part_number2_id'])) {
                            return Html::a($model['zc_part_number2'], '#', ['data-toggle' => 'modal', 'data-target' => '#mtrview-modal', 'onclick' => "itemClick({$model['zc_part_number2_id']})"]);
                        }
                    },
                    'format'=>'raw',
                 //   'contentOptions' => ['style' => 'width:130px; white-space: normal;'],
                ],
                [
                    'attribute' => Yii::t('material', 'third Zhiche Part Number'),
                    'value' => function($model){
                        if (!empty($model['zc_part_number3_id'])) {
                            return Html::a($model['zc_part_number3'], '#', ['data-toggle' => 'modal', 'data-target' => '#mtrview-modal', 'onclick' => "itemClick({$model['zc_part_number3_id']})"]);
                        }
                    },
                    'format'=>'raw',
                 //   'contentOptions' => ['style' => 'width:130px; white-space: normal;'],
                ],
                [
                    'attribute' => Yii::t('material', 'fourth Zhiche Part Number'),
                    'value' => function($model){
                        if (!empty($model['zc_part_number4_id'])) {
                            return Html::a($model['zc_part_number4'], '#', ['data-toggle' => 'modal', 'data-target' => '#mtrview-modal', 'onclick' => "itemClick({$model['zc_part_number4_id']})"]);
                        }
                    },
                    'format'=>'raw',
                 //   'contentOptions' => ['style' => 'width:130px; white-space: normal;'],
                ],
                [
                    'headerOptions' => ['style' => 'background:pink;'],
                    'contentOptions' => ['style' => 'background:pink;'],
                ],
                [
                    'attribute' =>'#',
                ],
                // 显示第二个BOM版本的差异列信息
                [
                    'attribute' => Yii::t('material', 'Zhiche Part Number'),
                    'value' => function($model){
                        if (!empty($model['zc_part_number_1_id'])) {
                            return Html::a($model['zc_part_number_1'], '#', ['data-toggle' => 'modal', 'data-target' => '#mtrview-modal', 'onclick' => "itemClick({$model['zc_part_number_1_id']})"]);
                        }
                    },
                    'format'=>'raw',
                 //   'contentOptions' => ['style' => 'width:130px; white-space: normal;'],
                ],
                [
                    'attribute' => Yii::t('material', 'Manufacturer Part Number'),
                    'value' => 'mfr_part_number_1',
                    //   'contentOptions' => ['style' => 'width:130px; white-space: normal;'],
                ],
                [
                    'attribute' => Yii::t('bom', 'Qty'),
                    'value' => 'qty_1',
                ],
                [
                    'attribute' => Yii::t('bom', 'Reference No.'),
                    'value' => 'ref_no_1',
                    'contentOptions' => ['style' => 'width:20%; word-wrap: break-word; word-break: break-all; white-space: normal;'],
                ],
                [
                    'attribute' => Yii::t('material', 'Second Zhiche Part Number'),
                    'value' => function($model){
                        if (!empty($model['zc_part_number2_1_id'])) {
                            return Html::a($model['zc_part_number2_1'], '#', ['data-toggle' => 'modal', 'data-target' => '#mtrview-modal', 'onclick' => "itemClick({$model['zc_part_number2_1_id']})"]);
                        }
                    },
                    'format'=>'raw',
                 //   'contentOptions' => ['style' => 'width:130px; white-space: normal;'],
                ],
                [
                    'attribute' => Yii::t('material', 'third Zhiche Part Number'),
                    'value' => function($model){
                        if (!empty($model['zc_part_number3_1_id'])) {
                            return Html::a($model['zc_part_number3_1'], '#', ['data-toggle' => 'modal', 'data-target' => '#mtrview-modal', 'onclick' => "itemClick({$model['zc_part_number3_1_id']})"]);
                        }
                    },
                    'format'=>'raw',
                 //   'contentOptions' => ['style' => 'width:130px; white-space: normal;'],
                ],
                [
                    'attribute' => Yii::t('material', 'fourth Zhiche Part Number'),
                    'value' => function($model){
                        if (!empty($model['zc_part_number4_1_id'])) {
                            return Html::a($model['zc_part_number4_1'], '#', ['data-toggle' => 'modal', 'data-target' => '#mtrview-modal', 'onclick' => "itemClick({$model['zc_part_number4_1_id']})"]);
                        }
                    },
                    'format'=>'raw',
                 //   'contentOptions' => ['style' => 'width:130px; white-space: normal;'],
                ],
            ],
        ]) ?>

        <h3><!--?= $models[1]->zc_part_number.' : '.$models[1]->parent_version ?--></h3>
        <!--?= GridView::widget([
            'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
            'dataProvider' => $provider_bom2diffbom1,
            'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '','datetimeFormat' => 'php:Y-m-d H:i:s',],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => Yii::t('material', 'Zhiche Part Number'),
                    'value' => function($model){
                        return Html::a($model['zc_part_number'],'#', ['data-toggle'=>'modal','data-target'=>'#mtrview-modal','onclick'=>"itemClick({$model['child_id']})"]);
                    },
                    'format'=>'raw',
                    'headerOptions' => ['width' => '10%'],
                ],
                [
                    'attribute' => Yii::t('bom', 'Qty'),
                    'value' => 'qty',
                ],
                [
                    'attribute' => Yii::t('bom', 'Reference No.'),
                    'value' => 'ref_no',
                    'headerOptions' => ['width' => '30%'],
                ],
                [
                    'attribute' => Yii::t('material', 'Second Zhiche Part Number'),
                    'value' => function($model){
                        if (!empty($model['zc_part_number2_id'])) {
                            return Html::a($model['zc_part_number2'], '#', ['data-toggle' => 'modal', 'data-target' => '#mtrview-modal', 'onclick' => "itemClick({$model['zc_part_number2_id']})"]);
                        }
                    },
                    'format'=>'raw',
                ],
                [
                    'attribute' => Yii::t('material', 'third Zhiche Part Number'),
                    'value' => function($model){
                        if (!empty($model['zc_part_number3_id'])) {
                            return Html::a($model['zc_part_number3'], '#', ['data-toggle' => 'modal', 'data-target' => '#mtrview-modal', 'onclick' => "itemClick({$model['zc_part_number3_id']})"]);
                        }
                    },
                    'format'=>'raw',
                ],
                [
                    'attribute' => Yii::t('material', 'fourth Zhiche Part Number'),
                    'value' => function($model){
                        if (!empty($model['zc_part_number4_id'])) {
                            return Html::a($model['zc_part_number4'], '#', ['data-toggle' => 'modal', 'data-target' => '#mtrview-modal', 'onclick' => "itemClick({$model['zc_part_number4_id']})"]);
                        }
                    },
                    'format'=>'raw',
                ],
            ],
        ]) ?-->
    </div>
</div>
<?php
//模态对话框查看物料详情
Modal::begin([
    'id' => 'mtrview-modal',
    'header' => '<h3 class="modal-title">查看</h3>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]);
Modal::end();
$materialView = Url::toRoute('/materials/view');

$Js = <<<JS
    function itemClick(id) {
        $.get('$materialView', {id:id,modal:1},
            function (data) {
                $('.modal-body').html(data);
            } 
        );
    }
JS;
$this->registerJs($Js,\yii\web\View::POS_BEGIN);

$data = json_encode($_POST['BomsParent']);//bom的数据

$JsEnd = <<<JS
    //设置table的高度
    var h = document.documentElement.clientHeight || document.body.clientHeight;//屏幕的高
    $('.table-responsive').attr('style','width: 100%;height: '+(h-240)+'px;');//屏幕高减去table上面那些高度

///////////////
    //下载csv
    $('.csv-download').on('click',function() {
        $.post(
            "/boms/download-csv", 
            {BomsParent:$data},
        );
    });

JS;

$this->registerJs($JsEnd);

