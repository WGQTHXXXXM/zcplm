<?php

use kartik\grid\GridView;
use frontend\models\QualitySystemManage;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\QualitySystemManageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '总览看板';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('/statics\js\Chart.js',['depends'=>'yii\web\JqueryAsset']);
?>
<br><br>
    <div class="row">
    <div class="col-md-1" style="font-size: 20px;">总进度：</div>
    <div class="col-md-3" style="margin-left: -50px;margin-top: 5px;">
        <div class="progress" style="background-color:#e0e1d7;margin-right: -50px;">
            <div class="progress-bar progress-bar-success" role="progressbar" style="width: <?=$resProcess?>%;">
                <?=$resProcess?>%
            </div>
        </div>
    </div>
</div><br><br>
<div class="row">
    <div class="col-md-5">
        <?= GridView::widget([
            'tableOptions' => ['style'=>'table-layout:fixed;'],
            'hover'=>true,
            'panel' => [ 'heading' => "文件分类完成度",'before'=>false,'after'=>false,'footer'=>false,'type'=>GridView::TYPE_INFO],
            'dataProvider' => $arrFileClass,
            'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '','datetimeFormat' => 'php:Y-m-d H:i:s',],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions'=>['width'=>'30px'],
                ],

                //文件名
                [
                    'attribute' => 'file_class',
                    'value' => function($model){
                        return QualitySystemManage::FILE_CLASS[$model['file_class']];
                    },
                    'label'=>'文件分类',
                    "headerOptions" => ['style'=>'text-align: center;'],
                    "contentOptions" => ['style'=>'text-align: center;'],

                ],
                [
                    'attribute' => 'mu',
                    'value' => 'mu',
                    'label'=>'计划总量',
                    "headerOptions" => ['style'=>'text-align: center;'],
                    "contentOptions" => ['style'=>'text-align: center;'],
                ],
                [
                    'attribute' => 'zi',
                    'value' => function($model){
                        return $model['mu']-$model['zi'];
                    },
                    'label'=>'提交总量',
                    "headerOptions" => ['style'=>'text-align: center;'],
                    "contentOptions" => ['style'=>'text-align: center;'],
                ],
                [
                    'attribute' => 'zimu',
                    'value' => function($model){
                        return intval((1-($model['zi']/$model['mu']))*100).'%';
                    },
                    'label'=>'提交完成度',
                    "headerOptions" => ['style'=>'text-align: center;'],
                    "contentOptions" => ['style'=>'text-align: center;'],
                ],

                [
                    'attribute' => 'ziPass',
                    'value' => function($model){
                        return $model['mu']-$model['ziPass'];
                    },
                    'label'=>'定版总量',
                    "headerOptions" => ['style'=>'text-align: center;'],
                    "contentOptions" => ['style'=>'text-align: center;'],
                ],
                [
                    'attribute' => 'ziPassmu',
                    'value' => function($model){
                        return intval((1-($model['ziPass']/$model['mu']))*100).'%';
                    },
                    'label'=>'定版完成度',
                    "headerOptions" => ['style'=>'text-align: center;'],
                    "contentOptions" => ['style'=>'text-align: center;'],
                ],


            ],
        ]); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <?= GridView::widget([
            'tableOptions' => ['style'=>'table-layout:fixed;'],
            'hover'=>true,
            'panel' => [ 'heading' => "部门完成度",'before'=>false,'after'=>false,'footer'=>false,'type'=>GridView::TYPE_INFO],
            'dataProvider' => $arrDepartment,
            'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '','datetimeFormat' => 'php:Y-m-d H:i:s',],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions'=>['width'=>'30px'],
                ],
                //文件名
                [
                    'attribute' => 'dptName',
                    'label'=>'归属部门',
                    "headerOptions" => ['style'=>'text-align: center;'],
                    "contentOptions" => ['style'=>'text-align: center;'],
                ],
                [
                    'attribute' => 'mu',
                    'value' => 'mu',
                    'label'=>'计划总量',
                    "headerOptions" => ['style'=>'text-align: center;'],
                    "contentOptions" => ['style'=>'text-align: center;'],
                ],
                [
                    'attribute' => 'zi',
                    'value' => function($model){
                        return $model['zi'];
                    },
                    'label'=>'提交总量',
                    "headerOptions" => ['style'=>'text-align: center;'],
                    "contentOptions" => ['style'=>'text-align: center;'],
                ],
                [
                    'attribute' => 'zimu',
                    'value' => function($model){
                        return intval(($model['zi']/$model['mu'])*100).'%';
                    },
                    'label'=>'提交完成度',
                    "headerOptions" => ['style'=>'text-align: center;'],
                    "contentOptions" => ['style'=>'text-align: center;'],
                ],
                [
                    'attribute' => 'ziPass',
                    'value' => function($model){
                        return $model['ziPass'];
                    },
                    'label'=>'定版总量',
                    "headerOptions" => ['style'=>'text-align: center;'],
                    "contentOptions" => ['style'=>'text-align: center;'],
                ],
                [
                    'attribute' => 'zimu',
                    'value' => function($model){
                        return intval(($model['ziPass']/$model['mu'])*100).'%';
                    },
                    'label'=>'定版完成度',
                    "headerOptions" => ['style'=>'text-align: center;'],
                    "contentOptions" => ['style'=>'text-align: center;'],
                ],

            ],
        ]); ?>
    </div>
    <div class="row">
        <div class="col-md-5">
            <canvas id="departmentChart" ></canvas>
        </div>
    </div>

</div>

    <div class="row">
        <div class="col-md-5">
            <?= GridView::widget([
                'tableOptions' => ['style'=>'table-layout:fixed;'],
                'hover'=>true,
                'panel' => [ 'heading' => "部门完成效率",'before'=>false,'after'=>false,'footer'=>false,'type'=>GridView::TYPE_INFO],
                'dataProvider' => $arrEfficiency,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '','datetimeFormat' => 'php:Y-m-d H:i:s',],
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions'=>['width'=>'30px'],
                    ],
                    //文件名
                    [
                        'attribute' => 'dptName',
                        'label'=>'归属部门',
                        "headerOptions" => ['style'=>'text-align: center;'],
                        "contentOptions" => ['style'=>'text-align: center;'],
                    ],
                    [
                        'attribute' => 'num',
                        'value' => function($model){
                            return $model['num'];
                        },
                        'label'=>'定版数量',
                        "headerOptions" => ['style'=>'text-align: center;'],
                        "contentOptions" => ['style'=>'text-align: center;'],
                    ],
                    [
                        'attribute' => 'adate',
                        'value' => function($model){
                            $used = $model['adate'];
                            //天86400，小时3600，分60，秒
                            $day=0;$hour=0;$min=0;$str = '';
                            if($used==0){
                                return '0小时';
                            }
                            if($used<=86400){
                                $temp = strval($used/3600);
                                $hour = substr($temp,0,5);
                                $str .= $hour.'小时';
                                return $str;
                            }
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

                            return $str;
                        },
                        'label'=>'累计用时',
                        "headerOptions" => ['style'=>'text-align: center;'],
                        "contentOptions" => ['style'=>'text-align: center;'],
                    ],

                    [
                        'attribute' => 'zimu',
                        'value' => function($model){
                            if($model['adate'] == 0)
                                return '0秒';
                            $used = intval($model['adate']/$model['num']);
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

                            return $str;
                        },
                        'label'=>'文件效率',
                        "headerOptions" => ['style'=>'text-align: center;'],
                        "contentOptions" => ['style'=>'text-align: center;'],
                    ],

                ],
            ]); ?>
        </div>
        <div class="row">
            <div class="col-md-5">
                <canvas id="efficiencyChart" ></canvas>
            </div>
        </div>

    </div>


<?php
$jsonDepartment = json_encode($arrDepartment->allModels);
$jsonEfficiency = json_encode($arrEfficiency->allModels);
$js=<<<JS
///部门饼///
var ctx1 = document.getElementById('departmentChart');
var internetType1 = []; //上网类型	
var onePercentage1 = [];  //所占百分比
var backColor1 = [];	//百分比颜色
var jsonDepartment = {$jsonDepartment};
for(var i = 0; ith = jsonDepartment.length, i < ith; i++){
    internetType1.push(jsonDepartment[i].dptName);
    onePercentage1.push(parseInt((jsonDepartment[i].ziPass/jsonDepartment[i].mu)*100));				                                                                                                                    
        backColor1.push('#8A2BE2','#808069','#E6E6E6','#FCE6C9','#9C661F','#FF7F50',
        '#FF6347','#FFC0CB','#B0171F','#00FF00','#00FFFF','#40E0D0','#082E54',
        '#228B22','#6B8E23','#03A89E','#00C78C','#191970','#808A87','#DA70D6',
        '#C76114','#BC8F8F','#D2B48C','#C76114','#8B864E','#FFE384','#FFFFCD',
        '#F0FFFF','#FAFFF0');
}
var departmentChart = new Chart(ctx1,{
    type:'pie',
    data:{
        labels: internetType1, //矩形标题
        datasets:[{
            data: onePercentage1, //所占整圆的比例
            backgroundColor:backColor1, //背景色
            //hoverBackgroundColor:'#95F3FF',
            
        }]   
    },
    options: {
        responsive: true,
        legend: {
            position: 'right'
        },
        title: {
            display: true,
            text: '部门完成度',
            lineHeight:2,
            fontSize:20
        },
        animation: {
            animateScale: true,
            animateRotate: true
        },
        tooltips: {
            callbacks:{
                label: function(tooltipItem, data) {
                    console.log(tooltipItem);
                    console.log(data);
                    
                    var label =data.labels[tooltipItem.index];

                    if (label) {
                        label += ': ';
                    }
                    label += data.datasets[0].data[tooltipItem.index];
                    return label+'%';
                }
            }
        }

    }
});
///效率饼///
var ctx2 = document.getElementById('efficiencyChart');
var internetType2 = []; //上网类型	
var onePercentage2 = [];  //所占百分比
var backColor2 = [];	//百分比颜色
var jsonEfficiency = {$jsonEfficiency};
for(var i = 0; ith = jsonEfficiency.length, i < ith; i++){
    internetType2.push(jsonEfficiency[i].dptName);
    var adate = 0;
    if(jsonEfficiency[i].num != 0)
        adate = parseInt(jsonEfficiency[i].adate/jsonEfficiency[i].num);
    onePercentage2.push(adate);				                                                                                                                    
    backColor2.push('#8A2BE2','#808069','#E6E6E6','#FCE6C9','#9C661F','#FF7F50',
        '#FF6347','#FFC0CB','#B0171F','#00FF00','#00FFFF','#40E0D0','#082E54',
        '#228B22','#6B8E23','#03A89E','#00C78C','#191970','#808A87','#DA70D6',
        '#C76114','#BC8F8F','#D2B48C','#C76114','#8B864E','#FFE384','#FFFFCD',
        '#F0FFFF','#FAFFF0');
}
var efficiencyChart = new Chart(ctx2,{
    type:'pie',
    data:{
        labels: internetType2, //矩形标题
        datasets:[{
            data: onePercentage2, //所占整圆的比例
            backgroundColor:backColor2, //背景色
            //hoverBackgroundColor:'#95F3FF',
        }]   
    },
    options: {
        responsive: true,
        legend: {
            position: 'right'
        },
        title: {
            display: true,
            text: '部门完成效率',
            lineHeight:2,
            fontSize:20
        },
        animation: {
            animateScale: true,
            animateRotate: true
        },
        tooltips: {
            callbacks:{
                label: function(tooltipItem, data) {
                    var label =data.labels[tooltipItem.index];

                    if (label) {
                        label += ': ';
                    }
                    var adate = data.datasets[0].data[tooltipItem.index];
                    var str='';
                    if(adate>=86400){
                        var day = parseInt(adate/86400);
                        str += day+'天';
                        adate = adate%86400;
                    }
                    if(adate>=3600){
                        var hour = parseInt(adate/3600);
                        str += hour+'小时';
                        adate = adate%3600;
                    }
    
                        
                    return label+str;
                }
            }
        }
        
    }
});
JS;

$this->registerJs($js);
?>