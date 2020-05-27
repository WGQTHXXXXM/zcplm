<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model frontend\models\Materials */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="materials-form">

    <?php $form = ActiveForm::begin();//var_dump($class2) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'class1')->dropDownList(empty($class1)? [] : ArrayHelper::map($class1, 'root', 'name'), ['prompt' => '请选择一级分类 ...']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'class2')->dropDownList(empty($class2)? [] : ArrayHelper::map($class2, 'id', 'name'), ['prompt' => '请选择二级分类 ...']) ?>
        </div>
    </div>
    <div class="row">
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
            <?= $form->field($model, 'mer9')->textInput(['maxlength' => true]) ?>
        </div>

    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'zc_part_number')->textInput(['maxlength' => true,'readonly'=>"readonly"]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'mfr_part_number')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'manufacturer')->dropDownList(empty($manufacturer)? [] : ArrayHelper::map($manufacturer, 'id', 'name')) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'assy_level')->dropDownList([''=>'','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'purchase_level')->dropDownList([''=>'','P'=>'P',]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'vehicle_standard')->dropDownList(['' => '', '0' => $model::VEHICLE_STANDARD[0],
                '1' => $model::VEHICLE_STANDARD[1], '2' => $model::VEHICLE_STANDARD[2]]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'pcb_footprint')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'schematic_part')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'datasheet')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'recommend_purchase')->dropDownList([''=>'','-1'=>$model::RECOMMEND_PURCHASE[-1],
                '0'=>$model::RECOMMEND_PURCHASE[0],'1'=>$model::RECOMMEND_PURCHASE[1],]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'lead_time')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'minimum_packing_quantity')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
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
    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$get_class2_by_class1id = Url::toRoute("/materials/get-class2-by-class1id");
$get_class3_by_class2id = Url::toRoute("/materials/get-class3-by-class2id");
$getSerialNum = Url::toRoute("/materials/get-serial-num");//根据料号一部分名字返回流水号
$getPartData = Url::toRoute("/materials/get-part-data");//根据输入的厂家料号，获得这个料的其它数据
$viewMaterial = Url::home(true).'materials/';//打开二三四供的查看窗口
//是不是更新界面
$isUpdate = 0;
if(!empty($class2))
    $isUpdate = 1;
$js = <<<JS

//PBC形成智车料号的数据结构//
var dataPCB = new Array();
dataPCB[4] = '板名'; 
dataPCB[7] = '项目号'; 
dataPCB[8] = '版号'; 
dataPCB[9] = '层数';

  /* 通过第一分类获得第二分类的信息 */
$("#materials-class1").change(function() {
    var class1Val = $(this).val();
    if (class1Val != "") {
        $.get('$get_class2_by_class1id',
            {class1Val:class1Val},
            function(json) {
                $("#materials-class2").empty();
                if (json.status == 1) {
                    var option = "<option value=\"\">" + "请选择二级分类 ..." + "</option>";
                    $("#materials-class2").append(option);
                    $.each(json.data, function() {
                        option = "<option value=\"" + this['id'] + "\">" + this['name'] + "</option>";
                        $("#materials-class2").append(option);
                    });
                }
            },
            "json"
        );
    }
});

  /**
   * 通过选中第二级分类，确定第三级（剩下所有的分类）; 
   */
$("#materials-class2").change(function() {
    var class2Val = $(this).val();
    if (class2Val != "") {
        /* 获得智车料号的数据 */
        $.get(
            "$get_class3_by_class2id",
            {class2Val:class2Val},//传的参数 
            function(json) {//返回数据后   
                if(json.status == 1){//如果是正常物料（除PCB和PCBA）返回成功
                    //先清空
                    $("#materials-mer1").empty();
                    $("#materials-mer2").empty();
                    $("#materials-part_type").empty();
                    $("#materials-mer4").val('');
                    $("#materials-mer5").empty();
                    $("#materials-mer6").empty();
                    $("#materials-mer7").val('');
                    $("#materials-mer8").val('');
                    $("#materials-mer9").val('');

                    $('#materials-zc_part_number').val('');
                    $('#materials-manufacturer').empty();

                    var i=0;
                    //把返回的数据显示到下拉框和文本框里
                    $.each(json.data, function(name,value) {
                        if (value==''){//说明是文件框
                            i++;
                            for(;i<4;i++){//把文本框前的下拉框隐藏
                                var element = "#materials-mer" + i;
                                $('.mer'+i).addClass('hide');
                            }
                            //给文本框提示字符属性
                            i=4;
                            var element = "#materials-mer" + i;
                            $(element).attr('placeholder',"请输入" + name + "...");
                            $('.mer'+i).removeClass('hide');
                            $('.mer4').find("label").text(name);
                        }else if(name.indexOf('分类')>=0){
                            i++;
                            for(;i<3;i++){//把分类前的下拉框隐藏
                                var element = "#materials-mer" + i;
                                $('.mer'+i).addClass('hide');
                            }
                            i=3;   
                            var element = "#materials-part_type";
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
                            $('#materials-manufacturer').append(option);  
                            $.each(value, function(key,val) {
                                option = "<option value=\"" + val.id + "\">" + val.name + "</option>";
                                $('#materials-manufacturer').append(option);  
                            });

                        }else{//说明是下拉框
                            i++;
                            var element = "#materials-mer" + i; 
                            var option = "<option value=\"\">" + "请选择" + name + "..." + "</option>";
                            $(element).append(option);
                            $('.mer'+i).removeClass('hide');
                            $('.mer'+i).find("label").text(name);
                            //向下拉框里添加 数据 
                            $.each(value, function(key,val) {
                                option = "<option value=\"" + val.remark + "\">" + val.name + "</option>";
                                $(element).append(option);
                            });
                        }
                    });
                    //把没有用到的隐藏起来！
                    i++;
                    for(;i<10;i++){
                       var element = "#materials-mer" + i;
                       $('.mer'+i).addClass('hide');
                    }
                }
                else if(json.status == 2)//pcb和pcba
                {
                    //先清空
                    $("#materials-mer1").empty();
                    $("#materials-mer2").empty();
                    $("#materials-part_type").empty();
                    $("#materials-mer4").val('');
                    $("#materials-mer5").empty();
                    $("#materials-mer6").empty();
                    $("#materials-mer7").val('');
                    $("#materials-mer8").val('');
                    $("#materials-mer9").val('');
                    $('#materials-zc_part_number').val('');
                    $('#materials-manufacturer').empty();
                    //显示和隐藏生成智车料号的框
                    for(var i=0;i<10;i++)
                    {
                        if(dataPCB.hasOwnProperty(i))
                        {
                            var element = "#materials-mer"+i;
                            $(element).attr('placeholder',"请输入" + dataPCB[i] + "...");
                            $('.mer'+i).removeClass('hide');
                            $('.mer'+i).find("label").text(dataPCB[i]);     
                        }
                        else
                            $('.mer'+i).addClass('hide');
                    }
                    //物料类型
                    $.each(json.data, function(name,value) {
                        if(name.indexOf('分类')>=0){
                            $('.mer3').removeClass('hide');
                            $('.mer3').find("label").text(name);
                            //向下拉框里添加 数据 
                            for(x in value) {
                                option = '<option value="'+value[x].id+'" remark="'+value[x].remark+'" selected>' + value[x].name + "</option>";
                                $("#materials-part_type").append(option);
                            }
                            $('#materials-description').val(value[x].name);
                        }
                    });
                }
                else
                    alert(json.message);
            },
            "json"
        );
    } 
});

/*自动添加描述前缀*/
$("#materials-part_type").change(function() {
  var class3 = $(this).find('option:selected').text();
  $("#materials-description").val(class3 + '_');
  needWhichDrop();
});
/*物料编码的下拉框事件*/
for(var i=1;i<10;i++){
  $('#materials-mer'+i).change(function() {
    needWhichDrop();
  });
}

  
  /*根据不同的组件需要哪几个控件去生成料号*/
  function needWhichDrop() {
    switch ($('#materials-class2').val())
    {
        case '5'://RES
            generateMaterialsCode([1,2,3,4,5],'RES',3);
            break;
        case '6'://CAP
            generateMaterialsCode([1,2,3,4,5,6],'CAP',3);
            break;
        case '7'://IND
            generateMaterialsCode([1,3,4],'IND',5);
            break;
        case 'BEAD':
            generateMaterialsCode([1,3,4],'EBA',5);
            break;
        case '45':
            generateMaterialsCode([1,3],'DIO',7);
            break;
        case 'Triode':
            generateMaterialsCode([1,3],'TRI',7);
            break;
        case 'MOS':
            generateMaterialsCode([1,3],'MOS',9);
            break;
        case 'Fuse':
            generateMaterialsCode([1,3],'FUS',7);
            break;
        case 'CONN':
            generateMaterialsCode([1,2,3,5],'CON',5);
            break;
        case 'Crystal/Oscillator':
            generateMaterialsCode([1,3],'CRY',9);
            break;
        case 'Spring':
            generateMaterialsCode([1,3],'SPR',10);
            break;
        case 'Buzzer':
            generateMaterialsCode([1,3],'BUZ',10);
            break;
        case 'Analog IC':
            generateMaterialsCode([1,3],'ICA',7);
            break;
        case 'Power IC':
            generateMaterialsCode([1,3],'ICP',7);
            break;
        case 'PHY':
            generateMaterialsCode([1,3],'ICH',7);
            break;
        case 'Memory':
            generateMaterialsCode([1,3],'ICM',7);
            break;
        case 'AP':
            generateMaterialsCode([1,3],'ICU',7);
            break;
        case 'Sensor':
            generateMaterialsCode([1,3],'ICS',7);
            break;
        case 'Module/IC':
        case 'ANT':
            generateMaterialsCode([1,3],'ICR',7);
            break;
        case 'Video':
            generateMaterialsCode([1,3],'ICV',6);
            break;
        case 'Battery':
            generateMaterialsCode([1,3],'ICB',8);
            break;
        case '443'://PCBA
            generateMaterialsCode([4,7,8,9],'',2);
            break;
        case '444'://PCB
            generateMaterialsCode([4,7,8,9],'',2);
            break;
    }
  }
  
  /*数字位不够，补零*/
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
      for(val in arr)
      {   
          var valueOption = $('#materials-mer'+arr[val]).val();
          if(arr[val] == 3)//当为3时，元素的id号是下面的
              valueOption = $('#materials-part_type').find('option:selected').attr('remark');
          if(val == 1)
              strCode = strCode + component;   
          if(valueOption)
              strCode += valueOption;
          else
          {     
              $('#materials-zc_part_number').val(" ");
              return false;
          }             
      }
      var SerialNum = generateSerialNum(strCode);
      if($('#materials-class2').val() == 443)
          strCode = 'P'+strCode;
      if($('#materials-class2').val() == 444)
          strCode = 'B'+strCode;
      if(SerialNum)//如果生成智车料号成功，把结果显示到input框上
          $('#materials-zc_part_number').val(strCode+padZore(SerialNum,num));   
  }
  
  /*查看输入的流水应该是多少号*/
  function generateSerialNum(code) {
    var sn='';
    $.ajax({  
      type : "get",  
      url : "$getSerialNum",  
      data : "code=" + code,  
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
    /* 获得智车料号的数据 */
    var zcMaterialNum = $('#materials-zc_part_number').val();
    var modelClass2 = $('#materials-class2').val();
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
                            var element = "#materials-mer" + i;
                            $('.mer'+i).addClass('hide');
                        }
                        //给文本框提示字符属性
                        i=4;
                        var element = "#materials-mer" + i;
                        $(element).attr('placeholder',"请输入" + name + "...");
                        $('.mer'+i).removeClass('hide');
                        $('.mer4').find("label").text(name);
                    }else if(name.indexOf('分类')>=0){
                        i++;
                        var element = "#materials-part_type";
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
                        var element = "#materials-mer" + i; 
                        $('.mer'+i).removeClass('hide');
                        $('.mer'+i).find("label").text(name);
                    }
                });
                //把没有用到的隐藏起来！
                i++;
                for(;i<10;i++){
                   var element = "#materials-mer" + i;
                   $('.mer'+i).addClass('hide');
                }
            }
            else if(json.status == 2)
            {
                //显示和隐藏生成智车料号的框
                for(var i=0;i<10;i++)
                {
                    if(dataPCB.hasOwnProperty(i))
                    {
                        var element = "#materials-mer"+i;
                        $(element).attr('placeholder',"请输入" + dataPCB[i] + "...");
                        $('.mer'+i).removeClass('hide');
                        $('.mer'+i).find("label").text(dataPCB[i]);     
                    }
                    else
                        $('.mer'+i).addClass('hide');
                }
                //物料类型
                $.each(json.data, function(name,value) {
                    if(name.indexOf('分类')>=0){
                        $('.mer3').removeClass('hide');
                        $('.mer3').find("label").text(name);
                        //向下拉框里添加 数据 
                        for(x in value) {
                            option = '<option value="'+value[x].id+'" remark="'+value[x].remark+'" selected>' + value[x].name + "</option>";
                            $("#materials-part_type").append(option);
                        }
                        $('#materials-description').val(value[x].name);
                    }
                });
            }
            else
                alert(json.message);
        },
        "json"
    ); 
}
JS;
$this->registerJs($js);

/**
 * 添完二三四供的料号去数据里看是否存在
 */
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

$js = <<<JS

    var viewPath = '';
    $('#materials-mfrpartno2').blur(modalDlg);
    $('#materials-mfrpartno3').blur(modalDlg);
    $('#materials-mfrpartno4').blur(modalDlg);
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
                        $.get('/materials/'+json.data+'?modal=1', {},
                           function (data) {
                               $('.modal-body').html(data);
                           } 
                        );
                        $('#view').click();
                    }
                }else{
                    alert("你输入的智车料号不存在");
                    mfrPartNoObj.val("");
                }
            }
        });
    }
    

JS;
$this->registerJs($js);
Modal::end();

?>