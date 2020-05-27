<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ModulesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Modules');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modules-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('common', 'Create Modules'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box table-responsive">
    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

          //  'module_id',
          //  'project_id',
            [
                'attribute' => 'project_name',
                'value' => 'project.name',
            ],
            'name',
            'category',
            'milestone',
            // 'produce_qty',
            // 'date_entered',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
            ],
        ],
    ]); ?>
    </div>
</div>
