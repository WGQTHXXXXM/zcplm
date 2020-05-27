<?php

use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Attachments */

$this->title = $material->mfr_part_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Materials'), 'url' => ['/materials/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attachments-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="box table-responsive">
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
            'dataProvider' => $attachments,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //   'attachment_id',
                //   'material_id',
                //   'attachment_url:url',
                [
                    'attribute' => 'attachment_url',
                    'value' => function($model) {
                        $filename = substr($model->attachment_url, strrpos($model->attachment_url, '/')+1);
                        return Html::a($filename, Url::to(['attachments/download', 'pathFile' => $model->attachment_url, 'filename' => $filename]));
                    },
                    'format' => 'raw',
                ],
                'version',
                [
                    'attribute' => 'updated_at',
                    'value'=> function($model){
                            return  date('y-m-d H:i:s',$model->updated_at);
                        },
                ],
                [
                    'attribute' => 'created_at',
                    'value'=>
                        function($model){
                            return  date('y-m-d H:i:s',$model->created_at);
                        },
                ],
            ],
        ]); ?>
    </div>
</div>
