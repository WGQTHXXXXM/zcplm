<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Attachments */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Attachments',
]) . $model->attachment_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Attachments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $materials[$model->material_id]['mfr_part_number'], 'url' => ['view', 'id' => $model->material_id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="attachments-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'materials' => $materials,
    ]) ?>

</div>
