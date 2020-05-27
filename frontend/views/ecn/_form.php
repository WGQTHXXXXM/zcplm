<?php
/**
 * 旧的：BOM的变更是按何冰系统的样子做的
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;
use kartik\dialog\Dialog;
use yii\bootstrap\Modal;
use frontend\web\JQWidgetsAsset;


/* @var $this yii\web\View */
/* @var $model frontend\models\Ecn */
/* @var $form yii\widgets\ActiveForm */
/* @var $dataUser yii\widgets\ActiveForm */
/* @var $changeSet frontend\models\EcnChangeSet*/
$this->registerJsFile('/statics/js/read_excel/xlsx.full.min.js');
JQWidgetsAsset::register($this);

?>
<br><br>
<div class="ecn-form">

    <?php $form = ActiveForm::begin(['options'=>['enctype' => 'multipart/form-data']]); ?>
    <input id="taskRemark" type="hidden" name="taskRemark">
    <input id="taskCommit" type="hidden" name="taskCommit">
    <div class="row">
            <?= $form->field($model, 'projectName', ['template'=> '<div class="col-md-1">{label}</div><div class="col-md-2">{input}</div>{error}'])
                ->textInput(['maxlength' => true,'readonly'=>''])->label('项目名称：'); ?>
    </div>
    <br>
    <div class="row">
        <?= $form->field($model, 'projectProcess', ['template'=> '<div class="col-md-1">{label}</div><div class="col-md-2">{input}</div>{error}'])
            ->textInput(['maxlength' => true,'readonly'=>''])->label('项目阶段：'); ?>
    </div>
    <br>
    <div class="row">
        <?php
        echo $form->field($model, 'partNo', [
                'options'=>['class'=>''],
                'template'=> '<div class="col-md-1">{label}</div><div class="col-md-2">{input}</div>{error}'
        ])
            ->textInput(['maxlength' => true,'readonly'=>''])->label('机种智车料号：');
        echo $form->field($model, 'description', ['template'=> '<div class="col-md-4">{input}</div>{error}'])
            ->textInput(['maxlength' => true,'readonly'=>''])->label('');
        ?>
    </div>
    <br>
    <div class="row">
            <?= $form->field($model, 'background',['template'=> '<div class="col-md-1">{label}</div><div class="col-md-6">{input}</div>{error}'])->textarea(['rows' => 6,'readonly'=>''])->label('变更背景：') ?>
    </div>
    <br>
    <div class="row">
            <?= $form->field($model, 'content', ['template'=> '<div class="col-md-1">{label}</div><div class="col-md-6">{input}</div>{error}'])->textarea(['rows' => 6,'readonly'=>''])->label('变更内容：') ?>
    </div>

    <br><br><br>
    <h3>具体变更操作</h3>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'change_now')->dropDownList($model::CHANGE_NOW,['prompt' => '']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'affect_stock')->dropDownList($model::AFFECT_STOCK,['prompt' => '']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
        </div>
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

    <h3 style="display:inline;">上传附件</h3>&emsp;&emsp;
    <a href="/ecn/download-ecn">查看ECN模板</a>
    <label class="hide"><input type="radio" value="0" name="Ecn[is_attachment]" id="ecnType1">输入变更</label>&emsp;&emsp;&emsp;&emsp;
    <label class="hide"><input type="radio" checked="" value="1" name="Ecn[is_attachment]" id="ecnType2">上传变更</label>
    <br>
    <br>
    <br>
    <div id="inputEcn">
        <h3 style="display: inline">变更集合</h3>&emsp;&emsp;&emsp;&emsp;
        <button type="button" id="change-add" class="btn btn-default">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
    </div>

    <div class="hide">
        <?php
        //为了自动包涵一些头文件
        echo '<br>';
        echo \kartik\file\FileInput::widget([
            'name' => 'uploadFile[]',
            'pluginOptions' => [
                'showPreview' => false,
                'showCaption' => true,
                'showRemove' => true,
                'showUpload' => false
            ]
        ]);

        ?>
    </div>

    <div id="uploadEcn" class="hide">
        <div class="row">
            <div class="col-md-4">
                <input type="file" class="fileEcn" name="attachment" data-show-preview="false" data-show-upload="false">
            </div>
            <div class="col-md-2">
                <?= Html::button('预览新BOM',[
                    'class'=>'btn btn-success btn-check-ecn',
            //      'data-target' => '#bomview-modal',
                    'data-toggle' => 'modal'
                ])?>
            </div>
        </div>
        <br><br>
        <div id="show-bom"></div>
    </div>

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

    <br/><br/><br/><br/><br/>
    <div class="form-group">
        <?= Html::Button($model->isNewRecord ? Yii::t('common', '保存') : Yii::t('common', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'btn-submit']); ?>
    </div>
    <?php ActiveForm::end(); ?>
    <br/><br/><br/><br/><br/>
</div>
<?php
///
$getPartData = Url::toRoute("/modify-material/get-part-data");//根据输入的厂家料号，获得这个料的其它数据
$ecnSn = $model->serial_number;
//模态对话框
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
//弹框控件，其它默认，prompt框改下默认的配置
echo Dialog::widget([]);

//更新时用
$isUpdate = 0;
$id=0;
$jsonChangeSet='[]';
if(!$model->isNewRecord)
{
    $isUpdate = 1;
    $id = $_GET['id'];
    $jsonChangeSet = json_encode($changeSet);
}

//模态框
Modal::begin([
    'id' => 'bomview-modal',
    'size'=>'modal-lg',
    'header' => '<h3 class="modal-title">错误</h3>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]);
Modal::end();

$js = <<<JS

$('#bomview-modal').modal('toggle');    
$('#bomview-modal').modal('hide');
var objChangeSet = {};
$('.btn-check-ecn').on('click',function() {
    objChangeSet = {};
    isSubmit = true;
    $('#btn-submit').attr('disabled','disabled');
    
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
                $('#btn-submit').attr('disabled','disabled');
            } else if(jsonData.status == 1) {
                isSubmit = true;$('#btn-submit').removeAttr('disabled');
                
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

//上传规格书的控件
$('.fileEcn').fileinput({
        'data-show-preview':false,
        'showUpload':false,
        'showRemove':false,
        'maxFileSize':60000
    });

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//新建变更类型的类
var createEcnChange = function () {
    this.mySn=0;
    this.myIndex=0;
};

createEcnChange.prototype = {
    //点增加一个改变时的代码
    createChangeAdd:function() {
        return '<br class="br'+this.myIndex+'"/><br class="br'+this.myIndex+'"/>' +
         '<input id="btn-hide'+this.myIndex+'" style="width:56%;text-align:left ;" class="btn btn-primary" type="button"' +
          ' changeType="1" mySn="'+this.mySn+'" value="$ecnSn-'+this.mySn+'-REPLACE">' +
         '<input value="$ecnSn-'+this.mySn+'-REPLACE" name="Ecn[change_sn]['+this.myIndex+']" type="hidden">' +
         '&emsp;<button id="btn-minus'+this.myIndex+'" mySn="'+this.mySn+'" class="btn btn-default" type="button">' +
         '<i class="glyphicon glyphicon-minus"></i></button>'+
            '<div id="div'+this.myIndex+'"><br><div class="row">' +
                '<div class="col-md-3">' +
                    '<div class="form-group">' +
                        '<label class="control-label">变更类型</label>&ensp;&ensp;<i class=" glyphicon glyphicon-alert" title="'+
                            '替换        - 适用于结构物料的料号变更；&#10;'+
                            '调整数量 - 适用于电子料用量增减、点位增减的变更类型；&#10;'+
                            '增加物料 - 适用于结构料、电子料的新料增加的变更类型；&#10;'+
                            '删除物料 - 适用于结构料、电子料的旧料删除的变更类型；&#10;'+
                            '更改供方 - 适用于各类物料的供方增减的变更类型；"></i>'+            
                        '<select last="1" changeType="1" myIndex="'+this.myIndex+'" id="change-type'+this.myIndex+'" class="form-control" name="Ecn[change_type]['+this.myIndex+']">'+
                            '<option value="1">替换</option>'+
                            '<option value="2">调整数量</option>'+
                            '<option value="3">增加</option>'+
                            '<option value="4">删除</option>'+
                            '<option value="5">更改供方</option>'+
                        '</select>'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            
            '<div id="div-content'+this.myIndex+'"><div class="row">'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">目前零件（旧零件）</label>'+
                            '<input class="form-control" id="original_material'+this.myIndex+'" type="text">'+
                            '<input name="Ecn[original_material]['+this.myIndex+']" type="hidden">'+                            
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">物料描述</label>'+
                            '<input class="form-control" readonly="" id="original_material_desc'+this.myIndex+'" type="text">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
            '</div>'+
                        
            '<div class="row">'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">替代零件（新零件）</label>'+
                            '<input class="form-control" id="new_material'+this.myIndex+'" type="text">'+
                            '<input name="Ecn[new_material]['+this.myIndex+']" type="hidden">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">物料描述</label>'+
                            '<input class="form-control" readonly="" id="new_material_desc'+this.myIndex+'" type="text">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
            '</div></div>'+
 
             '<div class="row">'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">库存处理</label>'+
                        '<select class="form-control" name="Ecn[stock_processing]['+this.myIndex+']">'+
                            '<option value=""></option>'+
                            '<option value="0">报废</option>'+
                            '<option value="1">移作他用</option>'+
                            '<option value="2">用完为止</option>'+
                        '</select>'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">&nbsp;</label><br/>'+
                        '<input id="btn-user'+this.myIndex+'" myIndex="'+this.myIndex+
                        '" class="btn btn-default" type="button" value="寻找使用者">'+
                        '<div class="help-block"></div>'+
                    '</div>'+        
                '</div>'+
            '</div></div>';
    },
    //改为替换时的增加的代码
    createReplace:function(myIndex) {
        return '<div id="div-content'+myIndex+'"><div class="row">'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">目前零件（旧零件）</label>'+
                            '<input class="form-control" id="original_material'+myIndex+'" type="text">'+
                            '<input name="Ecn[original_material]['+myIndex+']" type="hidden">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">物料描述</label>'+
                            '<input class="form-control" readonly="" id="original_material_desc'+myIndex+'" type="text">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
            '</div>'+
                        
            '<div class="row">'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">替代零件（新零件）</label>'+
                            '<input class="form-control" id="new_material'+myIndex+'" type="text">'+
                            '<input name="Ecn[new_material]['+myIndex+']" type="hidden">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">物料描述</label>'+
                            '<input class="form-control" readonly="" id="new_material_desc'+myIndex+'" type="text">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
            '</div></div>';
    },
    //改为调整数量时的增加的代码
    createAdjqty:function(myIndex) {
        return '<div id="div-content'+myIndex+'">' +        
        '<div class="row">'+
            '<div class="col-md-3">'+
                '<div class="form-group">'+
                    '<label class="control-label">调整零件</label>'+
                        '<input class="form-control" id="original_material'+myIndex+'" type="text">'+
                        '<input name="Ecn[original_material]['+myIndex+']" type="hidden">'+
                    '<div class="help-block"></div>'+
                '</div>'+
            '</div>'+
            '<div class="col-md-3">'+
                '<div class="form-group">'+
                    '<label class="control-label">物料描述</label>'+
                        '<input class="form-control" readonly="" id="original_material_desc'+myIndex+'" type="text">'+
                    '<div class="help-block"></div>'+
                '</div>'+
            '</div>'+
        '</div>'+  
              
        '<div class="row">'+
            '<div class="col-md-1">'+
                '<div class="form-group">'+
                    '<label class="control-label">原本数量</label>'+
                        '<input class="form-control" name="Ecn[original_qty]['+myIndex+']" type="text">'+
                    '<div class="help-block"></div>'+
                '</div>'+
            '</div>'+
            '<div class="col-md-1">'+
                '<div class="form-group">'+
                    '<label class="control-label">调整类型</label>'+
                        '<select class="form-control" name="Ecn[adj_type]['+myIndex+']">'+
                            '<option value="1">增加</option>'+
                            '<option value="0">减少</option>'+
                        '</select>'+
                    '<div class="help-block"></div>'+
                '</div>'+
            '</div>'+
            '<div class="col-md-1">'+
                '<div class="form-group">'+
                    '<label class="control-label">调整数量</label>'+
                        '<input class="form-control" name="Ecn[qty]['+myIndex+']" type="text">'+
                    '<div class="help-block"></div>'+
                '</div>'+
            '</div>'+
            '<div class="col-md-5">'+
                '<div class="form-group">'+
                    '<label class="control-label">调整位置</label>'+
                        '<input class="form-control" name="Ecn[position]['+myIndex+']" type="text">'+
                    '<div class="help-block"></div>'+
                '</div>'+
            '</div>'+
        '</div>'+
        '</div>';  

    },
    //改为增加时的增加的代码
    createAdd:function(myIndex) {
        return '<div id="div-content'+myIndex+'"><div class="row">'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">新增零件</label>'+
                            '<input class="form-control" id="original_material'+myIndex+'" type="text">'+
                            '<input name="Ecn[original_material]['+myIndex+']" type="hidden">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">物料描述</label>'+
                            '<input class="form-control" readonly="" id="original_material_desc'+myIndex+'" type="text">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
            '</div>'+
                        
            '<div class="row">'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">参照零件</label>'+
                            '<input class="form-control" id="new_material'+myIndex+'" type="text">'+
                            '<input name="Ecn[new_material]['+myIndex+']" type="hidden">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">物料描述</label>'+
                            '<input class="form-control" readonly="" id="new_material_desc'+myIndex+'" type="text">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
            '</div>' +
                
            '<div class="row">'+
                '<div class="col-md-1">'+
                    '<div class="form-group">'+
                        '<label class="control-label">调整数量</label>'+
                            '<input class="form-control" name="Ecn[qty]['+myIndex+']" type="text">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-5">'+
                    '<div class="form-group">'+
                        '<label class="control-label">调整位置</label>'+
                            '<input class="form-control" name="Ecn[position]['+myIndex+']" type="text">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
            '</div>'+

            '</div>';
      
    },
    //改为删除时的增加的代码
    createRemove:function(myIndex) {
        return '<div id="div-content'+myIndex+'"><div class="row">'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">移除零件</label>'+
                            '<input class="form-control" id="original_material'+myIndex+'" type="text">'+
                            '<input name="Ecn[original_material]['+myIndex+']" type="hidden">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">物料描述</label>'+
                            '<input class="form-control" readonly="" id="original_material_desc'+myIndex+'" type="text">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
            '</div></div>';
    },
    //更改供方
    createMfr234:function(myIndex) {
        return '<div id="div-content'+myIndex+'"><div class="row">'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">变更零件</label>'+
                            '<input class="form-control" id="original_material'+myIndex+'" type="text">'+
                            '<input name="Ecn[original_material]['+myIndex+']" type="hidden">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<label class="control-label">物料描述</label>'+
                            '<input class="form-control" readonly="" id="original_material_desc'+myIndex+'" type="text">'+
                        '<div class="help-block"></div>'+
                    '</div>'+
                '</div>'+
            '</div></div>';
    },
    //增加寻找使用都代码段
    createUser:function(arrdata,myIndex) {
        var str = '<div id="div-user'+myIndex+'" class="row"><div class="col-md-6"><table class="table table-striped table-bordered table-hover">' +
            '<thead><tr><th><input id="userParent'+myIndex+'" type="checkbox"></th> <th>上阶智车料号</th> <th>描述</th>' +
            '</tr></thead><tbody>';
            for(var i in arrdata)
            {
                str += '<tr><td><input ';
                if(arrdata[i].minStatus==0||arrdata[i].aprStatus!=null)
                    str += 'disabled="" title="这个料还在审批中"';
                else
                    str += 'name="Ecn[userParent]['+myIndex+']['+arrdata[i].idBom+']"';
                str += ' type="checkbox"></td><td>'+arrdata[i].userZcPartNo+'</td><td>'+arrdata[i].userDesc+'</td></tr>';    
            }
            str += '</tbody></table></div></div>';
        return str;
    },
    //替换二三四供时的寻找使用者的代码段
    createUserForGroup:function(arrdata,myIndex) {//更新和新建都用一个代码，如果有值就添加上，没值就不加。
        var str = '<div id="div-user'+myIndex+'" class="row"><div class="col-md-10"><table class="table table-striped table-bordered table-hover">' +
            '<thead><tr><th><input checked="" id="userParent'+myIndex+'" type="checkbox"></th> <th>上阶智车料号</th> <th>描述</th>' +
            '<th><label>一供</label></th>' +
            '<th><label><input checked="" type="radio" value="2" name="rdo'+myIndex+'">二供</label></th>' +
            '<th><label><input type="radio" value="3" name="rdo'+myIndex+'">三供</label></th>' +
            '<th><label><input type="radio" value="4" name="rdo'+myIndex+'">四供</label></th>' +
            '<th><select id="select-mfr-type'+myIndex+'"><option value=""></option><option value="0">增加</option><option value="1">减少</option>'+
            '<option value="2">替换</option></select><input id="all-zc-partno'+myIndex+'" type="text"></th></tr></thead><tbody>';
            for(var i in arrdata)
            {
                str += '<tr><td><input ';
                if(arrdata[i].minStatus==0||arrdata[i].aprStatus!=null)
                    str += 'disabled="" title="这个料还在审批中"';
                else
                    str += 'name="Ecn[userParent]['+myIndex+']['+arrdata[i].idBom+']" checked=""';
                str += ' type="checkbox"></td><td>'+arrdata[i].userZcPartNo+'</td><td>'+arrdata[i].userDesc+'</td>' +
                    '<td><label>'+arrdata[i].zcPartNo1+'</label></td>' +
                    '<td><label><input checked="" '+(arrdata[i].mfr_no == 2?'checked=""':'')+' value="2" name="Ecn[mfr]['+myIndex+']['+arrdata[i].idBom+']" type="radio">'+arrdata[i].zcPartNo2+'</label></td>' +
                    '<td><label><input '+(arrdata[i].mfr_no == 3?'checked=""':'')+' value="3" name="Ecn[mfr]['+myIndex+']['+arrdata[i].idBom+']" type="radio">'+arrdata[i].zcPartNo3+'</label></td>' +
                    '<td><label><input '+(arrdata[i].mfr_no == 4?'checked=""':'')+' value="4" name="Ecn[mfr]['+myIndex+']['+arrdata[i].idBom+']" type="radio">'+arrdata[i].zcPartNo4+'</label></td>' +
                    '<td><select name="Ecn[mdf_mfr_type]['+myIndex+']['+arrdata[i].idBom+']">' +
                    '<option value=""></option><option '+(arrdata[i].mdf_type == 0?'selected=""':'')+' value="0">增加</option>' +
                    '<option '+(arrdata[i].mdf_type == 1?'selected=""':'')+' value="1">减少</option>'+
                    '<option '+(arrdata[i].mdf_type == 2?'selected=""':'')+' value="2">替换</option></select>' +
                     '<input value="'+(arrdata[i].mdf_part==undefined?'':arrdata[i].mdf_part)+'" id="mdf_part_no'+myIndex+'['+arrdata[i].idBom+']" type="text">' +
                     '<input value="'+(arrdata[i].mdf_part_id==undefined?'':arrdata[i].mdf_part_id)+'" name="Ecn[mdf_part_no]['+myIndex+']['+arrdata[i].idBom+']" type="hidden">' +
                      '</td></tr>';    
            }
            str += '</tbody></table></div></div>';
        return str;  
    },    
    //给新加的属性块赋上新的编码
    assignPropertyForNewDiv:function (myIndex,ichangeType,strchangeType) {
        $('#btn-hide'+myIndex).attr({changeType:ichangeType});
        var sn = $('#btn-hide'+myIndex).attr('mysn');
        $('#btn-hide'+myIndex).val('$ecnSn-'+sn+strchangeType);
        $('input[name="Ecn[change_sn]['+myIndex+']"]').val('$ecnSn-'+sn+strchangeType);
    },
    //删除时改需要改的编码和属性
    delChangeDiv:function(mySn) {
        for(var i=parseInt(mySn)+1;i<=this.mySn;i++)
        {
            var objInput = $('input[mySn="'+i+'"]');
            var changeType = objInput.attr('changeType');
            var strChangeType = '-REPLACE';
            if(changeType==2)
                strChangeType='-ADJQTY';
            else if(changeType==3)
                strChangeType='-ADD';
            else if(changeType==4)
                strChangeType='-REMOVE';
            else if(changeType==5)
                strChangeType='-ALTGROUP';
            var inputHide = objInput.val();
            $('input[value='+inputHide+']').val('$ecnSn-'+(i-1)+strChangeType);
            objInput.attr('mySn',i-1);
            var objBtn = $('button[mySn="'+i+'"]');
            objBtn.attr('mySn',i-1);
        }
    }
};

var obj = new createEcnChange();

//增加一个变更的按钮
$('#change-add').on('click',onBtnChangeAdd);

/////////////////////如果是更新页面要渲染界面///////////////////////////////////////
if($isUpdate == 1)
{
    var dataUpdate = $jsonChangeSet;

    for(x in dataUpdate)
    {
        onBtnChangeAdd();
        var myIndex = parseInt(x)+1;
        if(dataUpdate[x].type == 1)//替换
        {
            $('#change-type'+myIndex).val(1);
            onSltChangeType('1',myIndex);
            $('#div'+myIndex).append(obj.createUser(dataUpdate[x].partUser,myIndex));
        }
        else if(dataUpdate[x].type == 2)//调整数量
        {
            $('#change-type'+myIndex).val(2);
            onSltChangeType('2',myIndex);
            $('#div'+myIndex).append(obj.createUser(dataUpdate[x].partUser,myIndex));
            $('input[name="Ecn[original_qty]['+myIndex+']"]').val(dataUpdate[x].original_qty);
            $('select[name="Ecn[adj_type]['+myIndex+']"]').val(dataUpdate[x].adj_type);
            $('input[name="Ecn[qty]['+myIndex+']"]').val(dataUpdate[x].qty);
            $('input[name="Ecn[position]['+myIndex+']"]').val(dataUpdate[x].position);
        }
        else if(dataUpdate[x].type == 3)//增加
        {
            $('#change-type'+myIndex).val(3);
            onSltChangeType('3',myIndex);
            $('#div'+myIndex).append(obj.createUser(dataUpdate[x].partUser,myIndex));
            $('input[name="Ecn[qty]['+myIndex+']"]').val(dataUpdate[x].qty);
            $('input[name="Ecn[position]['+myIndex+']"]').val(dataUpdate[x].position);
        }
        else if(dataUpdate[x].type == 4)//删除
        {
            $('#change-type'+myIndex).val(4);
            onSltChangeType('4',myIndex);
            $('#div'+myIndex).append(obj.createUser(dataUpdate[x].partUser,myIndex));
        }
        else if(dataUpdate[x].type == 5)//更改供方
        {
            $('#change-type'+myIndex).val(5);
            onSltChangeType('5',myIndex);
            $('#div'+myIndex).append(obj.createUserForGroup(dataUpdate[x].partUser,myIndex));
            //radio
            $('input[name="rdo'+myIndex+'"]').on('click',{myIndex:myIndex},onRdoPartUser);
            //dropdownlist
            $('#select-mfr-type'+myIndex).on('change',{myIndex:myIndex},onDpdListPartUser);  
            //总text
            $('#all-zc-partno'+myIndex).on('change',{myIndex:myIndex},onTxtAllPartUser);
            //单text
            $('input[id^="mdf_part_no'+myIndex+'[').on('change',onTxtSigPartUser);
        }
        //更新时给控件分配上数据：原零件，新零件，库存处理
        assigDataUpdate(myIndex,dataUpdate);
        //加完使用者表格时全选或全不选checkbox点击事件
        $('#userParent'+myIndex).on('click',{myIndex:myIndex},function(event) {
            if($('#userParent'+event.data.myIndex).is(':checked'))
                $("input[name^='Ecn[userParent]["+event.data.myIndex+"]']").prop('checked',true);
            else
                $("input[name^='Ecn[userParent]["+event.data.myIndex+"]']").prop('checked',false);
        });
    }
    $('input[name^="Ecn[userParent]"]').prop('checked',true);
}

/**
* 更新时给控件分配上数据：原零件，新零件，库存处理
*/
function assigDataUpdate(myIndex,dataUpdate) 
{
    $('#original_material'+myIndex).val(dataUpdate[x].original_material);
    $('#original_material_desc'+myIndex).val(dataUpdate[x].original_material_desc);
    $('input[name="Ecn[original_material]['+myIndex+']"]').val(dataUpdate[x].original_material_id);
    $('#new_material'+myIndex).val(dataUpdate[x].new_material);
    $('#new_material_desc'+myIndex).val(dataUpdate[x].new_material_desc);
    $('input[name="Ecn[new_material]['+myIndex+']"]').val(dataUpdate[x].new_material_id);
    $('select[name="Ecn[stock_processing]['+myIndex+']"]').val(dataUpdate[x].stock_processing);
}

/**
* 增加一个变更集合的函数
*/
function onBtnChangeAdd() 
{
    obj.myIndex++;
    obj.mySn++;
    $('#change-add').after(obj.createChangeAdd()); 
    var index = obj.myIndex;
    //
    var objMaterial = $("#original_material_desc"+index);
    $("#original_material"+index).on('change',{objMaterial:objMaterial},modalDlg);
    var objMaterialNew = $("#new_material_desc"+index);
    $("#new_material"+index).on('change',{objMaterial:objMaterialNew},modalDlg);

    
    //显示隐藏单个更改内容
    $('#btn-hide'+index).on('click',function() {
        if($(('#div'+index)).hasClass('hide'))
            $('#div'+index).removeClass('hide');
        else
            $('#div'+index).addClass('hide');
    }); 
    //寻找使用者按钮
    $('#btn-user'+index).on('click',onBtnPartUser);
    //删除自己的内容
    $('#btn-minus'+index).on('click',function() {
        if(confirm('您确定要删除这条变更吗？')){
            $('#div'+index).remove();
            $('#btn-minus'+index).remove();
            $('.br'+index).remove();
            var inputHide = $('#btn-hide'+index).val();
            $('input[value='+inputHide+']').remove();
            var mySn = $(this).attr('mySn');
            obj.delChangeDiv(mySn);
            obj.mySn--;
        }
    });
    
    /**
    * 变更类型改变的按钮
    */
    $('#change-type'+index).on('change',onSltChangeType);
    
}

/**
* 更改二三四供时的radio的点击事件
*/
function onRdoPartUser(event) 
{
    var myIndex = event.data.myIndex;
    var radio = $(this).val();

    switch (radio)
    {
        case "1":
            $('input[name^="Ecn[mfr]['+myIndex+']"][value="1"]').prop('checked',true);
            break;
        case "2":
            $('input[name^="Ecn[mfr]['+myIndex+']"][value="2"]').prop('checked',true);
            break;
        case "3":
            $('input[name^="Ecn[mfr]['+myIndex+']"][value="3"]').prop('checked',true);
            break;
        case "4":
            $('input[name^="Ecn[mfr]['+myIndex+']"][value="4"]').prop('checked',true);
            break;
    }    
}

/**
* 更改二三四供时的dorpdownlist的控件事件
*/
function onDpdListPartUser(event) 
{
    var myIndex = event.data.myIndex;
    var objSelect = $('select[name^="Ecn[mdf_mfr_type]['+myIndex+']"]');
    switch ($(this).val())
    {
        case '':
            objSelect.val('');
            break;
        case '0':
            objSelect.val(0);
            break;
        case '1':
            objSelect.val(1);
            break;
        case '2':
            objSelect.val(2);
            break;
    }
}

/**
* 更改二三四供时的总text的控件事件
*/
function onTxtAllPartUser(event) 
{
    var myIndex = event.data.myIndex;
    var textVal = $(this).val();
    var mfrPartNoObj = $(this);
    $.ajax({
        type:"get",
        url:"$getPartData",
        data:"zcPartNo=" + textVal,
        async:false,
        dataType:'json',
        success:function(json) {
            if(json.status == 1){
                $('input[id^="mdf_part_no'+myIndex+'[').val(textVal);
                $('input[ name^="Ecn[mdf_part_no]['+myIndex+']["]').val(json.data.id);
                if(confirm("你是否要打开该物料的信息")){
                    $.get('/materials/'+json.data.id+'?modal=1', {},
                       function (data) {
                           $('.modal-body').html(data);
                       } 
                    );
                    $('#view').click();
                }
            }else{
                krajeeDialog.alert("你输入的智车料号不存在");
                mfrPartNoObj.val("");
                $('input[id^="mdf_part_no'+myIndex+'[').val('');
                $('input[name^="Ecn[mdf_part_no]["]').val('');
            }
        }
    });

}

/**
* 更改二三四供时的单个text的控件事件
*/
function onTxtSigPartUser() 
{
    var textVal = $(this).val();
    var mfrPartNoObj = $(this);
    $.ajax({
        type:"get",
        url:"$getPartData",
        data:"zcPartNo=" + textVal,
        async:false,
        dataType:'json',
        success:function(json) {
            if(json.status == 1){
                mfrPartNoObj.siblings('input').val(json.data.id);
                if(confirm("你是否要打开该物料的信息")){
                    $.get('/materials/'+json.data.id+'?modal=1', {},
                       function (data) {
                           $('.modal-body').html(data);
                       } 
                    );
                    $('#view').click();
                }
            }else{
                krajeeDialog.alert("你输入的智车料号不存在");
                mfrPartNoObj.val("");
                mfrPartNoObj.siblings('input').val('');
            }
        }
    });  
}

/**
* 使用者按钮
*/
function onBtnPartUser() 
{
    var myIndex = $(this).attr('myIndex');
    $('#div-user'+myIndex).remove();
    var id_child = $('#original_material'+myIndex).siblings('input').val();
    if($('#original_material'+myIndex).val() == '')//输入为空时，返回
        return;
    var changeType = $('#change-type'+myIndex).val();
    //如果是增加类型，要选参照零件的上级
    if(changeType == 3)
        id_child = $('input[name="Ecn[new_material]['+myIndex+']"]').val();
    
    $.ajax({
    type:"get",
    url:'/ecn/get-upper-lvl',
    data:"id_child="+id_child+"&changeType="+changeType+'&id='+'$id',
    async:false,
    dataType:'json',
    success:function(json) {
            //如果是修改二三四供,使用者的表格不一样，还要添加一些事件
            if(changeType==5)
            {
                $('#div'+myIndex).append(obj.createUserForGroup(json.data,myIndex));
                //radio
                $('input[name="rdo'+myIndex+'"]').on('click',{myIndex:myIndex},onRdoPartUser);
                //dropdownlist
                $('#select-mfr-type'+myIndex).on('change',{myIndex:myIndex},onDpdListPartUser);
                //总text
                $('#all-zc-partno'+myIndex).on('change',{myIndex:myIndex},onTxtAllPartUser);
                //单text
                $('input[id^="mdf_part_no'+myIndex+'[').on('change',onTxtSigPartUser);
            }
            else
                $('#div'+myIndex).append(obj.createUser(json.data,myIndex));
            //全选或全不选checkbox
            $('#userParent'+myIndex).on('click',function() {
                if($('#userParent'+myIndex).is(':checked'))
                    $("input[name^='Ecn[userParent]["+myIndex+"]']").prop('checked',true);
                else
                    $("input[name^='Ecn[userParent]["+myIndex+"]']").prop('checked',false);
            });
        }
    });
}

/*
 *变更类型选择按钮
 */
function onSltChangeType(changeType=-1,myIndex=-1) {
    //改后把当前块干掉，把新块加进去。然后赋上属性值
    if(myIndex == -1)
    {
        var myIndex = $(this).attr('myIndex');
        changeType = $(this).val();
    }
    $('#div-content'+myIndex).remove();
    switch (changeType)
    {
        case '1'://替换
            {
                obj.assignPropertyForNewDiv(myIndex,1,'-REPLACE');
                $($(('#div'+myIndex)+' > div')[0]).after(obj.createReplace(myIndex));
                var objMaterial = $("#original_material_desc"+myIndex);
                $("#original_material"+myIndex).on('change',{objMaterial:objMaterial},modalDlg);
                var objMaterialNew = $("#new_material_desc"+myIndex);
                $("#new_material"+myIndex).on('change',{objMaterial:objMaterialNew},modalDlg);
                break;
            }               
        case '2'://调整数量
            {
                obj.assignPropertyForNewDiv(myIndex,2,'-ADJQTY');
                $($(('#div'+myIndex)+' > div')[0]).after(obj.createAdjqty(myIndex));
                var objMaterial = $("#original_material_desc"+myIndex);
                $("#original_material"+myIndex).on('change',{objMaterial:objMaterial},modalDlg);
                $("input[name='Ecn[position]["+myIndex+"]']").on('keyup',checkKeyDown);
                break;
            }
        case '3'://增加
            {
                obj.assignPropertyForNewDiv(myIndex,3,'-ADD');
                $($(('#div'+myIndex)+' > div')[0]).after(obj.createAdd(myIndex));
                var objMaterial = $("#original_material_desc"+myIndex);
                $("#original_material"+myIndex).on('change',{objMaterial:objMaterial},modalDlg);
                var objMaterialNew = $("#new_material_desc"+myIndex);
                $("#new_material"+myIndex).on('change',{objMaterial:objMaterialNew},modalDlg);
                $("input[name='Ecn[position]["+myIndex+"]']").on('keyup',checkKeyDown);
                break;
            }
        case '4'://移除
            {
                obj.assignPropertyForNewDiv(myIndex,4,'-REMOVE');
                $($(('#div'+myIndex)+' > div')[0]).after(obj.createRemove(myIndex));
                var objMaterial = $("#original_material_desc"+myIndex);
                $("#original_material"+myIndex).on('change',{objMaterial:objMaterial},modalDlg);
                break;
            }
        case '5'://234供的更换
            {
                obj.assignPropertyForNewDiv(myIndex,5,'-ALTGROUP');
                $($(('#div'+myIndex)+' > div')[0]).after(obj.createMfr234(myIndex));
                var objMaterial = $("#original_material_desc"+myIndex);
                $("#original_material"+myIndex).on('change',{objMaterial:objMaterial},modalDlg);
                break;
            }
    }
}

//检查二供存在，弹模态对话框
function modalDlg(event)
{
    var zcPartNoVal = $(this).val();
    var mfrPartNoObj = $(this);
    if(zcPartNoVal.replace(/(^\s*)/g, "") == "")//去空格
    {
        mfrPartNoObj.val("");
        event.data.objMaterial.val('');
        return;
    }
    $.ajax({
        type:"get",
        url:"$getPartData",
        data:"zcPartNo=" + zcPartNoVal,
        async:false,
        dataType:'json',
        success:function(json) {
            if(json.status == 1){
                event.data.objMaterial.val(json.data.desc);
                mfrPartNoObj.data('key',json.data.id);
                mfrPartNoObj.siblings().val(json.data.id);
                if(confirm("你是否要打开该物料的信息")){
                    $.get('/materials/'+json.data.id+'?modal=1', {},
                       function (data) {
                           $('.modal-body').html(data);
                       } 
                    );
                    $('#view').click();
                }
            }else{
                krajeeDialog.alert("你输入的智车料号不存在");
                mfrPartNoObj.val("");
                event.data.objMaterial.val('');
            }
        }
    });
}

//位置框输入时是否允许输入，只可以输入数字，字母，逗号
function checkKeyDown(event) 
{
    var c = event.charCode || event.keyCode; //FF、Chrome IE下获取键盘码
    var inputVal = $(event.target).val();
    //正则匹配，不在这个集合中的要替换成''
    $(event.target).val(inputVal.replace(/[^\w,，]/ig,''));
}

JS;
$this->registerJs($js);
?>

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
                    'cssClass'=>'hide'//没有稍后提交
                ],
                [
                    'label' => '马上提交任务',
                    'icon' => Dialog::ICON_OK,
                ],
            ]
        ],

    ]
]);
$js = <<<JS

//控制提交按钮是否失效
$('#ecnType1').on('click',function() {
    $('#btn-submit').removeAttr('disabled');
    $('#uploadEcn').addClass('hide');
    $('#inputEcn').removeClass('hide');    

});

var isSubmit = false;

$('#ecnType2').on('click',function() {
    $('#inputEcn').addClass('hide');
    $('#uploadEcn').removeClass('hide');

    if(isSubmit)
        $('#btn-submit').removeAttr('disabled');
    else
        $('#btn-submit').attr('disabled','disabled');
});

$('.fileEcn').on('change',function() {
    isSubmit = false;
    $('#btn-submit').attr('disabled','disabled');

});

$('#ecnType2').click();

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
        
        //检查审批人是否相同
        var checkApprover1=$('#ecn-approver1').val();
        var checkApprover2=$('#ecn-approver2').val();
        var checkApprover4=$('#ecn-approver4dcc').val();
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
JS;
$this->registerJs($js);


?>


