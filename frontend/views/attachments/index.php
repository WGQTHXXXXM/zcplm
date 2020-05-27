<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\AttachmentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Attachments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attachments-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <!--?= Html::a(Yii::t('common', 'Create Attachments'), ['create'], ['class' => 'btn btn-success']) ?-->
    </p>

    <div class="box table-responsive">
    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

         //   'attachment_id',
         //   'material_id',
            [
                'attribute' => 'part_no',
                'value' => 'material.mfr_part_number',
            ],
         //   'attachment_url:url',
            [
                'attribute' => 'attachment_url',
                'value' => function($model) {
                    $filename = substr($model->attachment_url, strrpos($model->attachment_url, '/')+1);
                    return Html::a($filename, ['attachments/download', 'pathFile' => $model->attachment_url, 'filename' => $filename]);
                },
                'format' => 'raw',
            ],
            'version',
            'created_at:date',
            'updated_at:date',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{/attachments/view}',
                'buttons' => [
                    '/attachments/view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-paperclip"></span>', ['attachments/view', 'id' => $model->material_id], ['title' => $this->title]);
                    },
                ],
            ],
        ],
    ]); ?>
    </div>
</div>
