<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\MaterialsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<p><button type="button" class="btn btn-primary toggle">数据列显示/隐藏</button></p>
<div class="materials-search hide">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <ul class="list-inline">
        <li>
            <?= $form->field($model, 'zc_part_number_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'mfr_part_number_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'part_name_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'car_number_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'description_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'manufacturer_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'manufacturer2_id_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'pcb_footprint_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'date_entered_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'vehicle_standard_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'purchase_level_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'remark_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'part_type_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'value_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'unit_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'schematic_part_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'manufacturer3_id_toggle')->checkbox([],true) ?>
        </li>
        <li>
            <?= $form->field($model, 'manufacturer4_id_toggle')->checkbox([],true) ?>
        </li>
    </ul>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Submit'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
  $(".toggle").click(function(){
    if ($(".materials-search").hasClass("hide")) {
        $(".materials-search").hide();
        $(".materials-search").removeClass("hide");
        $(".materials-search").show("slow");
    }
    else
        $(".materials-search").toggle("slow");
  });
JS;
$this->registerJs($js);
?>