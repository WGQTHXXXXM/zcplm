<?php
/**
 * Created by PhpStorm.
 * User: shiyoubao
 * Date: 2017/11/2
 * Time: 19:06
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\helpers\Url;

$this->title = Yii::t('bom', 'Compare');
$this->params['breadcrumbs'][] = ['label' => Yii::t('bom', 'BOM Search'), 'url' => ['search/bom-index']];
$this->params['breadcrumbs'][] = $this->title;

$data = ArrayHelper::map($parent_code, 'parent_id', 'zc_part_number');
?>
<div class="boms-compare">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="compare-form">

        <?php $form = ActiveForm::begin([
            'action' => 'compare-view',
            'method' => 'post',
        ]); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model[0], "[0]parent_id")->widget(Select2::classname(), [
                    'data' => $data,
                    'options' => ['placeholder' => '请选择料号 ...'],
                ])->label(Yii::t('bom', 'Assembly')) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model[0], "[0]parent_version")->widget(Select2::classname(), [
                 //   'data' => $data,
                    'options' => ['placeholder' => '请选择版本 ...'],
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model[1], "[1]parent_id")->widget(Select2::classname(), [
                    'data' => $data,
                    'options' => ['placeholder' => '请选择料号 ...'],
                ])->label(Yii::t('bom', 'Assembly')) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model[1], "[1]parent_version")->widget(Select2::classname(), [
                 //   'data' => $data,
                    'options' => ['placeholder' => '请选择版本 ...'],
                ]) ?>
                <div class="hide">
                    <?= $form->field($model[0], "[0]real_material")->textInput() ?>
                    <?= $form->field($model[1], "[1]real_material")->textInput() ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('bom', 'Compare'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div><!-- compare-form -->

</div><!-- boms-compare -->
<?php
$getVersionListByParentId = Url::toRoute("/boms/get-version-list-by-parent-id");
$js = <<<JS

/* 通过第一分类获得第二分类的信息 */
for(i=0;i<2;i++) {
  $("#bomsparent-"+i+"-parent_id").change(function() {
    var i = 0;
    if($(this).attr("id")=="bomsparent-1-parent_id") i = 1;
    var parent_id = $(this).val();
    if (parent_id != "") {
        $.get(
            '$getVersionListByParentId',
            {parent_id:parent_id},
            function(json) {
                $("#bomsparent-"+i+"-parent_version").empty();
                if (json.status == 1) {
                    var option = "<option value=\"\">" + "请选择版本 ..." + "</option>";
                    $("#bomsparent-"+i+"-parent_version").append(option);
                    $.each(json.data, function() {
                        option = "<option value=\"" + this['parent_version'] +'" real_material="'+this['real_material']+ "\">"
                         + this['parent_version']+'——'+this['real_material_part'] + "</option>";
                        $("#bomsparent-"+i+"-parent_version").append(option);
                    });
                }
            },
            "json"
        );
    }
  });

  $("#bomsparent-"+i+"-parent_version").attr('index',i);
  $("#bomsparent-"+i+"-parent_version").change(function() {
    var text = $(this).children('option:selected').attr('real_material');
    $("#bomsparent-"+$(this).attr('index')+"-real_material").val(text);   
  });

}

JS;
$this->registerJs($js);

?>