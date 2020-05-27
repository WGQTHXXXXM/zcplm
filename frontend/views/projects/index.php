<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ProjectsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Projects');
$this->params['breadcrumbs'][] = $this->title;

$str = Yii::$app->authManager->checkAccess(Yii::$app->user->id,'/projects/create')?'':'hide';
?>
<div class="projects-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('common', 'Create Projects'), ['create'], ['class' => 'btn btn-success '.$str]) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'hover'=>true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'name',
                'format'=>'raw',
                'value' => function($model){
                    return Html::a($model->name,'/projects/project-manage-view?id='.$model->id);
                },
            ],
            [
                'attribute'=>'created_at',
                'value'=>function ($model)
                {
                    return date('Y-m-d',$model->created_at);
                }
            ],
            [
                'attribute'=>'end_at',
                'value'=>function ($model)
                {
                    return date('Y-m-d',$model->end_at);
                }
            ],
            [
                'attribute'=>'status',
                'value'=>function ($model)
                {
                    return $model::STATUS_PROJECT[$model->status];
                }
            ],
//            [
//                'attribute'=>'working',
//                'label'=>'项目阶段',
//                'value'=>function($model)
//                {
//                    return $model->process->name;
//                }
//            ],
//            [
//                'attribute'=>'precent',
//                'format'=>'raw',
//                'value'=>function($model)
//                    $pct = $model->precent;
//                    return '<div class="progress" style="background-color:#e0e1d7;">
//                              <div class="progress-bar progress-bar-success" role="progressbar" style="width: '.$pct.'%;">
//                                                '.$pct.'%
//                              </div>
//                            </div>';
//                }
//            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'更新',
                'template' => '{update} {/projects/project-manage-modify}',
                'buttons' => [
                    '/projects/project-manage-modify' => function ($url, $model, $key) {
                        return Html::a('<span class="fa fa-fw fa-cogs"></span>', $url, ['title' => Yii::t('common', 'Project Manage Modify')]);
                    },
                ],
            ]
        ],
    ]); ?>
</div>