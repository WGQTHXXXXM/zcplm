<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\ProjectAttachment */

$this->title = $model->materials->zc_part_number;
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>
    <?php
    echo $this->title;
    ?>

</h1>

<div class="project-attachment-view">


    <br><br>
    <div class="row">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute'=>'name',
                        'format' => 'raw',
                        'value' => Html::a($model->name, ['modify-material/download', 'pathFile' => $model->path, 'filename' => $model->name])
                    ],
                    'version',
                    'remark',
                    'file_class_name',
                    [
                        'attribute'=>'updated_at',
                        'value'=>date('Y-m-d  H:i:s',$model->updated_at)
                    ],
                    [
                        'label'=>'智车料号',
                        'attribute'=>'material_id',
                        'value'=>$model->materials->zc_part_number
                    ],
                    [
                        'label'=>'物料描述',
                        'attribute'=>'material_id',
                        'value'=>empty($model->materials->part_name)?$model->materials->description:$model->materials->part_name,
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
<?php
require('../views/layouts/view-approve.php');

?>
