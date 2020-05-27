<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = Yii::t('common', 'Importing bom');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Importing materials & boms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Importing bom file'), 'url' => ['import-bom']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php echo  $result . "<br>"; ?>

<h4><!--?= Html::a('Back to import home', url::to(['import/index'])) ?--></h4>
