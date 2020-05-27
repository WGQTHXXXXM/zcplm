<?php
use kartik\tree\TreeView;
use kartik\tree\Module;
use yii\helpers\Url;

$this->title =Yii::t('common','Project Manage Modify');
$pro = Yii::t('common','expandAll/collapseAll');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

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
    'nodeView' => '@frontend/views/projects/pmm',
    'treeOptions' => ['style' => ['height'=>'700px', ]],
    'nodeActions' => [
        Module::NODE_REMOVE => Url::to(['node/pmm-remove']),
        Module::NODE_MANAGE => Url::to(['node/pmm-manage']),
        Module::NODE_SAVE => Url::to(['node/pmm-save']),
    ],

//    'footerOptions' => ['class' => 'hide'],
//    'detailOptions' => ['class' => 'hide'],
    'toolbar' =>[
        TreeView::BTN_CREATE_ROOT => ['options' => ['class' => 'hide']],
//        TreeView::BTN_MOVE_UP => ['options' => ['class' => 'hide']],
//        TreeView::BTN_MOVE_DOWN => ['options' => ['class' => 'hide']],
//        TreeView::BTN_MOVE_LEFT => ['options' => ['class' => 'hide']],
//        TreeView::BTN_MOVE_RIGHT => ['options' => ['class' => 'hide']],
    ],
]);
?>





