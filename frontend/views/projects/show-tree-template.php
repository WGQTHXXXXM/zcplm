<?php
use kartik\tree\TreeView;
use kartik\tree\Module;
use yii\helpers\Url;

$this->title ='APQP清单';
$pro = Yii::t('common','expandAll/collapseAll');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo TreeView::widget([
    // single query fetch to render the tree
    // use the Product model you have in the previous step

    'rootOptions' =>[
        'class' => 'text-primary',
        'label'=>$pro
    ],
    'query' => $dataMsView,
    'headingOptions' => ['label' => 'template'],
    'fontAwesome' => false,     // optional
    'isAdmin' => true,         // optional (toggle to enable admin mode)
    'displayValue' => -1,        // initial display value
    'softDelete' => false,       // defaults to true
    'cacheSettings' => [
        'enableCache' => false   // defaults to true
    ],
    'treeOptions' => ['style' => ['height'=>'710px', ]],
    'showIDAttribute' => false,
    'nodeView' => '@frontend/views/projects/stt',
    'nodeActions' => [
        Module::NODE_SAVE => Url::to(['node/stt-save']),
        Module::NODE_MANAGE => Url::to(['node/stt-manage']),
    ],
    'toolbar' =>[
//        TreeView::BTN_CREATE_ROOT => ['options' => ['class' => 'hide']],
//        TreeView::BTN_MOVE_UP => ['options' => ['class' => 'hide']],
//        TreeView::BTN_MOVE_DOWN => ['options' => ['class' => 'hide']],
//        TreeView::BTN_MOVE_LEFT => ['options' => ['class' => 'hide']],
//        TreeView::BTN_MOVE_RIGHT => ['options' => ['class' => 'hide']],
    ],

]);
?>
