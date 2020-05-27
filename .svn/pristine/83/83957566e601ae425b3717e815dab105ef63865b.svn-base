<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('common', 'Importing class & brand file');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Importing class & brand for materials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->field($model, 'classFile')->fileInput() ?>
<?= $form->field($model, 'brandFile')->fileInput() ?>

<div class="form-group">
  <?= Html::submitButton(Yii::t('common', 'Submit'), ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
