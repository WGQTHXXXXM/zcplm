<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model frontend\models\Projects */
/* @var $form ActiveForm */
?>
<div class="purchase-bom-form">

    <?php $form = ActiveForm::begin([
        'action' => (Yii::$app->controller->action->id == 'index')? ['view'] : [''],
        'method' => 'post',
    ]); ?>

    <?= $form->field($modules, 'project_id')->dropDownList(ArrayHelper::map($projects, 'project_id', 'name'),
        [
            'prompt'=>'请选择项目名称 ...',
            //  'id' => 'select_name',
            'onchange' => '
                $.ajax({
                    type: "GET",
                    url: "'.Url::to(["/purchase-bom/ajax?action=getmilestoneOption"], true).'",
                    data: {project_id:$(this).val()},
                    success: function(data) {
                        $("#modules-milestone").empty();
                        if (data != "null") {
                            var data = $.parseJSON(data);
                            option = \'<option value="">请选择里程碑 ...</option>\';
                            $("#modules-milestone").append(option);
                            $.each(data, function() {
                                option = \'<option value="\' + this.milestone + \'">\' + this.milestone + \'</option>\';
                                $("#modules-milestone").append(option);
                            });
                        }
                    },
                    timeout: 3000,
                    error: function(){
                        alert("错误：请求超时无响应");
                    }
                });'
        ]
    ) ?>

    <?= $form->field($modules, 'milestone')->dropDownList([]) ?>

    <?= $form->field($modules, 'category')->dropDownList([
        '电子' => '电子',
        '结构' => '结构'
    ]) ?>

    <div><p>数据列显示/隐藏</p>
        <p>
            <input type="hidden" name="part_no_toggle" value="0" />
            <input type="checkbox" name="part_no_toggle" value="1" />Part No.&nbsp;&nbsp;&nbsp;

            <input type="hidden" name="part_name_toggle" value="0" />
            <input type="checkbox" name="part_name_toggle" value="1" />物料名称&nbsp;&nbsp;&nbsp;

            <input type="hidden" name="description_toggle" value="0" />
            <input type="checkbox" name="description_toggle" value="1" />Description&nbsp;&nbsp;&nbsp;

            <input type="hidden" name="supplier_name_toggle" value="0" />
            <input type="checkbox" name="supplier_name_toggle" value="1" />Supplier Name&nbsp;&nbsp;&nbsp;

            <input type="hidden" name="second_source_part_no_toggle" value="0" />
            <input type="checkbox" name="second_source_part_no_toggle" value="1" />2nd Source P/N&nbsp;&nbsp;&nbsp;

            <input type="hidden" name="second_source_supplier_name_toggle" value="0" />
            <input type="checkbox" name="second_source_supplier_name_toggle" value="1" />2nd Supplier Name
        </p>
    </div>

    <div class="form-group">
        <?= Html::submitButton((Yii::$app->controller->action->id == 'index')? Yii::t('common', 'View') : Yii::t('common', 'Export'), ['class' => 'btn btn-success']) ?>
        <?= (Yii::$app->controller->action->id == 'export-data')? '文件名：'.Html::textInput("filename").'&nbsp;&nbsp;&nbsp;工作表：'.Html::textInput("worksheet") : '' ?>
    </div>

    <?php ActiveForm::end(); ?>

</div><!-- purchase-bom-form -->