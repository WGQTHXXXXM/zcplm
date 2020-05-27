<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = Yii::t('common', 'Get class & brand template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Importing class & brand for materials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Build class & brand template'), 'url' => ['build-template']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<p><?= Html::a(Yii::t('common', 'Click to download class template file'), url::to(['import-class-and-brand/get-template', 'file' => $model->class])) ?></p>

<p><?= Html::a(Yii::t('common', 'Click to download brand template file'), url::to(['import-class-and-brand/get-template', 'file' => $model->brand])) ?></p>

<h4><!--?= Html::a('Back to importing class and brand home', url::to(['import-class-and-brand/index'])) ?--></h4>
