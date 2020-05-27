<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\QualitySystemManage;
use kartik\select2\Select2;
use kartik\dialog\Dialog;

/* @var $this yii\web\View */
/* @var $mdlQsm frontend\models\QualitySystemManage */
/* @var $form yii\widgets\ActiveForm */

function formTemp($label,$input)//返回表单模板的字符串
{
    return ['template'=>'<div class="col-md-'.$label.'">{label}：</div><div class="col-md-'.$input.'">{input}</div>{error}'];
}
$this->registerCssFile('/css/self/self.css');

?>
<div class="quality-system-manage-form">

    <?php $form = ActiveForm::begin(); ?>
<!--  文件信息  -->
    <div class="row">
        <div class="container-sw col-md-5">
            <span class="title-sw">文件信息</span>
            <div class="row"><?= $form->field($mdlQsm, 'name',formTemp(2,8))
                    ->textInput(['maxlength' => true,'placeholder'=>'请输入文件名...']) ?></div>
            <br>
            <div class="row"><?= $form->field($mdlQsm, 'parent_name',formTemp(2,8))
                    ->textInput(['maxlength' => true,'placeholder'=>'请输入主过程名称...']) ?></div>
            <br>
            <div class="row"><?= $form->field($mdlQsm, 'son_name',formTemp(2,8))
                    ->textInput(['maxlength' => true,'placeholder'=>'请输入子过程名称...']) ?></div>
            <br>
            <div class="row"><?= $form->field($mdlQsm, 'department_belong_id',formTemp(2,8))
                    ->dropDownList($arrDepartment,['prompt' => '请选择部门']) ?></div>
            <br>
            <div class="row"><?= $form->field($mdlQsm, 'file_code',formTemp(2,8))
                    ->textInput(['maxlength' => true,'placeholder'=>'请输入文件编码...']) ?></div>
            <br>
            <div class="row"><?= $form->field($mdlQsm, 'file_class',formTemp(2,8))
                    ->dropDownList(QualitySystemManage::FILE_CLASS,['prompt' => '请选择文件分类']) ?></div>
            <br>
            <div class="row"><?php
                $able = ($mdlQsm->status_submit==$mdlQsm::FILE_STATUS_APPROVE?'disabled':'');

                $arrTemp = $mdlQsm::FILE_STATUS;
                if($mdlQsm->status_submit!=$mdlQsm::FILE_STATUS_APPROVE)
                    unset($arrTemp[$mdlQsm::FILE_STATUS_APPROVE]);

                echo $form->field($mdlQsm, 'status_submit',formTemp(2,8))
                    ->dropDownList($arrTemp,[$able=>'']) ?></div>
            <br>
            <div class="row"><?= $form->field($mdlQsm, 'visible',formTemp(2,8))
                    ->dropDownList([1=>'是',0=>'否',]) ?></div>
            <br>
        </div>
        <!--  审批流 -->
        <div class="container-sw col-md-5">
            <span class="title-sw">审批流</span><br>
            <input type="button" class="btn btn-success add-upload" value="增加审批部门"><br>
            <div id="part-approve">
<?php
    echo '<div class="row"><br><br><div class="col-md-2"><label class="label-approve control-label">第一级审批：</label>';
    echo '</div><div class="col-md-8">';
    echo Select2::widget([
        'name' => 'department_id[]',
        'data' => $arrDepartment,
        'value'=>empty($arrApprove[1])?[]:$arrApprove[1],
        'options' => ['placeholder' => '', 'multiple' => true, 'class' => 'select-approve'],
        'pluginOptions' => [
        //'tags' => true,
        ],
    ]);
    echo '<div class="help-block"></div></div></div><br><br><div class="row"><div class="col-md-2">';
    echo '<label class="label-approve control-label">第二级审批：</label></div><div class="col-md-8">';
    echo Select2::widget([
        'name' => 'department_id[]',
        'data' => $arrDepartment,
        'value'=>empty($arrApprove[2])?[]:$arrApprove[2],
        'options' => ['placeholder' => '', 'multiple' => true, 'class' => 'select-approve'],
        'pluginOptions' => [
        //'tags' => true,
        ],
    ]);
    echo '<div class="help-block"></div></div></div>';
    //弹出的confirm框
    echo Dialog::widget();

$js = <<<JS

    var countUpload = 1;

    /**
    * 增加"选择部门下拉框"
    */
    function addApprove() {
        var ElmSelect = $(".select-approve");
        var num = ElmSelect.length+1;
        var str = '臭不要脸了啊，还加!!!,加了也不好用';
        if(num == 3)
            str = '第三级审批';
        else if(num == 4)
            str = '第四级审批';
        else if(num == 5)
            str = '第五级审批';
        else if(num == 6)
            str = '第六级审批';
    
        $('#part-approve').append('<div class="row" id="del-upload'+countUpload+'"><br><br>' +
    
            '<div class="col-md-2"><label class="label-approve control-label">'+str+'：</label></div><div class="col-md-8">' +
                '<select name="department_id[]" multiple="true" class="select-approve" ><option></option></select><div class="help-block"></div></div>' +
    
            '<div class="col-md-2">' +
                '<input type="button" class="btn btn-default form-control" value="删除" id="btn-del-upload'+countUpload+'" ></div>'+
            '</div>');
    
        //删除控件，同时要重排控件名字
        $('#btn-del-upload'+countUpload).on('click',function() {
            $(this).parent().parent().remove();
            var lenLabel = $(".label-approve").length;
            for(var i=2;i<lenLabel;i++){
                var str = '臭不要脸了啊，还加!!!';
                if(i == 2)
                    str = '第三级审批';
                else if(i == 3)
                    str = '第四级审批';
                else if(i == 4)
                    str = '第五级审批';
                else if(i == 5)
                    str = '第六级审批';
                $($(".label-approve")[i]).text(str);
            }
        });
        countUpload++;
    }
    //显示这个文件下的设定的审批部门（更新）
    var jsondataApprove = $jsondataApprove;
    for(var departs in {$jsondataApprove}){
        addApprove();
        $(".select-approve").select2({
            theme:'krajee',
            width:'100%',
            data:jsondataApprove[departs],
            placeholder: ""
        });
    }

    
    //增加物料附件上传按钮
    $('.add-upload').on('click',function() {
        addApprove();
        //初始化控件
        $(".select-approve").select2({
            theme:'krajee',
            width:'100%',
            data:{$jsondataApproveAll},
            placeholder: ""
        });
    });
    //提交按钮的事件,select2插件初始化没法实现name="department_id[][]"
    $('button[type="submit"]').on('click',function() {
        alert('确认提交');    
        var elmSelect = $(".select-approve");
        var len = elmSelect.length;
        var isSuc = true;
        for(var i=0;i<len;i++){
            if($(elmSelect[i]).val() == null){
                krajeeDialog.alert('审批流不可以为空');
                return false;
            }
            $(elmSelect[i]).attr('name','department_id['+i+'][]');
        }
        // if(isSuc == false)
        //     return false;
        if($('#dloadpmsdepartment-department_id').val()==null&&$('#dloadpmsuser-user_id').val()==null){
            krajeeDialog.alert('下载权限不可以同时为空');
            return false;
        }
        $('#w0-nodeform').submit();
    });
////////////////检查审批流是否为空//////////////////
// function checkNull(elm) {
//     if(elm.val() == null){
//         elm.siblings('.help-block').html('不可以为空');
//         elm.parent().parent().addClass('has-error');
//         elm.parent().parent().removeClass('has-success');
//         return false;
//     }
//     elm.parent().parent().addClass('has-success');
//     elm.parent().parent().removeClass('has-error');
//     elm.siblings('.help-block').val('');
//     return true; 
// }



//////////////////////////////////



JS;
$this->registerJs($js);

?>
</div></div>
<!-- 下载权限 -->
        <div class="container-sw col-md-5">
            <span class="title-sw">下载权限</span>
            <div class="row"><?= $form->field($mdlPmsDepartment, 'department_id',formTemp(2,8))
                    ->widget(Select2::classname(), [
                        'data' => $arrDepartment,
                        'options' => ['placeholder' => '请设定允许部门 ...','multiple' => true],
                        'pluginOptions' => ['tokenSeparators' => [',', ' ']],
                    ]) ?>
            </div>
            <br>
            <div class="row"><?= $form->field($mdlPmsUser, 'user_id',formTemp(2,8))
                    ->widget(Select2::classname(), [
                        'data' => $arrUser,
                        'options' => ['placeholder' => '请设定允许用户 ...','multiple' => true],
                        'pluginOptions' => ['tokenSeparators' => [',', ' ']],
                    ]) ?>
            </div>
            <br>
        </div>

    </div>

    <br><br>
    <div class="form-group">
        <?= Html::submitButton($mdlQsm->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $mdlQsm->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
