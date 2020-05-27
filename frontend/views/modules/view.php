<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Modules */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Modules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modules-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->module_id], ['class' => 'btn btn-primary']) ?>
        <!--?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->module_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?-->
        <?= Html::a(Yii::t('common', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box">
    <?= DetailView::widget([
        'options' => ['class' => 'table table-striped table-bordered detail-view table-hover'],
        'model' => $model,
        'attributes' => [
          //  'module_id',
          //  'project_id',
            'project_name',
            'name',
            'category',
            'milestone',
          //  'produce_qty',
          //  'date_entered',
        ],
    ]) ?>
    </div>
</div>
