<?php
use kartik\tree\TreeView;
use kartik\tree\Module;
use yii\helpers\Url;

$this->title ='项目文件查看';
$pro = Yii::t('common','expandAll/collapseAll');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//项目的名称
//渲染控件
echo TreeView::widget([
    // single query fetch to render the tree
    // use the Product model you have in the previous step
    'options'=>['id'=>'treeID'] ,
    'rootOptions' =>[
        'class' => 'text-primary',
        'label'=>$pro
    ],
    'query' => $dataMsView,
    'headingOptions' => ['label' => $strProName],
    'fontAwesome' => false,     // optional
    'isAdmin' => true,         // optional (toggle to enable admin mode)
    'displayValue' => -1,        // initial display value
    'softDelete' => false,       // defaults to true
    'cacheSettings' => [
        'enableCache' => false   // defaults to true
    ],
//    'showCheckbox'=>true,
    'emptyNodeMsg'=>' ',
    'showIDAttribute' => false,
    'nodeView' => '@frontend/views/projects/pmv',
    'treeOptions' => ['style' => ['height'=>'700px', ]],
    'nodeActions' => [
        Module::NODE_MANAGE => Url::to(['node/pmv-manage']),
    ],

//    'footerOptions' => ['class' => 'hide'],
//    'detailOptions' => ['class' => 'hide'],
    'toolbarOptions'=>['class' => 'hide'],
]);

$Js = <<<JS
//提前声明，见下面是POS_BEGIN位置，为的AJAX请求完页面时认得这个变量
var nodeData={};
JS;
$this->registerJs($Js,\yii\web\View::POS_BEGIN);

$Js = <<<JS

  //得到节点数据是字符串形式的  ，得到nodeData变量
$("#treeID").on('treeview.beforeselect', function(event, key, jqXHR, settings) {
    nodeData = settings;
});

JS;

$this->registerJs($Js);

?>




