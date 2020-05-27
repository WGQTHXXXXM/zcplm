<?php

use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Html;
use kartik\dialog\Dialog;

$this->title = '批量上传';
$this->params['breadcrumbs'][] = $this->title;
?>
<br><br><br><br><br>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);?>
<input id="taskRemark" type="hidden" name="taskRemark">
<input id="taskCommit" type="hidden" name="taskCommit">

<div class="row">

    <div class="col-md-3">
        <label class="control-label" for="importbomform-filebom">选择文件</label>
        <?php echo FileInput::widget([
            'model' => $model,
            'attribute' => 'mtrFile',
            'options' => ['multiple' => false],
            'pluginOptions' => [

                // 需要预览的文件格式
                'allowedFileExtensions' => ['xls', 'xlsx'],
                'maxFileSize'=>10486,
                //不显示预览，显示文件名在input框
                'showPreview' => false,
                'showCaption' => true,
                'showRemove' => false,
                'showUpload' => false,
            ],
        ]);
        ?>
    </div>
    <div class="col-md-2">
    </div>

    <div class="col-md-3">
        <br>
        <?= Html::Button("检查导入", ['class'=>'btn btn-primary','id'=>'btn-submit']); ?>
    </div>
</div>
<br>
<br>




<?php ActiveForm::end(); ?>
<?php
//弹框控件，其它默认，prompt框改下默认的配置
//echo Dialog::widget([
//    'dialogDefaults'=>[
//
//        Dialog::DIALOG_PROMPT => [
//            'draggable' => true,
//            'closable' => true,
//            'title' => '是否马上提交任务',
//            'buttons' => [
//                [
//                    'label' => '稍后提交任务',
//                    'icon' => Dialog::ICON_CANCEL
//                ],
//                [
//                    'label' => '马上提交任务',
//                    'icon' => Dialog::ICON_OK,
//                    'class' => 'btn-primary'
//                ],
//            ]
//        ],
//
//    ]
//]);
$js = <<<JS

/**
* 提交表单的按钮
*/
$('#btn-submit').on('click',function() {
    // krajeeDialog.prompt({label:'备注', placeholder:'任务的备注...'}, function (result) {
    //     var remark = $("input[name='krajee-dialog-prompt']").val();
    //     $('#taskRemark').val(remark);
    //
    //     if (result!=null)
    //         $('#taskCommit').val('1');
    //     else
    //         $('#taskCommit').val('0');
         $('#w0').submit();
    // });
});

JS;
$this->registerJs($js);

?>

