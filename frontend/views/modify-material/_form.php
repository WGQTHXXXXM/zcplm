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

//是不是更新界面
$isUpdate = 0;//创建
$isUpgrade = 0;//是否是物料升级
if(!empty($class2)){//更新
    $isUpdate = 1;
    if(!isset($_GET['material']))
        $isUpgrade = 1;
}
/* @var $this yii\web\View */
/* @var $model frontend\models\Materials */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
if(!$model->mfr_part_number) //设定默认值
{
    $model->assy_level=3;
}

?>

<div class="materials-form">

    <?php
    $form = ActiveForm::begin(['action'=>$isUpgrade==1?'/modify-material/create':'','options' => ['enctype' => 'multipart/form-data']]);
    ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'class1')->dropDownList(empty($class1)? [] : ArrayHelper::map($class1, 'root', 'name'), ['prompt' => '请选择一级分类 ...']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'class2')->dropDownList(empty($class2)? [] : ArrayHelper::map($class2, 'id', 'name'), ['prompt' => '请选择二级分类 ...']) ?>
        </div>
    </div>
    <div class="row" style="/*border:1px solid #D2D6DE;margin:0px;*/">
        <div class="col-md-2 hide  mer1">
            <?= $form->field($model, 'mer1')->dropDownList(empty($dataDropMsg[0])? [] : ArrayHelper::map($dataDropMsg[0], 'remark', 'name')) ?>
        </div>
        <div class="col-md-2  hide mer2">
            <?= $form->field($model, 'mer2')->dropDownList(empty($dataDropMsg[1])? [] : ArrayHelper::map($dataDropMsg[1], 'remark', 'name')) ?>
        </div>
        <div class="col-md-2 hide mer3">
            <?= $form->field($model, 'part_type')->dropDownList([]) ?>
        </div>
        <div class="col-md-2 hide mer4">
            <?= $form->field($model, 'mer4')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-2 hide mer5">
            <?= $form->field($model, 'mer5')->dropDownList(empty($dataDropMsg[4])? [] : ArrayHelper::map($dataDropMsg[4], 'remark', 'name')) ?>
        </div>
        <div class="col-md-2 hide mer6">
            <?= $form->field($model, 'mer6')->dropDownList(empty($dataDropMsg[5])? [] : ArrayHelper::map($dataDropMsg[5], 'remark', 'name')) ?>
        </div>
        <div class="col-md-2 hide mer7">
            <?= $form->field($model, 'mer7')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-2 hide mer8">
            <?= $form->field($model, 'mer8')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-2 hide mer9">
            <?= $form->field($model, 'mer9')->dropDownList([]) ?>
        </div>
        <div id="material-struct"></div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'zc_part_number')->textInput(['maxlength' => true,'readonly'=>"readonly"]) ?>
        </div>
        <div class="col-md-4 hide material-structure">
            <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4 hide material-structure">
            <?= $form->field($model, 'car_number')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <br>
    <div class="row" style="/*border:1px solid #D2D6DE;margin:0px;*/">
        <div class="col-md-4">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4 hide">
            <?= $form->field($model, 'recommend_purchase')->
            dropDownList(array_merge([''=>''],$model::RECOMMEND_PURCHASE)) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'purchase_level')->dropDownList([''=>'','P'=>'P——purchase','M'=>'M——make']) ?>
        </div>

        <div class="col-md-4">
            <label class="control-label" >&nbsp;</label><!--为了排版-->
            <?= $form->field($model, 'is_first_mfr')->checkbox() ?>
        </div>

    </div>
    <div class="row">
        <div class="col-md-4 hide material-structure">
            <?= $form->field($model, 'part_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4 hide material-structure">
            <?= $form->field($model, 'unit')->dropDownList([''=>'','PCS'=>'PCS','REL'=>'REL','ML'=>'ML']) ?>
        </div>
        <div class="col-md-4 material-electronic">
            <?= $form->field($model, 'mfr_part_number')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4 material-electronic">
            <?= $form->field($model, 'pcb_footprint')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4  material-electronic">
            <?= $form->field($model, 'vehicle_standard')->dropDownList(['' => '', '0' => $model::VEHICLE_STANDARD[0],
                '1' => $model::VEHICLE_STANDARD[1], '2' => $model::VEHICLE_STANDARD[2]]) ?>
        </div>
        <div class="col-md-4 material-electronic">
            <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4 material-electronic">
            <?= $form->field($model, 'schematic_part')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'manufacturer')->dropDownList(empty($manufacturer)? [] : ArrayHelper::map($manufacturer, 'id', 'name')) ?>
        </div>
        <div class="col-md-4 hide">
            <?= $form->field($model, 'assy_level')->dropDownList([''=>'','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5']) ?>
        </div>
        <div class="col-md-4 hide">
            <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4 hide">
            <?= $form->field($model, 'lead_time')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4 hide">
            <?= $form->field($model, 'minimum_packing_quantity')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <h2>替代料</h2>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'mfrPartNo2')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'mfrPartNo3')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'mfrPartNo4')->textInput(['maxlength' => true]) ?>
        </div>
        <input id="taskRemark" type="hidden" name="taskRemark">
        <input id="taskCommit" type="hidden" name="taskCommit">
    </div>

    <hr style="border-top:2px solid #000">

    <h2>上传文件</h2>
    <div class="row">
        <div class="col-md-8">
            <?php
            if($isUpdate&&$isUpgrade==0)
            {
                echo '<h3>已有的</h3>';
                echo GridView::widget([
                    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
                    'dataProvider' => $dataAttachmentOld,
                    'striped'=>true,
                    'bordered'=>true,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn','headerOptions'=>['style'=>"width: 3.41%;"],],
                        [
                            'attribute' => 'file_class_name',
                            'headerOptions'=>['style'=>"width: 15.76%;"],
                        ],
                        [
                            'attribute' => 'name',
                            'value' => function($model) {
                                $filename = $model->name;
                                return Html::a($filename, ['modify-material/download', 'pathFile' => $model->path, 'filename' => $filename]);
                            },
                            'format'=>'raw',
                            'headerOptions'=>['style'=>"width: 32.17%;"],
                        ],
                        [
                            'attribute' => 'version',
                            'headerOptions'=>['style'=>"width: 5.07%;"],
                        ],
                        [
                            'attribute' => 'remark',
                            'headerOptions'=>['style'=>"width: 35.03%;"],
                        ],
                        [
                            'attribute' => 'updated_at',
                            'value' => function($model)
                            {
                                return date('Y-m-d H:i:s',$model->updated_at);
                            },
                            'headerOptions'=>['style'=>"width: 12.26%;"],
                        ],
                    ],
                ]);
                echo '<h3>新上传的</h3>';
                echo GridView::widget([
                    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
                    'dataProvider' => $dataAttachmentNew,
                    'striped'=>true,
                    'bordered'=>true,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn','headerOptions'=>['style'=>"width: 3.41%;"],],
                        [
                            'attribute' => 'file_class_name',
                            'headerOptions'=>['style'=>"width: 15.76%;"],
                        ],
                        [
                            'attribute' => 'name',
                            'value' => function($model) {
                                $filename = $model->name;
                                return Html::a($filename, ['modify-material/download', 'pathFile' => $model->path, 'filename' => $filename]);
                            },
                            'format'=>'raw',
                            'headerOptions'=>['style'=>"width: 32.17%;"],
                        ],
                        [
                            'attribute' => 'version',
                            'headerOptions'=>['style'=>"width: 5.07%;"],
                        ],
                        [
                            'attribute' => 'remark',
                            'headerOptions'=>['style'=>"width: 30%;"],
                        ],
                        [
                            'attribute' => 'updated_at',
                            'value' => function($model)
                            {
                                return date('Y-m-d H:i:s',$model->updated_at);
                            },
                            'headerOptions'=>['style'=>"width: 12.26%;"],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{/modify-material/delete-attachment}',
                            'header'=>'操作',
                            'headerOptions'=>['style'=>"width: 5%;"],
                            'buttons' => [
                                '/modify-material/delete-attachment' => function ($url) {
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url,
                                        ['title' => '删除','class'=>'delAtc']);
                                },
                            ],

                        ],
                    ],
                ]);
            }
            ?>

        </div>
    </div>

    <input type="button" class="btn btn-success add-upload" value="增加上传附件"><br>
    <div id="upload">
        <!--        <div class="row">-->
        <!--            <div class="col-md-2"><label class="control-label">文件类名</label>-->
        <!--                --><?php //echo Select2::widget([
        //                    'name' => 'fileClassName[]',
        //                    'data'=>$fileClassName,
        //                    'options'=>[
        //                        'placeholder'=>'选择文件类'
        //                    ]
        //                ]); ?>
        <!--            </div>-->
        <!--            <div class="col-md-4"><label class="control-label">附件</label>-->
        <!--                <input type="file" class="filefirst" name="attachment[]" data-show-preview="false" data-show-upload="false">-->
        <!--            </div>-->
        <!--            <div class="col-md-4"><label class="control-label">备注</label>-->
        <!--                <input type="text" class="form-control" name="attachment_remark[]">-->
        <!--            </div>-->
        <!--        </div>-->
    </div>
    <br>
    <div class="row hide">
        <div class="col-md-4">
            <?php
            //为了自动包涵一些头文件
            echo '<br>';
            echo FileInput::widget([
                'name' => 'uploadFile[]',
                'pluginOptions' => [
                    'showPreview' => false,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false
                ]
            ]);
            echo Select2::widget([
                'name' => 'waiwai[]',
            ]);
            $jsonFileClassName = [];
            foreach ($fileClassName as $val){
                $jsonFileClassName[] = ['id'=>$val,'text'=>$val];
            }
            $jsonFileClassName = json_encode($jsonFileClassName);

            $jsonAllMtr = [];
            foreach ($allMtr as $key=>$val){
                $jsonAllMtr[] = ['id'=>$key,'text'=>$val];
            }
            $jsonAllMtr = json_encode($jsonAllMtr);


            ?>
        </div>
    </div>
    <br><br>
    <input type="button" class="btn btn-success add-copy-upload" value="增加其它物料复件"><br>
    <div id="copy-upload">
    </div>
    <br>

    <hr style="border-top:2px solid #000">



    <h2>审批人</h2>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'approver1')->widget(Select2::className(),[
                'data' => $dataUser[0],
                'options' => ['placeholder' => '请选择审批人 ...'],
                'pluginOptions' => ['tokenSeparators' => [',', ' ']],
            ])->label('(部门内)一级审批人') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'approver2')->widget(Select2::className(),[
                'data' => $dataUser[1],
                'options' => ['placeholder' => '请选择审批人 ...'],
                'pluginOptions' => ['tokenSeparators' => [',', ' ']],
            ])->label('(部门内/外)二级审批人') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'approver3purchase')->widget(Select2::className(),[
                'data' => $dataUser[3],
                'options' => ['placeholder' => '请选择审批人 ...'],
                'pluginOptions' => ['tokenSeparators' => [',', ' ']],
            ])->label('(采购)三级审批人') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'approver3dcc')->widget(Select2::className(),[
                'data' => $dataUser[2],
                'options' => ['placeholder' => '请选择审批人 ...'],
                'pluginOptions' => ['tokenSeparators' => [',', ' ']],
            ])->label('(DCC)四级审批人') ?>
        </div>
    </div>

    <br><br><br>
    <div class="form-group">
        <?= Html::Button(!$model->mfr_part_number ? Yii::t('common', 'Create') : Yii::t('common', 'Update'),
            ['class' => !$model->mfr_part_number ? 'btn btn-success' : 'btn btn-primary','id'=>'btn-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$get_class2_by_class1id = Url::toRoute("/modify-material/get-class2-by-class1id");
$get_class3_by_class2id = Url::toRoute("/modify-material/get-class3-by-class2id");
$get_description_options_by_class3id = Url::toRoute("/modify-material/get-description-options-by-class3id");
$getSerialNum = Url::toRoute("/modify-material/get-serial-num");//根据料号一部分名字返回流水号
$getPartData = Url::toRoute("/modify-material/get-part-data");//根据输入的厂家料号，获得这个料的其它数据
$userTask = Url::toRoute("/tasks/index");//个人任务
$get_struct_sn=Url::toRoute('/modify-material/get-struct-sn');//获得结构流水号
$checkZcPartNo = Url::toRoute('/modify-material/check-zc-part-no');//检测是否有相同的一供智车料号
$getStructMoju = Url::toRoute('/modify-material/get-struct-moju');//得到模具的数据


//弹框控件，其它默认，prompt框改下默认的配置
echo Dialog::widget([
    'libName' => 'submitprompt',
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
echo Dialog::widget([]);
/////////////////////////////////////////////////////////////////////////////////////////////////////
$js = <<<JS

//上传规格书的控件
$('.filefirst').fileinput({
        'data-show-preview':false,
        'showUpload':false,
        'maxFileSize':60000
    });

////////////////////////PBC形成智车料号的数据结构,哪几个按钮////////////////
//编辑框应添入的数量
var inputLen = new Array(0,0,0);
//PCB板子的智车料号需要的控件号
var numPcb = [3,1,2,5,7,9];

/**
* 提交表单的按钮
*/
$('#btn-submit').on('click',function() {  
    
    submitprompt.prompt({label:'备注', placeholder:'任务的备注...',title:'是否马上提交任务'}, function (result) {
        var remark = $("input[name='krajee-dialog-prompt']").val();
        $('#taskRemark').val(remark);
                
        if (result!=null) 
            $('#taskCommit').val('1');
        else 
            $('#taskCommit').val('0');
        //检查审批人是否相同
        var checkApprover1=$('#modifymaterial-approver1').val();
        var checkApprover2=$('#modifymaterial-approver2').val();
        var checkApprover3=$('#modifymaterial-approver3dcc').val();
        var checkApprover4=$('#modifymaterial-approver3purchase').val();
        if(checkApprover1 == checkApprover2){
            alert('审批人不能相同');return;
        }
        if(checkApprover1 == checkApprover3||checkApprover2 == checkApprover3){
            alert('审批人不能相同');return;
        }
        if(checkApprover1 == checkApprover4||checkApprover2 == checkApprover4||checkApprover3 == checkApprover4){
            alert('审批人不能相同');return;
        }
        if(remark.trim() == ''){
            alert('提交的任务备注不能为空');return;
        }
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
        $('#modifymaterial-part_type').removeAttr('disabled');
        $('#w0').submit();
    });
});


  /* 通过第一分类获得第二分类的信息 */
$("#modifymaterial-class1").change(function() {
    var class1Val = $(this).val();
    if (class1Val != "") {
        $.get('$get_class2_by_class1id',
            {class1Val:class1Val},
            function(json) {
                $("#modifymaterial-class2").empty();
                if (json.status == 1) {
                    var option = "<option value=\"\">" + "请选择二级分类 ..." + "</option>";
                    $("#modifymaterial-class2").append(option);
                    $.each(json.data, function() {
                        option = "<option value=\"" + this['id'] + "\">" + this['name'] + "</option>";
                        $("#modifymaterial-class2").append(option);
                    });
                }
            },
            "json"
        );
        //如果是结构料(id=612)，显示part_name和unit字段；否则不显示
        if (class1Val == 612){
            $('.material-structure').removeClass('hide');
            $('.material-electronic').addClass('hide');
            $('#modifymaterial-is_first_mfr').prop("checked", false);
            $('.field-modifymaterial-is_first_mfr').addClass('hide');
        }else{
            $('.material-structure').addClass('hide');
            $('.material-electronic').removeClass('hide');
            $('#modifymaterial-is_first_mfr').prop("checked", true);
            $('.field-modifymaterial-is_first_mfr').removeClass('hide');
        }
    }
});

/**
* 限定最大字符长度
*/
function inputMaxStr(id) {
    switch (id)
    {
        case '5'://RES
        case '65'://BEAD
            inputLen=[4,4,2];
            doInputMaxStr(inputLen);
            break;
        case '6'://CAP
        case '7'://IND
            inputLen=[3,4,2];
            doInputMaxStr(inputLen);
            break;
        case '443'://pcb和pcba
        case '444':
            inputLen=[2,2,2];
            doInputMaxStr(inputLen);
            break;
    }
}

/**
* 设定四个编辑框的输入字符最大长度
*/
function doInputMaxStr(arr) {
    $('#modifymaterial-mer4').attr('maxlength',arr[0]);
    $('#modifymaterial-mer7').attr('maxlength',arr[1]);
    $('#modifymaterial-mer8').attr('maxlength',arr[2]);
}

  /**
   * 通过选中第二级分类，确定第三级（剩下所有的分类）; 
   */
$("#modifymaterial-class2").change(function() {
    $("#screw_material_encode_options").remove(); //先清除#screw_material_encode_options及其所有子元素节点以便清除相关数据
    $("#description_options").remove(); //先清除#description_options及其所有子元素节点以便清除相关数据
    var class2Val = $(this).val();
    
    inputMaxStr(class2Val);//设定编辑框的输入长度
    if (class2Val != "") {
        /* 获得智车料号的数据 */
        $.get(
            "$get_class3_by_class2id",
            {class2Val:class2Val},//传的参数 
            function(json) {//返回数据后   
                //选择完二级分类后，要清空的一些控件
                class3Empty();
                if(json.status == 1){//如果是正常物料（除PCB和PCBA）返回成功
                    var i=0;
                    //把返回的数据显示到下拉框和文本框里
                    $.each(json.data, function(name,value) {
                        if (value==''){//说明是文件框
                            i++;
                            for(;i<4;i++){//把文本框前的下拉框隐藏
                                var element = "#modifymaterial-mer" + i;
                                $('.mer'+i).addClass('hide');
                            }
                            if(i<=4)//bead这个料特别
                                i=4;
                            else
                                i=7;
                            //给文本框提示字符属性
                            var element = "#modifymaterial-mer" + i;
                            $(element).attr('placeholder',"请输入" + name + "...");
                            $('.mer'+i).removeClass('hide');
                            $('.mer'+i).find("label").text(name);
                            $('.mer'+i).find(".glyphicon-question-sign").remove();
                            // $('.mer4').find("label").after("<span class='glyphicon glyphicon-question-sign' " +
                            //  "title='我&#10;爱中国' style='color: #e2c12a;'>鼠标悬停提示</span>");
                        }else if(name.indexOf('分类')>=0){
                            i++;
                            for(;i<3;i++){//把分类前的下拉框隐藏
                                var element = "#modifymaterial-mer" + i;
                                $('.mer'+i).addClass('hide');
                            }
                            i=3;   
                            var element = "#modifymaterial-part_type";
                            var option = "<option value=\"\">" + "请选择" + name + "..." + "</option>";
                            $(element).append(option);
                            $('.mer'+i).removeClass('hide');
                            $('.mer'+i).find("label").text(name);
                            //向下拉框里添加 数据 
                            for(x in value) {
                                option = '<option value="'+value[x].id+'" remark="'+value[x].remark+'">' + value[x].name + "</option>";
                                $(element).append(option);
                            }
                        }else if(name == '厂家'){ 
                            var option = "<option value=\"\">" + "请选择" + name + "..." + "</option>";
                            $('#modifymaterial-manufacturer').append(option);  
                            $.each(value, function(key,val) {
                                option = "<option value=\"" + val.id + "\">" + val.name + "</option>";
                                $('#modifymaterial-manufacturer').append(option);  
                            });

                        }else{//说明是下拉框                           
                            i++;
                            if(i==4)
                                i++;
                            var element = "#modifymaterial-mer" + i;
                            var option = "<option value=\"\">" + "请选择" + name + "..." + "</option>";
                            $(element).append(option);
                            $('.mer'+i).removeClass('hide');
                            $('.mer'+i).find("label").text(name);
                            //向下拉框里添加 数据 
                            $.each(value, function(key,val) {
                                option = '<option value="' + val.remark + '">' + val.name + "</option>";
                                $(element).append(option);
                            });
                        }
                    });
                    //把没有用到的隐藏起来！
                    i++;
                    for(;i<10;i++){
                       var element = "#modifymaterial-mer" + i;
                       $('.mer'+i).addClass('hide');
                    }
                    $('#modifymaterial-zc_part_number').attr('readonly','');
                }
                else if(json.status == 2)//pcb和pcba
                {
                    //物料类型
                    var i=0;
                    $.each(json.data, function(name,value) {
                        if(i==0){
                            $('.mer3').find("label").text(name);
                            //向下拉框里添加 数据 
                            for(x in value) {
                                option = '<option value="'+value[x].id+'" remark="'+value[x].remark+'" selected>' + value[x].name + "</option>";
                                $("#modifymaterial-part_type").append(option);
                            }
                        }else if(i==4){
                            var element = "#modifymaterial-mer7";
                            $(element).attr('placeholder',"请输入" + name + "...");
                            $('.mer7').removeClass('hide');
                            $(element).val('00');
                            $('.mer7').find("label").text(name); 
                        }else{
                            var element = "#modifymaterial-mer" + numPcb[i]; 
                            var option = "<option value=\"\">" + "请选择" + name + "..." + "</option>";
                            $(element).append(option);
                            $('.mer'+numPcb[i]).removeClass('hide');
                            $('.mer'+numPcb[i]).find("label").text(name);
                            //向下拉框里添加 数据 
                            $.each(value, function(key,val) {
                                option = '<option value="' + val.remark + '">' + val.name + "</option>";
                                $(element).append(option);
                            });
                        }
                        i++;
                    });
                    partTypeChange();
                }
                else if(json.status == 3)//结构的物料
                {
                    if(class2Val == 1939)
                        $('#modifymaterial-zc_part_number').removeAttr('readonly');
                    else
                        $('#modifymaterial-zc_part_number').attr('readonly','');
                    
                    //如果是螺钉id=630，执行螺钉相关的函数
                    if (class2Val == 630){
                        var screw_material_encode_value = new Array();
                        for(var i=1;i<=9;i++){
                            screw_material_encode_value[i] = "";
                        }
                        screw_material_encode_value[8] = "0";
                        screw_material_encode_value[9] = "0";
                        //将不是生成螺钉物料编码的其它编码选项隐藏
                        for(var i=1;i<10;i++){
                            var element = "#modifymaterial-mer" + i;
                            $('.mer'+i).addClass('hide');
                        }
                        //执行螺钉相关的函数
                        generateScrewMaterialEncode(json, screw_material_encode_value);
                        return;
                    }
                    
                    /////////////////////////////
                    // 结构规则特点和代码思路：
                    // 1，前几个都是下拉框，后面都不是下拉框（然后就先把下拉框选建出来并添加上数据）
                    // 2. 根据不同的二级分类，分别建不同的控件，和不同的事件
                    // //////////////////////////
                    //加类型
                    var html='';
                    var i=0;
                    $.each(json.data, function(name,value) {
                        i++;
                        if (value!=''){//下拉框
                            if(name == '厂家'){ 
                                var option = "<option value=\"\">" + "请选择" + name + "..." + "</option>";
                                $('#modifymaterial-manufacturer').append(option);  
                                $.each(value, function(key,val) {
                                    option = "<option value=\"" + val.id + "\">" + val.name + "</option>";
                                    $('#modifymaterial-manufacturer').append(option);  
                                });
    
                            }else{
                                html += '<div class="col-md-2 form-group"><label>'+name+'</label><select id="struct'+i+'" class="form-control">';
                                html += "<option remark=\"\" value=\"\">" + "请选择" + name + "..." + "</option>";
                                //单一个直接选好唯一的一个
                                if(!(class2Val == 1827||class2Val == 738||class2Val == 1686||class2Val == 1668||class2Val == 1664)){
                                    if(i==1)
                                        $("#modifymaterial-part_type").append('<option value="">空</option>');//加一行空的数据
                                }

                                $.each(value, function(key,val) {
                                    html += '<option remark="'+val.id+'" value="' + val.remark + '">' + val.name + '</option>';
                                    //给part_type数据
                                    var option = '<option value="'+val.id+'">' + val.name + "</option>";
                                    if(i==1)
                                        $("#modifymaterial-part_type").append(option);
                                });
                                html += '</select></div>';
                            }
                        }
                    });
                    var strPjtName = '<option value="">请选择项目编号...</option><option value="S1">Smart 1C</option>' +
                     '<option value="S2">M11开发项目</option><option value="S3">FDC开发项目</option>';
                    //////////////////613部件和总成738/////////////
                    if(class2Val == 613||class2Val == 738||class2Val == 1827||class2Val == 1686){
                        //加预留位
                        html += '<div class="col-md-2 form-group"><label>预留位</label>' +
                         '<input  readonly="readonly" id="struct2" class="form-control" value="00" type="text" maxlength="2"> </div>';
                        //加项目编号
                        html += '<div class="col-md-2 form-group"><label>项目编号</label>' +
                         '<select id="struct3" class="form-control">'+strPjtName +
                          '</select></div>';
                    }
                    //加流水
                    if(class2Val == 613){//正常结构物料 
                        html += '<div class="col-md-2 form-group"><label>模具号</label>' +
                                '<label id="struct4-checkbox" style="float:right"><input checked="" type="checkbox">&nbsp;新建</label>' +
                                '<input id="struct4" readonly="" class="form-control" type="text"></div>';
                        html += '<div class="col-md-2 form-group"><label>模仁号/流水号</label>' +
                                '<label id="struct5-checkbox" style="float:right"><input checked="" type="checkbox">&nbsp;新建</label>' +
                                '<input id="struct5" readonly="" class="form-control" type="text"></div>';
                    }else if(class2Val == 738||class2Val == 1827||class2Val == 1686){//总成物料
                        html += '<div class="col-md-2 form-group"><label>流水号</label>' +
                                '<label id="struct4-checkbox" style="float:right"><input checked="" type="checkbox">&nbsp;新建</label>' +
                                '<input id="struct4" readonly="" class="form-control" type="text"></div>';
                    }
                    //////////////////其它1664/////////////
                    if(class2Val == 1664){
                        //加预留位
                        html += '<div class="col-md-2 form-group"><label>首次使用机型型号(00S10)</label>' +
                         '<input id="struct2" class="form-control" type="text" maxlength="5"> </div>';
                        html += '<div class="col-md-2 form-group"><label>流水号</label>' +
                         '<label id="struct3-checkbox" style="float:right"><input checked="" type="checkbox">&nbsp;新建</label>' +
                         '<input id="struct3" readonly="" class="form-control" type="text"></div>';
                    }
                    //////////////////半品1641/////////////
                    if(class2Val == 1641){
                        //加预留位
                        html += '<div class="col-md-2 form-group"><label>预留位</label>' +
                         '<input readonly="readonly" value="00" id="struct2" class="form-control" type="text" maxlength="2"> </div>';
                        html += '<div class="col-md-2 form-group"><label>项目编号</label>' +
                         '<select id="struct3" class="form-control">'+strPjtName +
                          '</select></div>'
                        html += '<div class="col-md-2 form-group"><label>流水号</label>' +
                         '<label id="struct4-checkbox" style="float:right"><input checked="" type="checkbox">&nbsp;新建</label>' +
                         '<input id="struct4" readonly="" class="form-control" type="text"></div>';
                    }
                    //////////////////包装1668/////////////
                    if(class2Val == 1668){
                        //加预留位
                        html += '<div class="col-md-2 form-group"><label>预留位</label>' +
                         '<input readonly="readonly" id="struct4" class="form-control" value="00" type="text" maxlength="2"> </div>';
                    }
                    $('#material-struct').append(html); 
                    //当编智车料号的前三个输入框有变化时，要清空后面的输入框和智车料号
                    if(class2Val!=1668){
                        $('#struct1').on('change',emptyStructSN);
                        $('#struct2').on('change',emptyStructSN);
                        if(class2Val != 1664)
                            $('#struct3').on('change',emptyStructSN);
                    }else{
                        $('#struct1').on('change',generateStructCode);
                        $('#struct2').on('change',generateStructCode);
                        $('#struct3').on('change',generateStructCode);
                        $('#struct4').on('change',generateStructCode);
                    }
                    //input框失去焦点事件:检查长度
                    $('#struct2').on('blur',checkInputLen);
                    $('#struct3').on('blur',checkInputLen);
                    $('#struct4').on('blur',checkInputLen);
                    
                    //新建的checkbox的事件
                    if(class2Val == 613){//部件
                        //点击复选框的事件
                        $('#struct4-checkbox').on('change',{class2Val:class2Val,id:4},getStructNo);
                        $('#struct5-checkbox').on('change',{class2Val:class2Val,id:5},getStructNo);
                    }else if(class2Val == 738||class2Val == 1827||class2Val == 1641||class2Val == 1686){//半品和总成
                        $('#struct4-checkbox').on('change',{class2Val:class2Val,id:4},getStructNo);                        
                    }else if(class2Val == 1664){//其它
                        $('#struct3-checkbox').on('change',{class2Val:class2Val,id:3},getStructNo);                                                
                    }
                    //如果是单一个要隐藏。
                    if(class2Val == 1664||class2Val == 1827||class2Val == 738||class2Val == 1686||class2Val == 1668){
                        $('#struct1').parent().prop('class','hide');
                        $('#struct1 option[remark=""]').remove();
                    }

                }
                else
                    krajeeDialog.alert(json.message);
            },
            "json"
        );
    } 
});

/**
* 结构input框失去焦点事件:检查长度
*/
function checkInputLen() {
    var maxLen = $(this).attr('maxlength');
    if(maxLen != undefined){
        var curLen = $(this).val().length;
        if(curLen!=0&&curLen!=maxLen){
            $(this).val('');
            krajeeDialog.alert('长度应该是'+maxLen+'位');
        }
    }  
}

/**
* 点击结构流水号时切换文本框和下拉框的事件，有获得数据，有合成智车料号
* @type {number}
*/
function getStructNo(event) 
{
    var id = event.data.id;
    var class2Val = event.data.class2Val;
    var zcCodePart ='';
    for(var i=1;i<id;i++)//检查生成智车料号的输入是否都输入了
    {
        if($('#struct'+i).val().length==0)//如果有一个没有输入，就不去检查流水，不生成智车料号
        {
            krajeeDialog.alert('前'+(id-1)+'个没添完整不能自动生成流水号');
            if($('#struct'+id+'-checkbox input[type="checkbox"]').prop('checked'))
                $('#struct'+id+'-checkbox input[type="checkbox"]').prop('checked',false);
            else
                $('#struct'+id+'-checkbox input[type="checkbox"]').prop('checked',true);
            return;
        }
        zcCodePart += $('#struct'+i).val();//如果都输入了，要把输入的记下来，然后去库里查流水号
    }
    
    
    $.get('$getStructMoju',{zcCodePart:zcCodePart,class2Val:class2Val,id:id},function(json) {
        //在编辑框和下拉之间切换
        if($('#struct'+id+'-checkbox input[type="checkbox"]').is(':checked'))//如果当前是文本框状态
        {//新建，向文本框里添数据 
            $('#struct'+id).remove();
            var serialNumber = '';
            if(json.status == 0)
            {
                if(class2Val == 1686||class2Val == 1641)
                    serialNumber = '0001';
                else if(class2Val == 738||class2Val == 1827)
                    serialNumber = '001';
                else if(class2Val == 613&&id==4)
                {
                    serialNumber = '001';
                    $('#struct5').empty();
                    $('#struct5').val('');
                }
                else if(class2Val == 613&&id==5)
                    serialNumber = '01';
                else if(class2Val == 1664)
                    serialNumber = '001';
            }
            else if(json.status == 1)
            {
                var newSn = json.data.pop();
                serialNumber = padZore(parseInt(newSn)+1,newSn.length);
            }
            $('#struct'+id+'-checkbox').after('<input id="struct'+id+'" readonly="" value="'+serialNumber+'" class="form-control" type="text">');
            //当显示文本框时要生成料号
            if(class2Val == 613&&id==4)
                return;
            generateStructCode();
        }
        else//下拉框，向下拉框里添数据
        {
            $('#struct'+id).remove();
            //得到数据
            var option='<select id="struct'+id+'" class="form-control"><option value=""> </option>';
            if(class2Val == 613&&id==4){
                option+='<option value="000">该模具非智车资产</option>';
            }
            for(var key in json.data)
            {
                option += '<option value="'+json.data[key]+'">'+json.data[key]+'</option>';  
            }
            //添加数据
            $('#struct'+id+'-checkbox').after(option+'</select>');
            if(class2Val == 613&&id==4)//如果是模具号变了，要让后面的模仁清空;或选中000后面要变成00
            {
                //创建完声明一个事件函数
                // $('#struct4').on('change',function() {
                //     if($(this).val() == '000'){
                //         $('#struct5').val('00');
                //         generateStructCode();
                //     }
                //     else{
                //         $('#struct5').empty();
                //         $('#struct5').val('');
                //     }
                // })
                $('#struct4').on('change',function() {
                    $('#struct5').empty();
                    $('#struct5').val('');
                })

            }
            //当下拉框改变时生成料号
            if(class2Val == 613&&id==5)
                $('#struct5').on('change',generateStructCode);
            else if(class2Val == 738||class2Val == 1827||class2Val == 1641||class2Val == 1686)
                $('#struct4').on('change',generateStructCode);    
            else if(class2Val == 1664)
                $('#struct3').on('change',generateStructCode);
        }
        
             
    },'json')
    
}

/**
* 生成结构的智车料号
*/
function  generateStructCode() {
    var zcCode = '';
    var class2Val = $("#modifymaterial-class2").val();
    if(class2Val == 1668)//包装的生成方式与别的不同
    {
        asynParttype();
        for(var j=1;j<5;j++){
            if($('#struct'+j).val()=='')
                return;
        }
    }
    for(var i=1;i<=5;i++)
    {
        if(document.getElementById('struct'+i))
            zcCode += $('#struct'+i).val()
    }
    $.get('$get_struct_sn',{zcCode:zcCode,class2Val:class2Val},function(json) {
        if(json.status == 0){
            switch (class2Val)
            {
                case '613':
                case '1641':
                    zcCode += json.data;
                    break;
                case '738':
                case '1827':
                    zcCode += padZore(json.data,3);
                    break;
                case '1664':
                case '1686':
                    zcCode += padZore(json.data,2);
                    break;
                case '1668':
                    zcCode += padZore(json.data,4);
                    break;
            }        
            $('#modifymaterial-zc_part_number').val(zcCode);
        }else{
            krajeeDialog.alert('请去升级料号');
            $('#modifymaterial-zc_part_number').val('');
        }
    },'json')
}


/*当编智车料号的前三个输入框有变化时，要清空后面的输入框和智车料号*/
function emptyStructSN(event) 
{
    //如果是选择类型，给part_type负值
    if(event.target.id == "struct1")
        asynParttype();
    
    var class2Val = $("#modifymaterial-class2").val();
    if(class2Val == 613){//613部件
        $('#struct4').empty();
        $('#struct4').val('');
        $('#struct5').empty();
        $('#struct5').val('');
    }else if(class2Val == 738||class2Val == 1827||class2Val == 1686){//总成738
        $('#struct4').empty();
        $('#struct4').val('');       
    }else if(class2Val == 1664){//其它1664
        $('#struct3').empty();
        $('#struct3').val('');      
    }else if(class2Val == 1641){//半品1641
        $('#struct4').empty();
        $('#struct4').val('');      
    }
    
    $('#modifymaterial-zc_part_number').val('');
}

/**
* 当结构的类型改变时part_type也跟着变
*/
function asynParttype() 
{
    var optElms = document.getElementById('modifymaterial-part_type');
    var ops = optElms.options;  
    for(var i=0;i<ops.length; i++){  
        var tempValue = ops[i].value;  
        if(tempValue == $('#struct1').find('option:selected').attr('remark'))  
        {  
            ops[i].selected = true;  
        }  
    }
}


/**
* 当选择二级分类时，要清空的一些框
*/
function class3Empty() {
    $("#modifymaterial-mer1").empty();
    $("#modifymaterial-mer2").empty();
    $("#modifymaterial-part_type").empty();
    $("#modifymaterial-mer4").val('');
    $("#modifymaterial-mer5").empty();
    $("#modifymaterial-mer6").empty();
    $("#modifymaterial-mer7").val('');
    $("#modifymaterial-mer8").val('');
    $("#modifymaterial-mer9").empty('');
    
    $('#modifymaterial-zc_part_number').val('');
    $('#modifymaterial-manufacturer').empty();
    $('#material-struct').empty();
    
    for(var i=0;i<10;i++)
    {
        var element = "#modifymaterial-mer" + i;
        $('.mer'+i).addClass('hide');
    }
}

/*生成螺钉物料编码*/
function generateScrewMaterialEncode(json, screw_material_encode_value) {
    var div_screw_material_encode_options=document.createElement("div");
    //div_screw_material_encode_options.setAttribute("class", "row");
    div_screw_material_encode_options.setAttribute("id", "screw_material_encode_options");
    $("#modifymaterial-zc_part_number").parents(".col-md-4").before(div_screw_material_encode_options);
    var i=0;
    //把返回的数据显示到下拉框和文本框里
    $.each(json.data, function(name,value) {
        i++;
        if (value==''){//说明是文本框
            //创建文本框元素节点
            var label=name;
            var screw_material_encode_options_i='screw_material_encode_options'+i;
            var screw_material_encode_option=document.createElement("div");  // 通过 DOM 创建元素
            screw_material_encode_option.setAttribute("class", "col-md-2");
            screw_material_encode_option.setAttribute("id", screw_material_encode_options_i);
            screw_material_encode_option.innerHTML='<div class="form-group field-modifymaterial-'+screw_material_encode_options_i+'">\
            <label class="control-label" for="modifymaterial-'+screw_material_encode_options_i+'">'+label+'</label>\
            <input type="text" id="modifymaterial-'+screw_material_encode_options_i+'" class="form-control" name="ModifyMaterial['+screw_material_encode_options_i+']" value="'+screw_material_encode_value[i]+'">\
            <div class="help-block"></div>\
            </div>';
            var tmp=document.getElementById("screw_material_encode_options");
            tmp.appendChild(screw_material_encode_option);
        }
        else if(name == '厂家')
        { 
            // var option = "<option value=\"\">" + "请选择" + name + "..." + "</option>";
            // $('#modifymaterial-manufacturer').append(option);  
            // $.each(value, function(key,val) {
            //     option = "<option value=\"" + val.id + "\">" + val.name + "</option>";
            //     $('#modifymaterial-manufacturer').append(option);  
            // });
            //
        }
        else{//说明是下拉框，创建下拉框元素节点并向下拉框里添加数据
            var label=name;
            var screw_material_encode_options_i='screw_material_encode_options'+i;
            var screw_material_encode_option=document.createElement("div");  // 通过 DOM 创建元素
            screw_material_encode_option.setAttribute("class", "col-md-2");
            screw_material_encode_option.setAttribute("id", screw_material_encode_options_i);
            screw_material_encode_option.innerHTML='<div class="form-group field-modifymaterial-'+screw_material_encode_options_i+'">\
            <label class="control-label" for="modifymaterial-'+screw_material_encode_options_i+'">'+label+'</label>\
            <select id="modifymaterial-'+screw_material_encode_options_i+'" class="form-control" name="ModifyMaterial['+screw_material_encode_options_i+']">\
            </select>\
            <div class="help-block"></div>\
            </div>';
            var tmp=document.getElementById("screw_material_encode_options");
            tmp.appendChild(screw_material_encode_option);
            
            var element = "#modifymaterial-" + screw_material_encode_options_i;
            var option = "<option value=\"\">" + "请选择" + name + "..." + "</option>";
            $(element).append(option);
            //向下拉框里添加数据 
            $.each(value, function(key,val) {
                var select = "";
                if (screw_material_encode_value && val.remark.trim() == screw_material_encode_value[i]) {
                    select = " selected";
                }
                option = "<option value=\"" + val.remark + "\"" + select + ">" + val.name + "</option>";
                $(element).append(option);
                //如果是#modifymaterial-screw_material_encode_options1，则同时也创建#modifymaterial-part_type的下拉信息，作为分类选项
                if (i == 1){
                    option = "<option value=\"" + val.id + "\"" + select + ">" + val.name + "</option>";
                    $("#modifymaterial-part_type").append(option);
                }
            });
        }
    });
    
    //定义螺钉物料编码各选项的改变事件，并生成一供智车料号
    defineScrewMaterialEncodeOptionsChangeEvent(i);
}

/*定义螺钉物料编码各选项的改变事件，并生成一供智车料号*/
function defineScrewMaterialEncodeOptionsChangeEvent(length) {
    length--;//宋崴加（不加会有报错问题）
  for(var i=1;i<=length;i++){
      $('#modifymaterial-screw_material_encode_options'+i).change(function() {
        try{
            var option_value = $(this).val();
            option_value = option_value.trim(); //移除字符串首尾空白
            if (option_value){
                //当是#modifymaterial-screw_material_encode_options2或#modifymaterial-screw_material_encode_options3的change事件时，它们的值必须是两位数字，否则去错误处理
                //当是#modifymaterial-screw_material_encode_options8或#modifymaterial-screw_material_encode_options9的change事件时，它们的值必须是一位数字，否则去错误处理
                //当是#modifymaterial-screw_material_encode_options1的change事件时，将#modifymaterial-part_type的选择设置为与其相同
                if ($(this).attr('id') == 'modifymaterial-screw_material_encode_options2' || $(this).attr('id') == 'modifymaterial-screw_material_encode_options3'){
                    var patt=new RegExp(/^[0-9][0-9]/);
                    if (!patt.test(option_value) || option_value.length != 2){
                        $(this).val("");
                        throw "长度必须是两位数字";
                    }
                }else if($(this).attr('id') == 'modifymaterial-screw_material_encode_options8' || $(this).attr('id') == 'modifymaterial-screw_material_encode_options9') {
                    var patt=new RegExp(/^[0-9]/);
                    if (!patt.test(option_value) || option_value.length != 1){
                        $(this).val("");
                        throw "长度必须是一位数字";
                    }
                }else if($(this).attr('id') == 'modifymaterial-screw_material_encode_options1') {
                    var str = $(this).find('option:selected').text();
                    obj = document.getElementById("modifymaterial-part_type");
                    for (i=0;i<obj.length;i++){
                        if (obj[i].text == str)
                            obj[i].selected = true;
                    }
                }
                //将各项值依次连接起来，生成一供智车料号
                var screw_material_encode = "";

                for (var i=1;i<=length;i++)
                {
                    option_value = $('#modifymaterial-screw_material_encode_options' +i).val();
                    option_value = option_value.trim(); //移除字符串首尾空白
                    if (option_value){
                        screw_material_encode = screw_material_encode + option_value;
                    } else {//如果螺钉物料编码各项有任何一项内容值为空去错误处理
                        throw "";
                    }
                }  

                $("#modifymaterial-zc_part_number").val(screw_material_encode);
                //检查该一供智车料号是否已存在，如存在清空一供智车料号，并弹出错误提示框
                $.ajax({
                    type: "GET",
                    url: "$checkZcPartNo",
                    data: {zcPartNo:screw_material_encode},
                    async:true,
                    dataType:'json',
                    success: function(json) {
                        if(json.status == 0){
                            $("#modifymaterial-zc_part_number").val("");
                            krajeeDialog.alert(json.message);
                        }
                    },
                    timeout: 3000,
                    error: function(){
                        alert("错误：请求超时无响应");
                    }
                });
            }else{ //当前值为空去错误处理
                throw "";
            }
        } catch(err) { //清空一供智车料号，并弹出错误提示框
            $("#modifymaterial-zc_part_number").val("");
            if (err != "") {krajeeDialog.alert(err);}
            return;
        }
      });
  }
}

/*通过添加物料描述的各项数据自动生成描述信息*/
$("#modifymaterial-part_type").change(partTypeChange);

/**
* part_type的change函数
*/
function partTypeChange() {
    needWhichDrop();
    //先清空物料描述
    if(!$isUpdate)//如果是更新
        $("#modifymaterial-description").val("");
    //创建一个数组，包含8个空数据，代表物料描述的8个组成项
    var description_value = new Array();
    for(var i=0;i<8;i++){
        description_value[i] = "";
    }
    var class3Val = $("#modifymaterial-part_type").val();
    if (class3Val != "")
        getDescriptionOptionsByClass3id(class3Val, description_value);
}

/* 获得物料描述的各项数据 */
function getDescriptionOptionsByClass3id(class3Val, description_value) {
    $.get(
        "$get_description_options_by_class3id",
        {class3Val:class3Val},//传的id参数
        function(json) {//返回数据后
            if(json.status == 1){//如果是正常物料描述各项（除PCB和PCBA）返回成功
                //先清除#description_options及其所有子元素节点以便清除相关数据，再重新在#modifymaterial-description前面创建#description_options元素节点
                $("#description_options").remove();
                var div_description_options=document.createElement("div");
                div_description_options.setAttribute("class", "row");
                div_description_options.setAttribute("id", "description_options");
                $("#modifymaterial-description").parents(".row").before(div_description_options);
                var i=0;
                //把返回的数据显示到下拉框和文本框里
                $.each(json.data, function(name,value) {
                    i++;
                    if (value==''){//说明是文本框
                        //如果是“无”加一位数字的name，说明该项不需要，返回，不创建文本框
                        var patt=new RegExp(/无[0-9]/);
                        if (patt.test(name) && name.length == 2){
                            return;
                        }
                        //创建文本框元素节点
                        var label=name;
                        var description_options_i='description_options'+i;
                        var description_option=document.createElement("div");  // 通过 DOM 创建元素
                        description_option.setAttribute("class", "col-md-2");
                        description_option.setAttribute("id", description_options_i);
                        description_option.innerHTML='<div class="form-group field-modifymaterial-'+description_options_i+'">\
                        <label class="control-label" for="modifymaterial-'+description_options_i+'">'+label+'</label>\
                        <input type="text" id="modifymaterial-'+description_options_i+'" class="form-control" name="ModifyMaterial['+description_options_i+']" value="'+description_value[i]+'">\
                        <div class="help-block"></div>\
                        </div>';
                        var tmp=document.getElementById("description_options");
                        tmp.appendChild(description_option);
                    }else{//说明是下拉框，创建下拉框元素节点并向下拉框里添加数据
                        var label=name;
                        var description_options_i='description_options'+i;
                        var description_option=document.createElement("div");  // 通过 DOM 创建元素
                        description_option.setAttribute("class", "col-md-2");
                        description_option.setAttribute("id", description_options_i);
                        description_option.innerHTML='<div class="form-group field-modifymaterial-'+description_options_i+'">\
                        <label class="control-label" for="modifymaterial-'+description_options_i+'">'+label+'</label>\
                        <select id="modifymaterial-'+description_options_i+'" class="form-control" name="ModifyMaterial['+description_options_i+']">\
                        </select>\
                        <div class="help-block"></div>\
                        </div>';
                        var tmp=document.getElementById("description_options");
                        tmp.appendChild(description_option);
                        
                        var element = "#modifymaterial-" + description_options_i; 
                        var option = "<option value=\"\">" + "请选择" + name + "..." + "</option>";
                        $(element).append(option);
                        //向下拉框里添加数据 
                        $.each(value, function(key,val) {
                            var select = "";
                            if (description_value && val.name.trim() == description_value[i]) {
                                select = " selected";
                            }
                            option = "<option value=\"" + val.name + "\"" + select + ">" + val.name + "</option>";
                            $(element).append(option);
                        });
                    }
                });
                if($isUpdate){//如果是更新
                    var descriptionVal = "{$model->description}";
                    if(class3Val == 453){//pcba
                        var num = descriptionVal.indexOf('_');
                        $('#modifymaterial-description_options1').val(descriptionVal.substr(0,num));
                        descriptionVal = descriptionVal.substr(num+1);
                        
                        num = descriptionVal.indexOf('_');
                        $('#modifymaterial-description_options2').val(descriptionVal.substr(0,num));
                        descriptionVal = descriptionVal.substr(num+1);

                        num = descriptionVal.indexOf('PCBA（');
                        $('#modifymaterial-description_options3').val(descriptionVal.substr(0,num));
                        descriptionVal = descriptionVal.substr(num+5);
                     
                        num = descriptionVal.indexOf('）');
                        $('#modifymaterial-description_options4').val(descriptionVal.substr(0,num));
                        descriptionVal = descriptionVal.substr(num);
                    }else if(class3Val == 1093){//pcb
                        var num = descriptionVal.indexOf('_');
                        $('#modifymaterial-description_options1').val(descriptionVal.substr(0,num));
                        descriptionVal = descriptionVal.substr(num+1);
                        
                        num = descriptionVal.indexOf('_');
                        $('#modifymaterial-description_options2').val(descriptionVal.substr(0,num));
                        descriptionVal = descriptionVal.substr(num+1);

                        num = descriptionVal.indexOf('PCB_');
                        $('#modifymaterial-description_options3').val(descriptionVal.substr(0,num));
                        descriptionVal = descriptionVal.substr(num+4);
                                          
                        num = descriptionVal.indexOf('层（');
                        $('#modifymaterial-description_options4').val(descriptionVal.substr(0,num));
                        descriptionVal = descriptionVal.substr(num+2);                        
                     
                        num = descriptionVal.indexOf('）');
                        $('#modifymaterial-description_options5').val(descriptionVal.substr(0,num));
                    }
                    
                }

                //定义物料描述各选项的改变事件，并生成物料描述
                defineDescriptionOptionsChangeEvent(i,class3Val);
            }
            else
                krajeeDialog.alert(json.message);
        },
        "json"
    );
}


//定义物料描述各选项的改变事件，并生成物料描述
function defineDescriptionOptionsChangeEvent(length,class3Val) {
  for(var i=1;i<=length;i++){
      $('#modifymaterial-description_options'+i).change(function() {
        try{
            var option_value = $(this).val();
            option_value = option_value.trim(); //移除字符串首尾空白
            //先判断值是否包含“_”字符，有则去错误处理
            var description = '';
            var patt=new RegExp(/_+/g);
            if (patt.test(option_value)){
                throw option_value+"不允许包含“_”字符";
            }

            if (option_value){//如果有值才去执行
                if(class3Val == 453){//pcba
                    for (var i=1;i<=length;i++)
                    {
                        if (document.getElementById('modifymaterial-description_options' +i)){
                            option_value = $('#modifymaterial-description_options' +i).val();
                            option_value = option_value.trim(); //移除字符串首尾空白
                            if (option_value){
                                //先判断值是否包含“_”字符，有则去错误处理
                                if(i == 4){
                                    description = description.substr(0,description.length-1);
                                    description = description + "PCBA（" + option_value + "）";                                    
                                }
                                else
                                    description = description + option_value + "_";
                            } else {//如果物料描述各项有任何一项内容值为空去错误处理
                                throw "";
                            }
                        } else {
                            throw "";
                        }
                    }
                    $("#modifymaterial-description").val(description);
                }else if(class3Val == 1093){//pcb                
                    for (var i=1;i<=length;i++)
                    {
                        if (document.getElementById('modifymaterial-description_options' +i)){
                            option_value = $('#modifymaterial-description_options' +i).val();
                            option_value = option_value.trim(); //移除字符串首尾空白
                            if (option_value){
                                //先判断值是否包含“_”字符，有则去错误处理
                                if(i == 4){
                                    description = description.substr(0,description.length-1);
                                    description = description + "PCB_" + option_value + "_";                                    
                                } else if(i == 5){
                                    description = description.substr(0,description.length-1);
                                    description = description + "层（" + option_value + "）";                                    
                                } else
                                    description = description + option_value + "_";
                            } else {//如果物料描述各项有任何一项内容值为空去错误处理
                                throw "";
                            }
                        } else {
                            throw "";
                        }
                    }
                    $("#modifymaterial-description").val(description);
                }else{//正常的电子物料
                    //将各项值以“_”字符依次连接起来，生产物料描述
                    description = $("#modifymaterial-part_type").find('option:selected').text();
                    for (var i=1;i<=length;i++)
                    {
                        if (document.getElementById('modifymaterial-description_options' +i)){
                            option_value = $('#modifymaterial-description_options' +i).val();
                            option_value = option_value.trim(); //移除字符串首尾空白
                            if (option_value){
                                //先判断值是否包含“_”字符，有则去错误处理
                                if (patt.test(option_value)){
                                    throw option_value+"不允许包含“_”字符";
                                }
                                description = description + "_" + option_value;
                            } else {//如果物料描述各项有任何一项内容值为空去错误处理
                                throw "";
                            }
                        } else {
                            description = description + "_";
                        }
                    }
                    $("#modifymaterial-description").val(description);
                }
            }else{ //当前值为空去错误处理
                throw "";
            }

            
        } catch(err) { //清空物料描述，并弹出错误提示框
            $("#modifymaterial-description").val("");
            if (err != "") {alert(err);}
            return;
        }     
      });
  }
}


/*物料编码的下拉框事件*/
for(var i=1;i<10;i++){
    if(i==4||i==7||i==8)
        continue;    
    $('#modifymaterial-mer'+i).change(function() {
        needWhichDrop();
    });     
}
//检测添入的input框的长度是否满足，不满足弹框
$('#modifymaterial-mer4').change(function() {
    var val = $(this).val();
    if(val.length!=inputLen[0])
    {
        $(this).val('');
        krajeeDialog.alert('长度应该是'+inputLen[0]+'位');
    }
   
    needWhichDrop();
}); 
$('#modifymaterial-mer7').change(function() {
    var val = $(this).val();
    if(val.length!=inputLen[1])
    {
        $(this).val('');
        krajeeDialog.alert('长度应该是'+inputLen[1]+'位');
    }
    needWhichDrop();
}); 
$('#modifymaterial-mer8').change(function() {
    var val = $(this).val();
    if(val.length!=inputLen[2])
    {
        $(this).val('');
        krajeeDialog.alert('长度应该是'+inputLen[2]+'位');
    }
    needWhichDrop();
}); 
 



  /*根据不同的组件需要哪几个控件去生成料号*/
  function needWhichDrop() {
    switch ($('#modifymaterial-class2').val())
    {   //选择value而不选择内容的原因是他们要是改规则的名字不会有影响
        case '5'://RES
            generateMaterialsCode([1,2,3,4,5],'RES',3);
            break;
        case '6'://CAP
            generateMaterialsCode([1,2,3,4,5,6],'CAP',3);
            break;
        case '7'://IND
            generateMaterialsCode([1,3,4],'IND',5);
            break;
        case '65'://BEAD
            generateMaterialsCode([1,3,5,7],'BEA',4);
            break;
        case '45'://Diode
            generateMaterialsCode([1,3],'DIO',7);
            break;
        case '66'://Triode
            generateMaterialsCode([1,3],'TRI',7);
            break;
        case '189'://MOS
            generateMaterialsCode([1,3],'MOS',9);
            break;
        case '204'://Fuse
            generateMaterialsCode([1,3],'FUS',7);
            break;
        case '205'://CONN
            generateMaterialsCode([1,3,5,6],'CON',5);
            break;
        case '206'://Crystal/Oscillator
            generateMaterialsCode([1,3],'CRY',9);
            break;
        case '207'://Spring
            generateMaterialsCode([1,3],'SPR',8);
            break;
        // case '208'://Buzzer
        //     generateMaterialsCode([1,3],'BUZ',10);
        //     break;
        case '8'://Analog IC
            generateMaterialsCode([1,3],'AIC',7);
            break;
        case '9'://Power IC
            generateMaterialsCode([1,3],'PIC',7);
            break;
        case '338'://PHY
            generateMaterialsCode([1,3],'ICH',7);
            break;
        case '396'://Memory
            generateMaterialsCode([1,3],'ICM',7);
            break;
        case '398'://AP
            generateMaterialsCode([1,3],'ICU',7);
            break;
        case '399'://Sensor
            generateMaterialsCode([1,3],'ICS',7);
            break;
        case '2060'://Secure IC
            generateMaterialsCode([1,3],'SIC',7);
            break;
        case '10'://Module/IC和ANT
        case '11':
            generateMaterialsCode([1,3],'ICR',7);
            break;
        case '397'://Video
            generateMaterialsCode([1,3],'ICV',6);
            break;
        case '551'://Battery
            generateMaterialsCode([1,3],'ICB',8);
            break;
        case '2000'://Buzzer
            generateMaterialsCode([1,3],'BUZ',7);
            break;
        case '1987'://RELAY
            generateMaterialsCode([1,3],'REL',7);
            break;

        case '443'://PCBA
            generateMaterialsCode([1,2,5,7,9],'',2);
            break;
        case '444'://PCB
            generateMaterialsCode([1,2,5,7,9],'',2);
            break;
    }
  }

  /**
  * 数字位不够，补零
* @param num:被补数
* @param n:补多少位
* @returns {*}:补完的数
*/
function padZore(num, n) {  
    var len = num.toString().length;  
    for(;len < n;len++)   
        num = "0" + num;   
    return num;  
}    
  
  /*根据输入的下拉框和电器元件生成不带流水的料号 */
/**
* 
* @param arr 是取哪几个框的数据
* @param component 流水号的第2，3，4位要添的
* @param num 流水号多少位
* @returns {boolean}智车的物料号
*/
function generateMaterialsCode(arr,component,num) {
  var strCode = '';
  var class2Val = $('#modifymaterial-class2').val();
  for(val in arr)
  { 
      var valueOption = $('#modifymaterial-mer'+arr[val]).val();
      
      if(arr[val] == 3)//当为3时，元素的id号是下面的
          valueOption = $('#modifymaterial-part_type').find('option:selected').attr('remark');
      if(val == 1)
          strCode = strCode + component;   
      if(valueOption)
          strCode += valueOption;
      else
      {     
          $('#modifymaterial-zc_part_number').val("");
          return false;
      }             
  }
  //pcb和pcba编码
  if(class2Val == 443)
      strCode = 'P'+strCode;
  if(class2Val == 444)
      strCode = 'B'+strCode;
  if(class2Val==444)
      var SerialNum = generateSerialNum(strCode,num,true);
  else
      var SerialNum = generateSerialNum(strCode,num);
  // if(class2Val == 443||class2Val == 444){//如果是pcba或pcb要查看是不是新初版（物料库里没有这个系列的板子）
  //     if(SerialNum != 0){
  //         alert('这个系列的板子已经存在了');
  //         $('#modifymaterial-zc_part_number').val("");
  //         return false;
  //     }
  // }
  if(class2Val == 444){//如果是pcb要第11位加，最后一位为0
      $('#modifymaterial-zc_part_number').val(strCode+SerialNum+'0');
      return true;
  }
  $('#modifymaterial-zc_part_number').val(strCode+padZore(SerialNum,num));   
}

/**
* 把带K,m,M的数字转成存数字
* 
*/
/*function turnKmM(valueOption)
{
    valueOption = valueOption.trim();
    var lastChar = valueOption.substr(valueOption.length-1,1);//最后一位字符
    alert(valueOption);
    valueOptio = parseFloat(valueOption);
    alert(valueOption);
    switch(lastChar)
    {
    case 'k':
    case 'K':
        valueOption = valueOption*1000;
        break;
    case 'M':
        valueOption = valueOption*1000000;
        break;
    case 'm':
        valueOption = valueOption/1000;
        break;
    }
    return valueOption;
}*/

  
/*查看输入的流水应该是多少号*/
function generateSerialNum(code,num,ispcb=false) {
    var sn='';
    var data = "code=" + code+"&num="+num;
    if(ispcb)
        data = data+"&ispcb=1";
    $.ajax({  
        type : "get",  
        url : "$getSerialNum",  
        data : data,  
        async : false,
        dataType : 'json',
        success : function(json) {
            if (json.status)
                sn=json.data;
            }
    });
    return sn;
}
    
/////////////////如果是更新界面时///////////////////
if($isUpdate)
{
    //为了让智车料号不可以更新让控件失效
    $('#modifymaterial-class1').prop('disabled','disabled');
    $('#modifymaterial-class2').prop('disabled','disabled');
    $('#modifymaterial-part_type').prop('disabled','disabled');
    /* 获得智车料号的数据 */
    var zcMaterialNum = $('#modifymaterial-zc_part_number').val();
    var modelClass2 = $('#modifymaterial-class2').val();
    inputMaxStr(modelClass2);
    $.get(
        "$get_class3_by_class2id",
        {class2Val:modelClass2},//传的参数 
        function(json) {//返回数据后   
            if(json.status == 1){//如果返回成功
                    
                var i=0;
                //把返回的数据显示到下拉框和文本框里
                $.each(json.data, function(name,value) {
                    if (value==''){//说明是文件框
                        i++;
                        for(;i<4;i++){//把文本框前的下拉框隐藏
                            var element = "#modifymaterial-mer" + i;
                            $('.mer'+i).addClass('hide');
                        }
                        //给文本框提示字符属性
                        i=4;
                        var element = "#modifymaterial-mer" + i;
                        $(element).attr('placeholder',"请输入" + name + "...");
                        $('.mer'+i).removeClass('hide');
                        $('#modifymaterial-mer'+i).prop('disabled','disabled');
                        $('.mer4').find("label").text(name);
                    }else if(name.indexOf('分类')>=0){
                        i++;
                        for(;i<3;i++){//把分类前的下拉框隐藏
                            var element = "#modifymaterial-mer" + i;
                            $('.mer'+i).addClass('hide');
                        }
                        i=3;   
                        var element = "#modifymaterial-part_type";
                        var option = "<option value=\"\">" + "请选择" + name + "..." + "</option>";
                        $(element).empty();
                        $(element).append(option);
                        $('.mer'+i).removeClass('hide');
                        $('.mer'+i).find("label").text(name);
                        //向下拉框里添加 数据 
                        for(x in value) {
                            if("$model->part_type" == value[x].id)
                                option = '<option value="'+value[x].id+'" remark="'+value[x].remark+'" selected>' + value[x].name + "</option>";
                            else
                                option = '<option value="'+value[x].id+'" remark="'+value[x].remark+'">' + value[x].name + "</option>";
                            $(element).append(option);
                        }
                    }else if(name.indexOf('厂家')>=0){
                        
                    }else{//说明是下拉框
                        i++;
                        if(i==4)
                            i++;
                        var element = "#modifymaterial-mer" + i; 
                        $('.mer'+i).removeClass('hide');
                        $('#modifymaterial-mer'+i).prop('disabled','disabled');
                        $('.mer'+i).find("label").text(name);
                    }
                });
                //把没有用到的隐藏起来！
                i++;
                for(;i<10;i++){
                   var element = "#modifymaterial-mer" + i;
                   $('.mer'+i).addClass('hide');
                }
                //让智车料号改不了
                
            }
            else if(json.status == 2)
            {
                //显示和隐藏生成智车料号的框（不用显示 了）
                // for(var i=0;i<10;i++)
                // {
                //     if(dataPCB.hasOwnProperty(i))
                //     {
                //         var element = "#modifymaterial-mer"+i;
                //         $(element).attr('placeholder',"请输入" + dataPCB[i] + "...");
                //         $('.mer'+i).removeClass('hide');
                //         $('#modifymaterial-mer'+i).prop('disabled','disabled');
                //         $('.mer'+i).find("label").text(dataPCB[i]);     
                //     }
                //     else
                //         $('.mer'+i).addClass('hide');
                // }
                //物料类型
                $.each(json.data, function(name,value) {
                    if(name.indexOf('分类')>=0){
                        $('.mer3').find("label").text(name);
                        //向下拉框里添加 数据 
                        for(x in value) {
                            option = '<option value="'+value[x].id+'" remark="'+value[x].remark+'" selected>' + value[x].name + "</option>";
                            $("#modifymaterial-part_type").append(option);
                        }
                    }
                });
                //partTypeChange();                    
            }
            else if(json.status == 3)//当是结构物料时
            {
                //如果是螺钉id=630，执行螺钉相关的函数
                if (modelClass2 == 630){
                    var screw_material_encode_value = new Array();
                    screw_material_encode_value[1] = zcMaterialNum.substr(0,2);
                    screw_material_encode_value[2] = zcMaterialNum.substr(2,2);
                    screw_material_encode_value[3] = zcMaterialNum.substr(4,2);
                    screw_material_encode_value[4] = zcMaterialNum.substr(6,1);
                    screw_material_encode_value[5] = zcMaterialNum.substr(7,1);
                    screw_material_encode_value[6] = zcMaterialNum.substr(8,1);
                    screw_material_encode_value[7] = zcMaterialNum.substr(9,1);
                    screw_material_encode_value[8] = zcMaterialNum.substr(10,1);
                    screw_material_encode_value[9] = zcMaterialNum.substr(11,1);
                    //将不是生成螺钉物料编码的其它编码选项隐藏
                    for(var i=1;i<10;i++){
                        var element = "#modifymaterial-mer" + i;
                        $('.mer'+i).addClass('hide');
                    }
                    //执行螺钉相关的函数
                    generateScrewMaterialEncode(json, screw_material_encode_value);
                } else {
                    var html='';                
                    //加类型
                    var i=0;
                    $.each(json.data, function(name,value) {
                        i++;
                        if(name == '厂家')
                        {}
                        else if (value!=''){//下拉框
                            html += '<div class="col-md-2 form-group"><label>'+name+'</label><select id="struct'+i+'" class="form-control">';
                            html += "<option remark=\"\" value=\"\">" + "请选择" + name + "..." + "</option>";
                            if(i==1)
                                $("#modifymaterial-part_type").append('<option value="">空</option>');//加一行空的数据
                            $.each(value, function(key,val) {
                                //给part_type数据
                                if(i==1){
                                    html += '<option '+('$model->part_type' == val.id?'selected':'')+
                                    ' remark="'+val.id+'" value="' + val.remark + '">' + val.name + '</option>';
                                    
                                    var option = '<option '+('$model->part_type' == val.id?'selected':'')+
                                    ' value="'+val.id+'">' + val.name + "</option>";
                                    
                                    $("#modifymaterial-part_type").append(option);
                                }else{
                                    if(modelClass2 == 1668){//包装
                                        if(i == 2){
                                            html += '<option '+(zcMaterialNum.substr(2,2) == val.remark?'selected':'')+
                                                ' remark="'+val.id+'" value="' + val.remark + '">' + val.name + '</option>';   
                                        }else if(i == 3){
                                            html += '<option '+(zcMaterialNum.substr(4,2) == val.remark?'selected':'')+
                                                ' remark="'+val.id+'" value="' + val.remark + '">' + val.name + '</option>';                                           
                                        }
                                    }
                                }
                            });
                            html += '</select></div>';
                        }
                    });
                    //////
                    var strTemp = '';
                    
                    if(modelClass2 == 613||modelClass2 == 738||modelClass2 == 1827||modelClass2 == 1686){
                        strTemp = zcMaterialNum.substr(2,2);
                        //加预留位
                        html += '<div class="col-md-2 form-group"><label>预留位</label>' +
                         '<input id="struct2" readonly="readonly" class="form-control" value="'+strTemp+'" type="text" maxlength="2"> </div>';
                        //加项目编号
                        strTemp = zcMaterialNum.substr(4,2);
                        
                        html += '<div class="col-md-2 form-group"><label>项目编号</label>' +
                         '<select id="struct3" class="form-control">'+getPjtName(strTemp)+'</select></div>';

                        //
                        // html += '<div class="col-md-2 form-group"><label>项目编号</label>' +
                        //  '<input id="struct3" class="form-control" value="'+strTemp+'" type="text" maxlength="2"> </div>';
                    }
                    //加流水
                    if(modelClass2 == 613){//正常结构物料 
                        strTemp = zcMaterialNum.substr(6,3);
                        html += '<div class="col-md-2 form-group"><label>模具号</label>' +
                                '<label id="struct4-checkbox" style="float:right"><input checked="" type="checkbox">&nbsp;新建</label>' +
                                '<input value="'+strTemp+'" id="struct4" readonly="" class="form-control" type="text"></div>';
                        strTemp = zcMaterialNum.substr(9,2);
                        html += '<div class="col-md-2 form-group"><label>模仁号/流水号</label>' +
                                '<label id="struct5-checkbox" style="float:right"><input checked="" type="checkbox">&nbsp;新建</label>' +
                                '<input value="'+strTemp+'" id="struct5" readonly="" class="form-control" type="text"></div>';
                    }else if(modelClass2 == 738||modelClass2 == 1827){//总成物料
                        strTemp = zcMaterialNum.substr(6,3);
                        html += '<div class="col-md-2 form-group"><label>流水号</label>' +
                                '<label id="struct4-checkbox" style="float:right"><input checked="" type="checkbox">&nbsp;新建</label>' +
                                '<input value="'+strTemp+'" id="struct4" readonly="" class="form-control" type="text"></div>';
                    }else if(modelClass2 == 1686){//成品出货
                        strTemp = zcMaterialNum.substr(6,4);
                        html += '<div class="col-md-2 form-group"><label>流水号</label>' +
                                '<label id="struct4-checkbox" style="float:right"><input checked="" type="checkbox">&nbsp;新建</label>' +
                                '<input value="'+strTemp+'" id="struct4" readonly="" class="form-control" type="text"></div>';
                    }
                    //////////////////其它1664/////////////
                    if(modelClass2 == 1664){
                        //加预留位
                        strTemp = zcMaterialNum.substr(2,5);
                        html += '<div class="col-md-2 form-group"><label>首次使用机型型号(00S10)</label>' +
                         '<input value="'+strTemp+'" id="struct2" class="form-control" type="text" maxlength="5"> </div>';
                        strTemp = zcMaterialNum.substr(7,3);
                        html += '<div class="col-md-2 form-group"><label>流水号</label>' +
                         '<label id="struct3-checkbox" style="float:right"><input checked="" type="checkbox">&nbsp;新建</label>' +
                         '<input value="'+strTemp+'" id="struct3" readonly="" class="form-control" type="text"></div>';
                    }
                    //////////////////半品1641/////////////
                    if(modelClass2 == 1641){
                        strTemp = zcMaterialNum.substr(3,2);
                        //加预留位
                        html += '<div class="col-md-2 form-group"><label>预留位</label>' +
                         '<input id="struct2" class="form-control" readonly="readonly" value="'+strTemp+'" type="text" maxlength="2"> </div>';
                        strTemp = zcMaterialNum.substr(5,2);
                        html += '<div class="col-md-2 form-group"><label>项目编号</label>' +
                         '<select id="struct3" class="form-control">'+getPjtName(strTemp)+'</select></div>'
                        strTemp = zcMaterialNum.substr(7,4);
                        html += '<div class="col-md-2 form-group"><label>流水号</label>' +
                         '<label id="struct4-checkbox" style="float:right"><input checked="" type="checkbox">&nbsp;新建</label>' +
                         '<input value="'+strTemp+'" id="struct4" readonly="" class="form-control" type="text"></div>';
                    }
                    //////////////////包装1668/////////////
                    if(modelClass2 == 1668){
                        //加预留位
                        strTemp = zcMaterialNum.substr(6,2);
                        html += '<div class="col-md-2 form-group"><label>预留位</label>' +
                         '<input readonly="readonly" value="'+strTemp+'" id="struct4" class="form-control" type="text" maxlength="2"> </div>';
                    }
                    $('#material-struct').append(html); 
                    //当编智车料号的前三个输入框有变化时，要清空后面的输入框和智车料号
                    if(modelClass2!=1668){
                        $('#struct1').on('change',emptyStructSN);
                        $('#struct2').on('change',emptyStructSN);
                        if(modelClass2 != 1664)
                            $('#struct3').on('change',emptyStructSN);
                    }else{
                        $('#struct1').on('change',generateStructCode);
                        $('#struct2').on('change',generateStructCode);
                        $('#struct3').on('change',generateStructCode);
                        $('#struct4').on('change',generateStructCode);
                    }
                    //input框失去焦点事件:检查长度
                    $('#struct2').on('blur',checkInputLen);
                    $('#struct3').on('blur',checkInputLen);
                    $('#struct4').on('blur',checkInputLen);
                    
                    //新建的checkbox的事件
                    if(modelClass2 == 613){//部件
                        //点击复选框的事件
                        $('#struct4-checkbox').on('change',{class2Val:modelClass2,id:4},getStructNo);
                        $('#struct5-checkbox').on('change',{class2Val:modelClass2,id:5},getStructNo);
                    }else if(modelClass2 == 738||modelClass2 == 1827||modelClass2 == 1641||modelClass2 == 1686){//半品和总成
                        $('#struct4-checkbox').on('change',{class2Val:modelClass2,id:4},getStructNo);                        
                    }else if(modelClass2 == 1664){//其它
                        $('#struct3-checkbox').on('change',{class2Val:modelClass2,id:3},getStructNo);                                                
                    }
                    //让控件失效
                    $('#struct1').prop('disabled','disabled');
                    $('#struct2').prop('disabled','disabled');
                    $('#struct3').prop('disabled','disabled');
                    $('#struct4').prop('disabled','disabled');
                    $('#struct5').prop('disabled','disabled');
                    $('#struct3-checkbox input').prop('disabled','disabled');
                    $('#struct4-checkbox input').prop('disabled','disabled');
                    $('#struct5-checkbox input').prop('disabled','disabled');
                }
            }
            else
                krajeeDialog.alert(json.message);
        },
        "json"
    );
    
    /* 获得物料描述的各项数据 */
    var class1Val = $('#modifymaterial-class1').val();
    
    //如果是结构料(id=612)，显示part_name和unit字段；
    if (class1Val == 612){
        $('.material-structure').removeClass('hide');
        $('.material-electronic').addClass('hide');
        $('#modifymaterial-is_first_mfr').prop("checked", false);
        $('.field-modifymaterial-is_first_mfr').addClass('hide');
    }else{
        $('.material-structure').addClass('hide');
        $('.material-electronic').removeClass('hide');
        $('#modifymaterial-is_first_mfr').prop("checked", true);
        $('.field-modifymaterial-is_first_mfr').removeClass('hide');
        var description_value = $('#modifymaterial-description').val();
        //更新描述
        description_value = description_value.split("_"); //将物料描述以"_"分隔符拆分成字符串数组
        var class3Val = "$model->part_type";
        getDescriptionOptionsByClass3id(class3Val, description_value);
    }
}

function getPjtName(strTemp)
{
    if(strTemp == 'S1')
        return '<option value="S1">Smart 1C</option>';
    else if(strTemp == 'S2')
        return '<option value="S2">M11开发项目</option>';
    else if(strTemp == 'S3')
        return '<option value="S3">FDC开发项目</option>';
}
JS;
$this->registerJs($js);


///////////////////////////////添完二三四供的料号去数据里看是否存在////////////////////////

echo Html::a('查看', '#', [
    'id' => 'view',
    'data-toggle' => 'modal',
    'data-target' => '#create-modal',
    'class' => 'btn btn-success hidden',
]);
Modal::begin([
    'id' => 'create-modal',
    'header' => '<h4 class="modal-title">查看</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]);
Modal::end();
//////////////
Modal::begin([
    'id' => 'mtr-file-modal',
    'size'=>"modal-lg",
    'header' => '<h4 class="modal-title">选择物料附件</h4>',
    'footer' => '<a href="#" class="btn btn-primary" id="ok-choice-file" data-dismiss="modal">确定</a>
<a href="#" class="btn btn-primary" data-dismiss="modal">取消</a>',
]);
Modal::end();

$getMfrByPartno = Url::toRoute('/modify-material/get-mfr-by-partno');
$checkMfrPartNo = Url::toRoute('/modify-material/check-mfr-part-no');//检测是否有相同的厂家料号
$js = <<<JS
//////二三四供失去焦点时的事件//////

    var viewPath = '';
    $('#modifymaterial-mfrpartno2').blur(modalDlg);
    $('#modifymaterial-mfrpartno3').blur(modalDlg);
    $('#modifymaterial-mfrpartno4').blur(modalDlg);
    function modalDlg()
    {
        var zcPartNoVal = $(this).val();
        var mfrPartNoObj = $(this);
        if(zcPartNoVal.replace(/(^\s*)/g, "") == "")//去空格
            return;
        $.ajax({
            type:"get",
            url:"$getPartData",
            data:"zcPartNo=" + zcPartNoVal,
            async:false,
            dataType:'json',
            success:function(json) {
                if(json.status == 1){
                    if(confirm("你是否要打开该物料的信息")){
                        $.get('/materials/'+json.data.id+'?modal=1', {},
                           function (data) {
                               $('#create-modal .modal-body').html(data);
                           } 
                        );
                        $('#view').click();
                    }
                }else{
                    krajeeDialog.alert("你输入的智车料号不存在");
                    mfrPartNoObj.val("");
                }
            }
        });
    }
/////////一供失去焦点时的事件////////////
    $('#modifymaterial-mfr_part_number').on('blur',function() {
        $.get('$getMfrByPartno',{mfrPartNo:$('#modifymaterial-mfr_part_number').val()},
            function(json) {
                if(json.status == 1)//说明有相同的，要清除这个编辑框
                {
                    krajeeDialog.alert(json.message);
                }
            },'json'
        ); 
    });
    
    $('#modifymaterial-manufacturer').on('change',function() {
        var partNo = $('#modifymaterial-mfr_part_number').val();
        var mfr = $('#modifymaterial-manufacturer').val();
        if(partNo!=''&&mfr!='')
        {
            $.get('$checkMfrPartNo',{mfrPartNo:partNo,mfr:mfr},
                function(json) {
                    if(json.status == 1)//说明有相同的，要清除这个编辑框
                    {
                        krajeeDialog.alert(json.message);
                        $('#modifymaterial-manufacturer').val('');
                    }
                },'json'
            );
        }  
    });

///////////////////增加上传的插件///////////////////////   

var countUpload = 1;
var mtrFileId = 0;

//增加物料附件上传按钮
$('.add-upload').on('click',function() {
    
      $('#upload').append('<div class="row" id="del-upload'+countUpload+'">' +
      
           '<div class="col-md-2"><label class="control-label">文件类名</label>' +
            '<select name="fileClassNameA[]" class="select-file-name" ><option></option></select></div>' +
            
            '<div class="col-md-4"><label class="control-label">附件</label>' +
            '<input type="file" class="file" name="attachment[]" data-show-preview="false"></div>' +
           '<div class="col-md-4"><label class="control-label">备注</label>' +
            '<input type="text" class="form-control" name="attachment_remarkA[]"></div>' +
            '<div class="col-md-1"><label class="control-label">&emsp;</label>' +
             '<input type="button" class="btn btn-default form-control" value="删除附件" id="btn-del-upload'+countUpload+'" ></div>'+
       '</div>');
      $('.file').fileinput({
          'data-show-preview':false,
          'showUpload':false
          
      });
      
      $('.select-file-name').select2({
          theme:'krajee',
          width:'100%',
          data:{$jsonFileClassName},
          placeholder: "选择文件类"
      });
      
      $('#btn-del-upload'+countUpload).on('click',function() {
            $(this).parent().parent().remove();
      });
      countUpload++;
});

//增加物料附件复用按钮
$('.add-copy-upload').on('click',function() {
    $('#copy-upload').append('<div class="row" id="del-copy-upload'+countUpload+'">' +
    
         '<div class="col-md-2"><label class="control-label">文件类名</label>' +
          '<select name="fileClassNameB[]" class="select-file-name" placeholder="asdf" ><option></option></select></div>' +
                      
         '<div class="col-md-2"><label class="control-label">复用物料</label>' +
          '<select index="'+countUpload+'" id="select-copy-upload'+countUpload+'" class="select-refer-mtr" ><option></option></select></div>' +
    
         '<div class="col-md-3"><label class="control-label">复用的附件</label>' +
          '<input type="text" id="input-copy-upload'+countUpload+'"  readonly="readonly" class="form-control attachment-name"></div>' +
          
         '<div class="col-md-4 hide"><input name="attachmentId[]" id="input-hide-copy-upload'+countUpload+'" type="text"></div>' +
                      
         '<div class="col-md-4"><label class="control-label">备注</label>' +
          '<input type="text" class="form-control" name="attachment_remarkB[]"></div>' +
          '<div class="col-md-1"><label class="control-label">&emsp;</label>' +
           '<input type="button" class="btn btn-default form-control" value="删除附件" id="btn-del-copy-upload'+countUpload+'" ></div>'+
     '</div>');
    
    $('.select-file-name').select2({
        theme:'krajee',
        width:'100%',
        data:{$jsonFileClassName},
        placeholder: "选择文件类"
    
    });
    
    var selectCopyUpload = $('#select-copy-upload'+countUpload);
    
    selectCopyUpload.select2({
        theme:'krajee',
        width:'100%',
        data:{$jsonAllMtr},
        placeholder: "选择复用物料"
    });
    //.val(2837).trigger("change");
    selectCopyUpload.on('change',function() {
        $.post('/modify-material/mtr-file',{id:$(this).val()},function(obj) {
            
            $('#mtr-file-modal .modal-body').html(obj);  
            $('#mtr-file-modal').modal({
                  show:true
            });
            mtrFileId = selectCopyUpload.attr('index');
        }); 
    });
    
    
    $('#btn-del-copy-upload'+countUpload).on('click',function() {
        $(this).parent().parent().remove();
    });
    countUpload++;
});

if($isUpgrade){//如果是物料升级，显示升级的附件在复用处
    var dataTemp = {$dataAttachment};
    for(var i in dataTemp){
       $('#copy-upload').append('<div class="row" id="del-copy-upload'+countUpload+'">' +
   
        '<div class="col-md-2"><label class="control-label">文件类名</label>' +
         '<select name="fileClassNameB[]" class="select-file-name" placeholder="asdf" ><option></option></select></div>' +
                     
        '<div class="col-md-2"><label class="control-label">复用物料</label>' +
         '<select index="'+countUpload+'" id="select-copy-upload'+countUpload+'" class="select-refer-mtr" ><option></option></select></div>' +
   
        '<div class="col-md-3"><label class="control-label">复用的附件</label>' +
         '<input type="text" id="input-copy-upload'+countUpload+'"  readonly="readonly" ' +
          'value="'+dataTemp[i].name+'" class="form-control attachment-name"></div>' +
         
        '<div class="col-md-4 hide"><input name="attachmentId[]" id="input-hide-copy-upload'+countUpload+
        '" type="text" value="'+dataTemp[i].id+'"></div>' +
                     
        '<div class="col-md-4"><label class="control-label">备注</label>' +
         '<input type="text" value="'+dataTemp[i].remark+'" class="form-control" name="attachment_remarkB[]"></div>' +
         '<div class="col-md-1"><label class="control-label">&emsp;</label>' +
          '<input type="button" class="btn btn-default form-control" value="删除附件" id="btn-del-copy-upload'+countUpload+'" ></div>'+
        '</div>');
   
        $('#del-copy-upload'+countUpload +' .select-file-name').select2({
            theme:'krajee',
            width:'100%',
            data:{$jsonFileClassName},
            placeholder: "选择文件类"
        
        }).val(dataTemp[i].file_class_name).trigger("change");
        $('#input-hide-copy-upload'+countUpload).val(dataTemp[i].id);
        var selectCopyUpload = $('#select-copy-upload'+countUpload);
        
        selectCopyUpload.select2({
            theme:'krajee',
            width:'100%',
            data:{$jsonAllMtr},
            placeholder: "选择复用物料"
        }).val(dataTemp[i].material_id).trigger("change");
        selectCopyUpload.on('change',function() {
            $.post('/modify-material/mtr-file',{id:$(this).val()},function(obj) {
                
                $('#mtr-file-modal .modal-body').html(obj);  
                $('#mtr-file-modal').modal({
                      show:true
                });
                mtrFileId = selectCopyUpload.attr('index');
            }); 
        });
        
        $('#btn-del-copy-upload'+countUpload).on('click',function() {
            $(this).parent().parent().remove();
        });
        countUpload++;
    }
}

//选择文件模态对话框的确定按钮
$('#ok-choice-file').on('click',function() {
   
    $('#input-hide-copy-upload'+mtrFileId).val($('input[name="kvradio"]:checked').val());
    $('#input-copy-upload'+mtrFileId).val($('input[name="kvradio"]:checked').parent().next().text());
});

$('.delAtc').on('click',function() {
    var url = $(this).attr('href');
    var btnDel = $(this);

    krajeeDialog.confirm("您确定要删除此项吗？", function (result) {
        if (result) {
            $.get(url,function(obj) {
                if(obj.status == 1){
                    btnDel.parents("tr").remove();
                }else{
                    alert('删除失败');
                }  
                
            },'json');
        }
    });
    return false;
});

//下载模板
$(".download-temp").on('click',function(){
    var tempCalss1 = $("#modifymaterial-class1").val();
    var tempCalss2 = $("#modifymaterial-class2").val();
    var tempPartType = $("#modifymaterial-part_type").val();
    if(tempCalss1.trim() == ''){
        krajeeDialog.alert('一级分类不能为空');
    }
    if(tempCalss2.trim() == ''){
        krajeeDialog.alert('二级分类不能为空');
    }else{
        if(tempPartType.trim() == ''){
            krajeeDialog.alert($("#modifymaterial-part_type").prev().text()+'不能为空');
        }else{
            window.open('/modify-material/get-temp?id2='+tempCalss2+'&id3='+tempPartType);
            // $.get("/modify-material/get-temp",{id2:tempCalss2,id3:tempPartType},function(obj) {
            //     if(obj.status == 1){
            //         btnDel.parents("tr").remove();
            //     }else{
            //         alert('删除失败');
            //     }  
            //    
            // },'json');
        }
    }

    
});

JS;
$this->registerJs($js);


?>
