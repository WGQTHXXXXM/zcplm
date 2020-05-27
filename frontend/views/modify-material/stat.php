<?php

use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model frontend\models\ModifyMaterial */
/* @var $mdlUserTask frontend\models\UserTask */
?>
<div class="modify-material-view">

    <?php
    if($status == 0){
        echo '<h4>此料为导入料</h4>';
    }else if($status == 1) {
        echo '<h3>任务情况</h3>';
        echo GridView::widget([
            'dataProvider' => $dataPorviderTask,
            'pjax' => true,
            'striped' => true,
            'hover' => true,
            'toolbar'=>[],
            'columns' => [
                [
                    'attribute' => 'mtr',
                    'header'=>'智车料号',
                    'value' => function ($model) {
                        return $model->modifyMaterial->zc_part_number;
                    },
                ],
                [
                    'attribute' => 'name',
                    'header'=>'任务名称',
                ],
                [
                    'attribute' => 'status',
                    'header'=>'任务状态',
                    'value' => function ($model) {
                        return $model::STATUS_COMMIT[$model->status];
                    },
                ],
                [
                    'attribute' => 'user_id',
                    'header'=>'任务创建人',
                    'value' => function ($model) {
                        return $model->user->username;
                    },
                ],
            ],
        ]);

//        echo '<h3>审批情况</h3>';
//        echo GridView::widget([
//            'dataProvider' => $dataProvider,
//            'pjax' => true,
//            'striped' => true,
//            'hover' => true,
//            'toolbar'=>[],
//            'columns' => [
//                [
//                    'attribute' => 'user_id',
//                    'header'=>'审批人',
//                    'value' => function ($model, $key, $index, $widget) {
//                        return $model->user->username;
//                    },
//                ],
//                [
//                    'attribute' => 'status',
//                    'header'=>'审批状态',
//                    'value' => function ($model, $key, $index, $widget) {
//                        return $model::STATUS_APPROVE[$model->status];
//                    },
//                ],
//            ],
//        ]);
    }



    ?>
</div>
