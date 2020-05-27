<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\file\FileInput;
use yii\helpers\Url;
use kartik\dialog\Dialog;
use frontend\models\Ecn;
use frontend\web\JQWidgetsAsset;

$this->registerJsFile('/statics/js/read_excel/xlsx.full.min.js');
JQWidgetsAsset::register($this);


/* @var $this yii\web\View */
/* @var $model frontend\models\Ecr */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecr-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <input id="taskRemark" type="hidden" name="taskRemark">
    <input id="taskCommit" type="hidden" name="taskCommit">
    <h3>ECR内容</h3>
    <div class="row">
        <?php

        $pjtName = [''=>'']+$pjtName;

        echo $form->field($model, 'project_id',['template'=>'<div class="col-md-1">{label}</div><div class="col-md-2">{input}</div>{error}',])
            ->dropDownList($pjtName,[
                    'maxlength' => true,
                    'placeholder'=>'选择项目',
                    'onchange'=>'
                        $.post("/ecr/get-project-process",{id:$(this).val()},function(json){
                            $("#ecr-project_process_id").html(json);
                        });
                    ',]
            )->label('项目名称:');
        ?>
    </div>
    <br>
    <div class="row">
        <?php
        echo $form->field($model, 'project_process_id',
            [
                'template'=>'<div class="col-md-1">{label}</div><div class="col-md-2">{input}</div>{error}',
                //'options' => ['class' => '']
            ])
            ->dropDownList(empty($projectProcess)? [] : \yii\helpers\ArrayHelper::map($projectProcess, 'id', 'name'),['maxlength' => true])
            ->label('项目阶段:');
        ?>
    </div>
    <br>
    <div class="row">
        <?php
        //如果是更新
        $value = '';
        if(!$model->isNewRecord)
        {
            $dataTemp = json_decode($dataMtrDescription,true);
            $value = $dataTemp[$model->bom_id];
        }

        echo $form->field($model, 'bom_id',
            [
                'template'=>'<div class="col-md-1">{label}</div><div class="col-md-2">{input}</div>{error}',
                'options' => ['class' => ''],
            ])->widget(Select2::className(),[
                    'data' => $dataMtrPartNo,
                    'options' => ['placeholder' => '请选择料号 ...'],
                    'pluginEvents'=> [
                        "change" => "function() { 
                            var desVal = $(this).val();
                            $('#mtr-description').val(dataMtrDescription[desVal]);
                        }",
                    ],

            ])->label('机种智车料号:');
        echo '<div class="col-md-4">
                <input id="mtr-description" value="'.$value.'" class="form-control" readonly="" maxlength="255"  type="text">
            </div>';
        ?>
    </div>
    <br>
    <div class="row">
        <?php
        echo $form->field($model, 'reason',
            [
                'template'=>'<div class="col-md-1">{label}</div><div class="col-md-6">{input}</div>{error}',
                //'options' => ['class' => '']
            ])
            ->textarea(['rows' => 4,'placeholder'=>'用于描述变更的起因、设计变更的物料料号等。'])
            ->label('变更背景:');
        ?>
    </div>
    <br>
    <div class="row">
        <?php
        echo $form->field($model, 'detail',
            [
                'template'=>'<div class="col-md-1">{label}</div><div class="col-md-6">{input}</div>{error}',
                //'options' => ['class' => '']
            ])
            ->textarea(['rows' => 6,'placeholder'=>'可用分条描述成对比描述方式'])->label('变更内容:');
        ?>
    </div>
    <br>
    <div class="row">
        <?php
        //ECR影响范围
        $UpdateEffectRange = $model->effect_range;
        if(empty($model->effect_range)){//说明不是更新
            $UpdateEffectRange=[];
        }else
            $UpdateEffectRange = explode(',',$UpdateEffectRange);

        $content = '<div class="col-md-1"><label class="control-label" for="ecr-project_process_id">影响范围:</label></div><div class="col-md-6">';
        foreach ($arrEffectRange as $key=>$value){
            $content.= '<div class="row"><div class="col-md-2"><label>'.$key.'：</label></div><div class="col-md-10">';
            foreach ($value as $val){
                $content .= '<label style="font-family: Arial;color: grey"><input type="checkbox" name="Ecr[effect_range][]" value="';
                if(false !== array_search($val,$UpdateEffectRange))//更新时把复选框变成已选
                    $content .= $val.'" checked="">'.$val.'</label>&emsp;&emsp;';
                else
                    $content .= $val.'">'.$val.'</label>&emsp;&emsp;';
            }
            $content .= '</div></div><br>';
        }
        $content .= '</div>';

        echo $form->field($model, 'effect_range',
            [
                'template'=>'<div class="col-md-1">{label}</div><div class="col-md-6">{input}</div>{error}',
            ])->render($content);
        ?>
    </div>
    <br><br><br>
    <div class="row">
        <div class="col-md-1"><label class="control-label">附件:</label></div>
        <div class="col-md-4">
            <?php
            echo FileInput::widget([
                    'model' => $model,
                    'attribute' => 'uploadFile[]',
                    'options' => ['multiple' => true],
                    'pluginOptions' => [

                        // 异步上传的接口地址设置
                        'uploadUrl' => Url::toRoute(['/attachments/async-upload']),
                        // 异步上传需要携带的其他参数，比如material_id等
//                        'uploadExtraData' => [
//                            //'material_id' => $id,
//                        ],
                        //'uploadAsync' => true,

                        // 需要预览的文件格式
                        'allowedFileExtensions' => [
                            'jpg', 'bmp', 'png', 'txt', 'pdf', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
                        'maxFileSize'=>10486,
                        // 预览的文件
                        'initialPreview' => empty($preview)?[]:$preview,
                        'initialPreviewConfig' => empty($previewCfg)?[]:$previewCfg,
                        'initialPreviewAsData'=>true,
                        //上传最大
                        'maxFileCount' => 5,
                        'dropZoneTitle'=>'',
                        'dropZoneEnabled'=>false,
                        'dropZoneClickTitle'=>'点击可选择上传多个文件',
                        //编辑框边不显示的三个按钮
//                        'showCaption'=> false,
                        'showRemove' => false,
                        'showUpload' => false,
//                        'showBrowse' => false,
                        //上传新的不把以前的冲掉
                        'overwriteInitial'=>false,

                        //每个文件的选项
                        'fileActionSettings' => ['showUpload' => false,'showZoom'=>false],
                        // 展示图片区域是否可点击选择多文件
                        'browseOnZoneClick' => true,
                    ],
                ]);
            ?>
        </div>
        <div class="col-md-2" style="font-size: 18px">（用于补充说明）</div>
    </div>
    <br><br>
    <div class="row">
        <div class="col-md-1" style="float: left"><span id="create-ecn" class="btn btn-warning">取消ECN</span></div>
        <div class="col-md-7 ecn-part" style="margin-left: -45px;"><hr style="border-top:2px solid #000"></div>
    </div>
    <br>
<!---->
    <div class="ecn-part">
        <h3>ECN内容</h3>
        <div class="row">
                <?= $form->field($model, 'change_now',['template'=>'<div class="col-md-1">{label}</div><div class="col-md-3">{input}</div>{error}'])
                    ->dropDownList(Ecn::CHANGE_NOW,['prompt' => ''])->label('是否立即变更:') ?>
        </div>
        <br>
        <div class="row">
                <?= $form->field($model, 'affect_stock',['template'=>'<div class="col-md-1" style="padding-right: 8px;">{label}</div><div class="col-md-3">{input}</div>{error}'])
                    ->dropDownList(Ecn::AFFECT_STOCK,['prompt' => ''])->label('是否影响库存产品:') ?>
        </div>
        <br>
        <div class="row">
                <?= $form->field($model,'remark',['template'=>'<div class="col-md-1">{label}</div><div class="col-md-6">{input}</div>{error}'])
                    ->textarea(['rows' => 6])->label('备注:') ?>
            <div class="col-md-5">
                <label class="control-label" for="ecn-remark">&emsp;</label><br>
                A返工 - 旧物料返工后按新料号入库，且新料号在新BOM中使用；<br>
                B旧料用完用新 - 旧物料以《代料单》形式入库，且新料号在新BOM中使用；<br>
                C立即导入 - 无旧料库存，且新料号在新BOM中使用；<br>
                D报废 - 旧物料以旧料号报废，且新料号在新BOM中使用；<br><br>
                <font color="red">例如：AS00S1015002，有库存XXpcs，处理方式A返工。</font>
            </div>
        </div>

        <br><br><br>

        <a href="/ecn/download-ecn">查看ECN模板</a>
        <br>

        <div class="row">
            <div class="col-md-1"><label class="control-label">ECN附件:</label></div>
            <div class="col-md-4">
                <input type="file" class="fileEcn" name="attachment" data-show-preview="false" data-show-upload="false">
            </div>
            <div class="col-md-2">
                <?= Html::button('检查附件/预览新BOM',[
                    'class'=>'btn btn-success btn-check-ecn',
                    //      'data-target' => '#bomview-modal',
                    'data-toggle' => 'modal'
                ])?>
            </div>
        </div>
        <br><br>
        <div id="show-bom"></div>
    </div>
<!---->
    <br>
    <hr style="border-top:2px solid #000">
    <h2>审批人</h2>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'approver1')->widget(Select2::className(),[
                'data' => $dataUser[1],
                'options' => ['placeholder' => '请选择审批人 ...'],
                'pluginOptions' => ['tokenSeparators' => [',', ' ']],
            ]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'approver2')->widget(Select2::className(),[
                'data' => $dataUser[1],
                'options' => ['placeholder' => '请选择审批人 ...'],
                'pluginOptions' => ['tokenSeparators' => [',', ' ']],
            ]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'approver4dcc')->widget(Select2::className(),[
                'data' => $dataUser[3],
                'options' => ['placeholder' => '请选择审批人 ...'],
                'pluginOptions' => ['tokenSeparators' => [',', ' ']],
            ]) ?>
        </div>
    </div>

    <br><br><br>
    <div class="form-group">
        <?= Html::Button($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'btn-submit']); ?>
    </div>




    <?php ActiveForm::end(); ?>

</div>

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
                    'icon' => Dialog::ICON_CANCEL,
                    'cssClass'=>'hide'
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


//模态框
\yii\bootstrap\Modal::begin([
    'id' => 'bomview-modal',
    'size'=>'modal-lg',
    'header' => '<h3 class="modal-title">错误</h3>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]);
\yii\bootstrap\Modal::end();


$js = <<<JS

var dataMtrDescription={$dataMtrDescription};
var btnSubmit = $('#btn-submit');

/**
* 提交表单的按钮
*/
btnSubmit.on('click',function() {
    krajeeDialog.prompt({label:'备注', placeholder:'任务的备注...'}, function (result) {
        var remark = $("input[name='krajee-dialog-prompt']").val();
        $('#taskRemark').val(remark);
                
        if (result!=null) 
            $('#taskCommit').val('1');
        else 
            $('#taskCommit').val('0');
        
        //检查审批人是否相同
        var checkApprover1=$('#ecr-approver1').val();
        var checkApprover2=$('#ecr-approver2').val();
        var checkApprover4=$('#ecr-approver4dcc').val();
        if(checkApprover1 == checkApprover2){
            alert('审批人不能相同');return;
        }
        if(checkApprover1 == checkApprover2||checkApprover2 == checkApprover4){
            alert('审批人不能相同');return;
        }
        if(checkApprover1 == checkApprover2||checkApprover2 == checkApprover4||checkApprover1 == checkApprover4){
            alert('审批人不能相同');return;
        }

        
        $('#w0').submit();
    });
});

//初始化复选框控件
$('input[name="Ecr[effect_range][]"]').iCheck({ 
    checkboxClass: 'icheckbox_square-blue'
});
////////////////////////////////////////////////////////////////////////////////////////////////////////
//创建ECN按钮
$("#create-ecn").on('click',function() {
    if($(this).hasClass('btn-warning')){
        $(this).removeClass('btn-warning');
        $(".ecn-part").addClass('hide');
        $(this).addClass('btn-success');
        $(this).html('增加ECN');
        btnSubmit.removeAttr('disabled');
    } else {
        $(this).removeClass('btn-success');
        $(".ecn-part").removeClass('hide');
        $(this).addClass('btn-warning');  
        $(this).html('取消ECN');
        btnSubmit.attr('disabled','disabled');
    }
});

//上传规格书的控件
$('.fileEcn').fileinput({
        'data-show-preview':false,
        'showUpload':false,
        'showRemove':false,
        'maxFileSize':60000
});

btnSubmit.attr('disabled','disabled');
var objChangeSet = {};
$('.btn-check-ecn').on('click',function() {
    objChangeSet = {};
    isSubmit = true;
    btnSubmit.attr('disabled','disabled');
    
    var reader = new FileReader();
    //读取回调
    reader.onload = function(e) {
        var data = e.target.result;
        var wb = XLSX.read(data, {type: 'binary'});
        var objOriginal =XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]]);
        var parentNo,zcNo,qtyOld,qtyNew,subRef,addRef,zcNo2;//需要的字段
        //bom_parent数组
        //var objChangeSet = {};
        for(var i=4,len=objOriginal.length;i<len;i++){
            parentNo = objOriginal[i].__EMPTY;
            zcNo = objOriginal[i].__EMPTY_3;
            qtyOld = objOriginal[i].hasOwnProperty('__EMPTY_8')?objOriginal[i].__EMPTY_8:'';
            qtyNew = objOriginal[i].hasOwnProperty('__EMPTY_9')?objOriginal[i].__EMPTY_9:'';
            subRef = objOriginal[i].hasOwnProperty('__EMPTY_12')?objOriginal[i].__EMPTY_12:'';
            addRef = objOriginal[i].hasOwnProperty('__EMPTY_13')?objOriginal[i].__EMPTY_13:'';
            zcNo2 = objOriginal[i].hasOwnProperty('__EMPTY_16')?objOriginal[i].__EMPTY_16:'';
            if(parentNo==undefined||zcNo ==undefined)//如果遇到空，说明到底了就跳出
                break;
            if(!objChangeSet.hasOwnProperty(parentNo))
                objChangeSet[parentNo]=[];
            objChangeSet[parentNo].push({zcNo:zcNo,qtyOld:qtyOld,qtyNew:qtyNew,subRef:subRef,addRef:addRef,zcNo2:zcNo2});
        }
      
        //根据boms_parent查看boms_child
        $.post('/ecn/get-bom-parts',{objChangeSet:objChangeSet},function(jsonData) {
            if(jsonData.status == 0){//没有的parentNo
                $('#bomview-modal').modal('show');
                $('.modal-body').html(showError(jsonData.data));
                $('#bomview-modal').find('.modal-title').text('错误');
                isSubmit = false;
                btnSubmit.attr('disabled','disabled');
            } else if(jsonData.status == 1) {
                isSubmit = true;btnSubmit.removeAttr('disabled');
                
                var showBom = $("#show-bom");
                var source =
                    {
                        dataType: "json",
                        dataFields: [
                            { name: 'id', type: 'number' },
                            { name: 'child_id', type: 'number' },
                            { name: 'zc_part_number', type: 'string' },
                            { name: 'qty', type: 'number' },
                            { name: 'ref_no', type: 'string' },
                            { name: 'zc_part_number2', type: 'string' },
                            { name: 'zc_part_number3', type: 'string' },
                            { name: 'zc_part_number4', type: 'string' },
                            { name: 'children', type: 'array' }
                        ],
                        hierarchy:
                        {
                            root: 'children'
                        },
                        id: 'child_id',
                        localData: jsonData.data
                    };
                var dataAdapter = new $.jqx.dataAdapter(source);
                ////////自适应屏幕高度////////////////
                /////////////
                // create Tree Grid
                showBom.jqxTreeGrid(
                {
                //    width: 850,
                    width: 'auto',
                    height: 'auto',
                    source: dataAdapter,
                    sortable: true,
                    columnsResize: true,
                    altRows: true,
                    icons: true,
                    ready: function()
                    {
                        $('#show-bom').jqxTreeGrid({enableBrowserSelection: true });
                    },
                    columns: [
                      { text: '智车料号', dataField: 'zc_part_number', width: 260,
                        cellsRenderer: function (rowKey, dataField, value, data) {
                            if(data.changed == 1)
                                return '<font size="3" color="red">'+value+'</font>';
                            return value;
                        }
                      },
                      { text: '数量', dataField: 'qty', width: 50 },
                      { text: '位置', dataField: 'ref_no', width: 150 },
                      { text: '二供', dataField: 'zc_part_number2_id', width: 150 },
                      { text: '三供', dataField: 'zc_part_number3_id', width: 150 },
                      { text: '四供', dataField: 'zc_part_number4_id', width: 150 }
                    ]
                });
      
                showBom.on('rowDoubleClick',function(event) {
                    var args = event.args;
                    var row = args.row;
                $('#bomview-modal').modal('show');
                $('#bomview-modal').find('.modal-title').text('查看位置');
                $('.modal-body').html('<div style="word-wrap:break-word;">'+row.ref_no+"</div>");
                })
            }
        },'json');
    };

    //将文件读取为二进制字符串
    var fileEcn = $('.fileEcn')[0].files[0];
    if(fileEcn){//如果有上传的附件
        reader.readAsBinaryString(fileEcn); 
    }
});

/*
 返回模态框的错误的信息的HTML代码   
 */
function showError(data)
{
    
    var strHtml = '';
    for(var i in data){
        strHtml += '<h3>'+i+'</h3>';
        for(var j in data[i]){
            strHtml += '<h5>'+data[i][j]+'</h5>';
        }
        strHtml += '<br><br>'
    }   
    return strHtml;
}


JS;

$this->registerJsFile('/iCheck/icheck.min.js',['depends'=>'yii\web\JqueryAsset']);
$this->registerCssFile('/iCheck/skins/all.css');
$this->registerJs($js);


?>
