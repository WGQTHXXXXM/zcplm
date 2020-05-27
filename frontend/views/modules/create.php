<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Modules */

$this->title = Yii::t('common', 'Create Modules');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Modules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modules-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
