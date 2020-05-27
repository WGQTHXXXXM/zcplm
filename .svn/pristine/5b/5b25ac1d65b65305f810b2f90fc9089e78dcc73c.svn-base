<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php
$this->registerJs( <<<EOT_JS
$(document).ready(function(){
  /* Get class2 option by class1 id */
  $("#typeserviceform-class1").change(function() {

    var class1 = $(this).val();
    if (class1 != "") {
      $.get("get-class2-by-class1id",
        {filter_class1:class1},
        function(json) {
          if (json.class2s.length > 0) {
            $("#typeserviceform-class2").empty();
            var option = "<option value=\"\">" + "---Please select---" + "</option>";
            $("#typeserviceform-class2").append(option);
            $.each(json.class2s, function() {
              option = "<option value=\"" + this['id'] + "\">" + this['class2'] + "</option>";
              $("#typeserviceform-class2").append(option);
            });
          }
        },
        "json"
      );
    }
  });

  $("#typeserviceform-class2").change(function() {

    var class2 = $(this).val();

    /* Get class3 option by class2 id */
    if (class2 != "") {
      $.get("get-class3-by-class2id",
        {filter_class2:class2},
        function(json) {
          if (json.class3s.length > 0) {
            $("#typeserviceform-class3").empty();
            var option = "<option value=\"\">" + "---Please select---" + "</option>";
            $("#typeserviceform-class3").append(option);
            $.each(json.class3s, function() {
              option = "<option value=\"" + this['id'] + "\">" + this['name'] + "(" + this['description'] + ")</option>";
              $("#typeserviceform-class3").append(option);
            });
          }
        },
        "json"
      );

      /* Get brand option by class2 id */
      $.get("get-brand-by-class2id",
        {filter_class2:class2},
        function(json) {
          if (json.brands.length > 0) {
            $("#typeserviceform-brand").empty();
            var option = "<option value=\"\">" + "---Please select---" + "</option>";
            $("#typeserviceform-brand").append(option);
            $.each(json.brands, function() {
              option = "<option value=\"" + this['id'] + "\">" + this['brand'] + "</option>";
              $("#typeserviceform-brand").append(option);
            });
          }
        },
        "json"
      );
    } 
  });

  /* Get selected class3 name by class3 id */
  $("#typeserviceform-class3").change(function() {
    var class3 = $(this).val();
    if (class3 != "") {
      $.get("get-class3name-by-class3id",
        {filter_class3:class3},
        function(json) {
          if (json.name) {
           $("#typeserviceform-codenumber").val(json.name + '_');
          }
        },
        "json"
      );
      $("#typeserviceform-codenumber").focus();
    } else {
      $("#typeserviceform-codenumber").val('');
    }
  });
});
EOT_JS
);
?>

<?php $form = ActiveForm::begin(); ?>

<h1>Dynamic type demo</h1>

<?= $form->field($model, 'class1')->dropDownList($class1, ['prompt' => '---select---']) ?>
<?= $form->field($model, 'class2')->dropDownList(['' => ''], ['prompt' => '--select--']) ?>
<?= $form->field($model, 'class3')->dropDownList(['' => ''], ['prompt' => '--select--']) ?>
<?= $form->field($model, 'codenumber')->textInput() ?>
<?= $form->field($model, 'brand')->dropDownList(['' => ''], ['prompt' => '--select--']) ?>


<div class="form-group">
  <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
