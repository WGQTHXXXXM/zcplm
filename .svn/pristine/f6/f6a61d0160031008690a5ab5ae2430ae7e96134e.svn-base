<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\dialog\Dialog;
use kartik\select2\Select2;
use kartik\file\FileInput;
use kartik\grid\GridView;
$this->title = '文件上传';

$this->registerCssFile('/css/self/self.css');
?>

<br>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<input id="taskRemark" type="hidden" name="taskRemark">
<input id="taskCommit" type="hidden" name="taskCommit" value="1" >

<div class="row">
<!--    -->
    <div class="container-sw col-md-5">
        <span class="title-sw">上传信息</span>
        <br>
        <label><input type="radio" name='radio-type-upload' value="1" checked="">上传新文件</label>&emsp;&emsp;&emsp;
        <label><input type="radio" name='radio-type-upload' value="2">复用已有文件</label>
        <br><br>
        <div class="row sc">
            <div class="col-md-2"><label class="control-label">附件</label>：</div>
            <div class="col-md-9">
                <?php
                echo FileInput::widget(['name' => 'uploadFile', 'pluginOptions' => [
                    'showPreview' => false, 'showCaption' => true, 'showRemove' => true,'showUpload' => false]
                ]);
                ?>
                <div class="help-block"></div>
            </div>
            <div class="col-md-2"><label class="control-label">备注</label>：</div>
            <div class="col-md-9">
                <input type="text" class="form-control" name="attachment_remark_sc">
            </div>
        </div>


        <div class="row fy">
            <div class="col-md-2"><label class="control-label">复用物料</label>：</div>
            <div class="col-md-9">
                <?php echo Select2::widget([
                    'name' => 'fileClassName',
                    'data'=>$allMtr,
                    'id'=>'select-copy-upload',
                    'options'=>[
                        'placeholder'=>'选择文件类'
                    ]
                ]); ?>
            </div>

            <div class="col-md-2"><label class="control-label">复用的附件</label>：</div>
            <div class="col-md-9">
                <input type="text" id="input-copy-upload" class="form-control" readonly="">
                <div class="help-block"></div>
            </div>
            <div class="col-md-4 hide"><label class="control-label">id</label>
                <input type="text" id="input-hide-copy-upload" class="form-control" name="material_attachemt_id" readonly="">
            </div>
            <div class="col-md-2"><label class="control-label">备注</label>：</div>
            <div class="col-md-9">
                <input type="text" class="form-control" name="attachment_remark_fy">
            </div>
        </div><br>


    </div>
<!--    -->
    <div class="container-sw col-md-5">
        <span class="title-sw">基本信息</span>
        <br>
        <?= \yii\widgets\DetailView::widget([
            'model' => $model,
            'options' => ['class'=>'table table-striped table-bordered detail-view','style'=>'width:90%;'],
            'template' => '<tr><th width="100px">{label}:</th><td>{value}</td></tr>',
            'attributes' => [
                [
                    'attribute' => 'zc_part_number',
                    'value' => $model->materials->zc_part_number,
                    'label'=>'智车料号'
                ],
                [
                    'attribute' => 'description',
                    'value' => empty($model->materials->part_name)?$model->materials->description:$model->materials->part_name,
                    'label'=>'物料描述'
                ],
                'file_class_name'
            ],
        ]) ?>
        <div class="col-md-4 hide">
            <?= $form->field($model, 'material_id')->textInput(['maxlength' => true,'readonly'=>"readonly"]) ?>
        </div>
        <div class="col-md-4 hide">
            <?= $form->field($model, 'file_class_name')->textInput(['maxlength' => true,'readonly'=>"readonly"]) ?>
        </div>
    </div>
</div>
<br><br><br>


<hr style="border-top:2px solid #000">
<h3>审批人</h3><br>
<div class="row">

<?php

    //审批人
    foreach ($departmentApprove as $lvl=>$lvlApproves){
        echo '<div class="col-md-2 kv-detail-container" >';
        echo '<h4>第'.$lvl.'级审批人</h4>';
        foreach ($lvlApproves as $dep=>$lvlApprove){
            echo $form->field($model, 'departLvl['.$lvl.']['.$dep.']')->widget(Select2::className(),[
                'data' => $lvlApprove,
                'options' => ['placeholder' => '请选择审批人 ...'],
                'pluginOptions' => ['tokenSeparators' => [',', ' ']],
            ])->label($dep);
        }
        echo '</div>';
    }
?>
</div>

<br><br><br>
<div class="form-group">
    <?= Html::Button('保存',['class' => 'btn btn-success','id'=>'btn-submit']) ?>
</div>
<?php ActiveForm::end(); ?>


<?php
//弹框控件，其它默认，prompt框改下默认的配置
echo Dialog::widget([
    'libName' => 'submitprompt',
    'dialogDefaults'=>[
        Dialog::DIALOG_PROMPT => [
            'draggable' => true,
            'closable' => true,
            'title' => '确定提交',
            'buttons' => [
                [
                    'label' => '稍后提交任务',
                    'icon' => Dialog::ICON_CANCEL,
                    'cssClass'=>'hide'
                ],
                [
                    'label' => '确定',
                    'icon' => Dialog::ICON_OK,
                ],
            ]
        ],

    ]
]);
//////////////
Modal::begin([
    'id' => 'mtr-file-modal',
    'size'=>"modal-lg",
    'header' => '<h4 class="modal-title">选择物料附件</h4>',
    'footer' => '<a href="#" class="btn btn-primary" id="ok-choice-file" data-dismiss="modal">确定</a>
<a href="#" class="btn btn-primary" data-dismiss="modal">取消</a>',
]);
Modal::end();



/////////////////////////////////////////////////////////////////////////////////////////////////////
$js = <<<JS

///////radio/////////
$('.fy').hide();
$('input[name="radio-type-upload"]').on('click',function() {
    if($(this).val() == 1){//上传新文件
        $('.sc').show();
        $('.fy').hide();
    }else{//复用已有文件
        $('.sc').hide();
        $('.fy').show();        
    }
    
});
////////select选择物料下拉框//////////
$('#select-copy-upload').on('change',function() {
    $.post('/modify-material/mtr-file',{id:$(this).val()},function(obj) {
        
        $('#mtr-file-modal .modal-body').html(obj);  
        $('#mtr-file-modal').modal({
              show:true
        });

    }); 
});
$('#ok-choice-file').on('click',function() {
   
    $('#input-hide-copy-upload').val($('input[name="kvradio"]:checked').val());
    $('#input-copy-upload').val($('input[name="kvradio"]:checked').parent().next().text());
});


/**
* 提交表单的按钮
*/
$('#btn-submit').on('click',function() {  
    // if(!yanzheng())
    //     return;
    submitprompt.prompt({label:'备注', placeholder:'任务的备注...',title:'是否马上提交任务'}, function (result) {
        var remark = $("input[name='krajee-dialog-prompt']").val();
        $('#taskRemark').val(remark);

        var selectFileName = $('.select-file-name');
        var arrUnique=[];
        var isSuc = 1;
        selectFileName.each(function() {
            var tempVal = $(this).val();
            if(arrUnique.indexOf(tempVal) != -1){
                alert('文件类名“'+tempVal+'”重复');isSuc = 0;return false;
            }
            arrUnique.push(tempVal);
        });
        if(isSuc == 0)
            return;
        
        $('#w0').submit();
    });
});

// function yanzheng() {
//     var radioVal = $('input[name="radio-type-upload"]:checked').val();
//     if(radioVal == 1){//上传新文件 
//         return inputYanZhengKong($('input[name="uploadFile"]'));
//     }else{//复用已有文件
//         return inputYanZhengKong($('#input-copy-upload'));
//     }
// }
//
// function inputYanZhengKong(obj) {
//     if(obj.val().trim() == ''){
//         obj.parents('.col-md-4').addClass('has-error');
//         obj.children('.help-block').text('不可以为空');
//         return false;
//     }else{
//         obj.parents('.col-md-4').addClass('has-success');
//         obj.children('.help-block').text('');
//         return true;
//     }    
// }

JS;
$this->registerJs($js);


?>
