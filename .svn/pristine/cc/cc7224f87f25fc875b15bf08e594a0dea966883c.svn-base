<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = Yii::t('common', 'Get material & bom template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Importing materials & boms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Build material & bom template'), 'url' => ['build-template']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<p><?= Html::a(Yii::t('common', 'Click to download material template file'), url::to(['import/get-template', 'file' => $model->material])) ?></p>

<p><?= Html::a(Yii::t('common', 'Click to download bom template file'), url::to(['import/get-template', 'file' => $model->bom])) ?></p>

<h4><!--?= Html::a('Back to import home', url::to(['import/index'])) ?--></h4>
