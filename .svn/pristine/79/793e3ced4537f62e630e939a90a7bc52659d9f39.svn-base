<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('common', 'Build class & brand template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Importing class & brand for materials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'class')->textInput(['value' => 'classes.csv']) ?>

<?= $form->field($model, 'brand')->textInput(['value' => 'brands.csv']) ?>

<div class="form-group">
  <?= Html::submitButton(Yii::t('common', 'Submit'), ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
