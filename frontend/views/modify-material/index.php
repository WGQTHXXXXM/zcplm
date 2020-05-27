<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Modify Materials');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modify-material-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('common', 'Create Modify Material'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'Id',
            'assy_level',
            'purchase_level',
            'mfr_part_number',
            'description',
            // 'pcb_footprint',
            // 'manufacturer',
            // 'zc_part_number',
            // 'date_entered',
            // 'vehicle_standard',
            // 'part_type',
            // 'value',
            // 'schematic_part',
            // 'price',
            // 'recommend_purchase',
            // 'minimum_packing_quantity',
            // 'lead_time:datetime',
            // 'manufacturer2_id',
            // 'manufacturer3_id',
            // 'manufacturer4_id',
            // 'material_id',
            // 'task_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
