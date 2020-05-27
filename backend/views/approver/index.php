<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ApproverSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-md-6">
<div class="approver-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增审批人', ['create?typeid='.$_GET['typeid']], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'department',
            [
                'attribute'=>'user_id',
                'label'=>'审批人',
                'value'=>function ($model)
                {
                    return $model->userName->username;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'header'=>'操作',
                'buttons'=>[
                    'update'=>function ($url){
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            $url.'&typeid='.$_GET['typeid'],
                            ['title'=>"更新"]
                        );
                    },
                    'delete'=>function ($url){
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                            $url.'&typeid='.$_GET['typeid'],
                            //post方式传参
                            ['data-method'=>"post",'data-confirm'=>"您确定要删除此项吗？"]
                        );
                    }
                ]
            ],
        ],
    ]); ?>
</div></div>
