<?php

use kartik\grid\GridView;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'hover' => true,
    'bordered' => true,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'class' => 'kartik\grid\RadioColumn',
            'width' => '36px',
            'headerOptions' => ['class' => 'kartik-sheet-style'],
        ],
        'name',
        'version',
    ]
]);

?>


