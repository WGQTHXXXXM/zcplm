<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '查看上传的bom';
$this->params['breadcrumbs'][] = ['label' => '上一页', 'url' => empty($_SERVER['HTTP_REFERER'])?'':$_SERVER['HTTP_REFERER']];
$this->params['breadcrumbs'][] = $this->title;


?>
    <h1>
        <?php
        echo $this->title;
        ?>

    </h1>

<?= $this->render('upload-table', [
    'isUpdate'=>false,
]) ?>
<?php
require('../views/layouts/view-approve.php');

?>

