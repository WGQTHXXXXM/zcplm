<?php

use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Html;
use kartik\dialog\Dialog;


$this->title = '生成初版BOM';
$this->params['breadcrumbs'][] = $this->title;
?>

<br>
<br>

<h3><?= '步骤1. 获取正确的模板并填写物料；' ?></h3>
<a class="btn btn-success" href="download-template/?id=1">电子模板下载</a>&emsp;&emsp;&emsp;&emsp;
<a class="btn btn-success" href="download-template/?id=2">结构模板下载</a>

<br><br><br><br>
<h3><?= '步骤2. 选择BOM类型并上载文件；' ?></h3>
<h5 style="color: red">备注：<br>
    上传结构BOM时，PCBA供应商须填写“ZHICHE"。
    <br>变更任务尚未全部关闭时，暂不支持BOM上传。
</h5>
<br>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);?>
    <input id="taskRemark" type="hidden" name="taskRemark">
    <input id="taskCommit" type="hidden" name="taskCommit">

<div class="row">

    <div class="col-md-2">
        <label class="control-label">选择BOM类型</label>
        <div class="form-group field-modifymaterial-is_first_mfr required">

<!--            <label>-->
<!--                <input name="isElecBom" value="0" type="radio">-->
<!--                老版本电子-->
<!--            </label>-->

            <label>
                <input name="isElecBom" value="1" type="radio" checked="">
                电子
            </label>

            <label>
                <input name="isElecBom" value="2" type="radio">
                结构
            </label>
            <div class="help-block"></div>
        </div>
    </div>

    <div class="col-md-3">
        <?php echo $form->field($model, 'zcPartNo')->textInput(['maxlength' => true]);?>
        <div class="hide"><?php echo $form->field($model, 'merId')->textInput(['maxlength' => true]);?></div>
    </div>
    <div class="col-md-3">
        <label class="control-label" for="importbomform-filebom">选择文件</label>
        <?php echo FileInput::widget([
            'model' => $model,
            'attribute' => 'bomFile',
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
</div>
<br>
<br>
<br>
<div class="form-group">
    <h3><?= '步骤3. 校验文件。' ?></h3>

    <?= Html::Button("检查导入", ['class'=>'btn btn-primary','id'=>'btn-submit']); ?>
</div>


<?php ActiveForm::end(); ?>
<?php
//弹框控件，其它默认，prompt框改下默认的配置
echo Dialog::widget([
    'dialogDefaults'=>[

        Dialog::DIALOG_PROMPT => [
            'draggable' => true,
            'closable' => true,
            'title' => '是否马上提交任务',
            'buttons' => [
                [
                    'label' => '稍后提交任务',
                    'icon' => Dialog::ICON_CANCEL
                ],
                [
                    'label' => '马上提交任务',
                    'icon' => Dialog::ICON_OK,
                    'class' => 'btn-primary'
                ],
            ]
        ],

    ]
]);

////焦点离开input框的事件
//$js = <<<JS
//$('#importbomform-zcpartno').blur(modalDlg);
//JS;
//$this->registerJs($js);
////查找智车料号的文件
//include('../views/layouts/publicjs.php');

//焦点离开input框的事件
$url = \yii\helpers\Url::toRoute("/boms/get-bom-data");
$js = <<<JS
$('#importbomform-zcpartno').change(getBomPart);
function getBomPart() {
    var part = $(this).val().trim();
    if(part == '')
        return;
    $.post(
        '$url',
        {part:part},
        function(obj) {
            if(obj.status == 0)
            {
                $('#importbomform-zcpartno').val('');
                $('#importbomform-merId').val('');
                alert('此料号不满足上传条件');
            }
            else
            {
                $('#importbomform-merid').val(obj.data.material_id);
            }
        },'json'        
    );
}

/**
* 提交表单的按钮
*/
$('#btn-submit').on('click',function() {
    krajeeDialog.prompt({label:'备注', placeholder:'任务的备注...'}, function (result) {
        var remark = $("input[name='krajee-dialog-prompt']").val();
        $('#taskRemark').val(remark);
                
        if (result!=null) 
            $('#taskCommit').val('1');
        else 
            $('#taskCommit').val('0');
        $('#w0').submit();
    });
});
$('input[name="isElecBom"]').iCheck({ 
  labelHover : false, 
  cursor : true, 
  radioClass : 'iradio_square-blue', 
  increaseArea : '20%' 
}); 

JS;
$this->registerJsFile('/iCheck/icheck.min.js',['depends'=>'yii\web\JqueryAsset']);
$this->registerCssFile('/iCheck/skins/all.css');
$this->registerJs($js);


?>

