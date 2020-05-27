<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\QsmAttachment */

$this->title = $model->qsm->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>

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
                        'value' => Html::a($model->name, ['quality-system-manage/download', 'pathFile' => $model->path, 'filename' => $model->name])
                    ],
                    'version',
                    [
                        'attribute'=>'remark',
                        'format'=>'ntext'
                    ],
                    [
                        'attribute'=>'updated_at',
                        'value'=>date('Y-m-d  H:i:s',$model->updated_at)
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
<?php
require('../views/layouts/view-approve.php');

?>
