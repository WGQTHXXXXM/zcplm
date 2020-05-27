<?php
/**
 * 新的：按现在公司的ECN的excel表格方式填写BOM变更
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\web\JQWidgetsAsset;
use kartik\dialog\Dialog;


JQWidgetsAsset::register($this);

/* @var $this yii\web\View */
/* @var $model frontend\models\Ecn */
/* @var $form yii\widgets\ActiveForm */
/* @var $dataUser yii\widgets\ActiveForm */
/* @var $changeSet frontend\models\EcnChangeSet*/
?>
<br><br>
<div class="ecn-form">

    <?php $form = ActiveForm::begin(); ?>
    <input id="taskRemark" type="hidden" name="taskRemark">
    <input id="taskCommit" type="hidden" name="taskCommit">
    <div class="row">
        <div class="col-md-7">
            <?= $form->field($model, 'reason')->textarea(['rows' => 6,'readonly'=>'']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <?= $form->field($model, 'detail')->textarea(['rows' => 6,'readonly'=>'']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <?= $form->field($model, 'module')->textInput(['maxlength' => true,'readonly'=>'']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <?php echo  $form->field($model, 'project_process')->textInput(['maxlength' => true,'readonly'=>''])?>
        </div>
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
    </div>
    <br><br><br>

    <button id="aaa">aaa</button>
    <input type="text" name="bbb" id="bbb" value="">
    <div id="treegridbom"></div>

    <br/><br/><br/><br/><br/>

    <div class="form-group">
        <?= Html::Button($model->isNewRecord ? Yii::t('common', '保存') : Yii::t('common', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'btn-submit']); ?>
    </div>
    <?php ActiveForm::end(); ?>
    <br/><br/><br/><br/><br/>
</div>


<?php

$isUpdate = 1;

//弹框控件，其它默认，prompt框改下默认的配置
echo Dialog::widget([
    'dialogDefaults'=>[

        Dialog::DIALOG_CONFIRM => [
            'draggable' => true,
            'closable' => true,
        ],

    ]
]);

///////////////js代码//////////////////
$js=<<<JS

$('#aaa').on('click',function() {
  alert(11);
  $('.ckbadd').attr('disabled','');
});

    //上级物料数据
    var parentJson = {$dataBom};
    //物料库的料
    var dataMtr = {$dataMtr};

    var newRowID = null;//新建行的号
    var selRowId=null;//选择的行号
    var CellBgvalue = '';//格子改变前的值
    var CellBgQty = '';
    var CellBgPos = '';
    //点击查看详情
    
           
    var source = {//对象————生成表格的数据对象
        dataType: "json",
        dataFields: [
            { name: 'id', type: 'number' },//对应行号和bomschild的id号
            { name: 'zc_part_number', type: 'string' },
            { name: 'parent_part', type: 'string' },
            { name: 'Qty', type: 'number' },
            { name: 'Pos', type: 'string' },
        ],
        hierarchy:
        {
            root: 'children'
        },
        localData:[],
        id: 'id',
        addRow: function (rowID, rowData, position, parentID, commit) {//重载添加行函数
            newRowID = rowID;
            commit(true);
        }
    };
    //生成一个树表格控件 
    var dataAdapter = new $.jqx.dataAdapter(source);
    $("#treegridbom").jqxTreeGrid(
    {
        width: '100%',
        height:'600px',
        source: dataAdapter,
        columnsResize: true,//列可以重定义大小
        altRows: true,//隔行变色                  //换页时保存                失去焦点时保存      换选择时保存               
        editSettings: {/*editSingleCell: true,*/saveOnPageChange: true, saveOnBlur: true, saveOnSelectionChange: true,
         //   Esc时取消          回车保存 
         cancelOnEsc: true,saveOnEnter: true,editOnDoubleClick: true },
        ready: function()//加载完html时运行
        {
            $("#treegridbom").jqxTreeGrid('expandAll');
            $("#treegridbom").jqxTreeGrid('hideColumn','mtrid');
        },
        editable:true, //如果是更新可编辑，可显示工具条，可渲染工具条。查看时为false                                
        showToolbar: true,//显示工具条
        renderToolbar: function(toolBar)//渲染工具条
        {
            if(!'$isUpdate')
                return;
            //以下是添加工具条的按钮
            var toTheme = function (className) {
                if (theme == "") return className;
                return className + " " + className + "-" + theme;
            };

            // appends buttons to the status bar.
            var container = $("<div style='overflow: hidden; position: relative; height: 100%; width: 100%;'></div>");
            var buttonTemplate = "<div style='float: left; padding: 3px; margin: 2px;'><div style='margin: 4px; width: 16px; height: 16px;'></div></div>";
            var addButton = $(buttonTemplate);
            var deleteButton = $(buttonTemplate);
            container.append(addButton);
            container.append(deleteButton);

            toolBar.append(container);
            addButton.jqxButton({cursor: "pointer", enableDefault: false, disabled: true, height: 25, width: 25 });
            addButton.find('div:first').addClass(toTheme('jqx-icon-plus'));
            addButton.attr('title','增加');
            
            deleteButton.jqxButton({ cursor: "pointer", disabled: true, enableDefault: false,  height: 25, width: 25 });
            deleteButton.find('div:first').addClass(toTheme('jqx-icon-delete'));
            deleteButton.attr('title','删除');
            
            
            addButton.jqxButton({ disabled: false });
            //根据当前的操作（在选择，在编辑），来控制按钮是否有效
            var updateButtons = function (action) {
            switch (action) {
                case "Select":
                    addButton.jqxButton({ disabled: false });
                    deleteButton.jqxButton({ disabled: false });
                    break;
                case "Unselect":
                    addButton.jqxButton({ disabled: true });
                    deleteButton.jqxButton({ disabled: true });
                    break;
                case "Edit":
                    addButton.jqxButton({ disabled: true });
                    deleteButton.jqxButton({ disabled: true });
                    break;
                case "End Edit":
                    addButton.jqxButton({ disabled: false });
                    deleteButton.jqxButton({ disabled: false });
                    break;
                }
            };
            //选择一行时记住这行id，可在它下面添加行或删除该行时用。
            $("#treegridbom").on('rowSelect', function (event) {
                selRowId = event.args.key;
                updateButtons('Select');
            });
            
            //编辑完该行时，认为是保存
            $("#treegridbom").on('rowEndEdit', function (event) {
                var args = event.args;
                var rowKey = args.key;
                //如果智车料号改变了，才去看这个料存不存在
                /////////////////////
                updateButtons('End Edit');
            });
            
            //编辑前事件，可记住改变前的值
            $("#treegridbom").on('rowBeginEdit', function (event) {
                $('.ckbadd').removeAttr('disabled');
                updateButtons('Edit');
            });
            //添加按钮的函数
            addButton.click(function (event) {
                if (!addButton.jqxButton('disabled')) {
                    $("#treegridbom").jqxTreeGrid('expandRow', selRowId);
                    // add new empty row.                         标志0是新添加的 
                    $("#treegridbom").jqxTreeGrid('addRow', null, {id:0}, 'first', null);
                    // select the first row and clear the selection.
                    $("#treegridbom").jqxTreeGrid('clearSelection');
                    $("#treegridbom").jqxTreeGrid('selectRow', newRowID);
                    // edit the new row.
                    $("#treegridbom").jqxTreeGrid('beginRowEdit', newRowID);
                    updateButtons('add');
                }
            });
            //删除按钮的函数 
            deleteButton.click(function () {
                krajeeDialog.confirm('是否要删除所中项？', function (result) {
                    if (result) 
                    {
                        if (!deleteButton.jqxButton('disabled')) {
                            var arrSelect = [];//选中的放这里，准备删除
                            var isLvl0 = 0;//是否是最高层，如果是最高级记录下，删除时要全删除掉
                            var selection = $("#treegridbom").jqxTreeGrid('getSelection');
                            if (selection.length > 1) {
                                var keys = new Array();
                                for (var i = 0; i < selection.length; i++) {
                                    var tempRowNo = $("#treegridbom").jqxTreeGrid('getKey', selection[i]);
                                    keys.push(tempRowNo);
                                    arrSelect.push($("#treegridbom").jqxTreeGrid('getCellValue',tempRowNo,'id'));
                                    if($("#treegridbom").jqxTreeGrid('getCellValue',tempRowNo,'lvl') ==0)//如果是最高级，记录下
                                        isLvl0 = $("#treegridbom").jqxTreeGrid('getCellValue',tempRowNo,'id');
                                }
                                $("#treegridbom").jqxTreeGrid('deleteRow', keys);
                            }
                            else {
                                if($("#treegridbom").jqxTreeGrid('getCellValue',selRowId,'lvl') ==0)//如果是最高级，记录下
                                    isLvl0 = $("#treegridbom").jqxTreeGrid('getCellValue',selRowId,'id');
                                arrSelect.push($("#treegridbom").jqxTreeGrid('getCellValue',selRowId,'id'));
                                $("#treegridbom").jqxTreeGrid('deleteRow', selRowId);
                            }
                            updateButtons('delete');
                        }
                    }
                });
            });
        },



        columns: [   
            //代表这个字段不可以编辑
            {
              text: '新增料', cellsAlign: 'center', align: "center", columnType: 'template', editable: false,width: 60,
              sortable: false, dataField: null, createEditor: function (row, cellvalue, editor, cellText, width, height) {
                    // construct the editor. 
                    editor.attr('id','checkboxadd');
                    editor.jqxCheckBox({});
                    editor.on('checked', function (event) { 
                        $("#selectcbom").jqxDropDownList({source: dataMtr});
                        $("#textqty").val('');
                        $("#textpos").val('');
                    });
                    editor.on('unchecked', function (event) { 
                        $.get('/ecn/get-bom-part',{id:$("#selectpbom").val()},function(json) {
                            $("#selectcbom").jqxDropDownList({source: json});
                        },"json");
                    });
                },
                getEditorValue: function (row, cellvalue, editor) {
                   // return the editor's value. 
                   
                   return editor.val()==false?'否':'是';
                }
            },
            {
                text: '上级料号', dataField: 'parent_part', width: 160,columnType: "template",id:"qwe",
                createEditor: function (row, cellvalue, editor, cellText, width, height) {
                    // construct the editor. 
                    editor.attr('id','selectpbom');
                    editor.jqxDropDownList({filterable: true, source: parentJson, width: '100%', height: '100%' });
                    editor.on('select', function (event)
                    {
                        if($("#checkboxadd").val() == false)
                        {
                            $.get('/ecn/get-bom-part',{id:event.args.item.value},function(json) {
                                $("#selectcbom").jqxDropDownList({source: json});
                            },"json");
                        }
                    });
                  
                },
                initEditor: function (row, cellvalue, editor, celltext, width, height) {
                   // set the editor's current value. The callback is called each time the editor is displayed.
                   editor.jqxDropDownList('selectItem', cellvalue);
                },
                getEditorValue: function (row, cellvalue, editor) {
                   // return the editor's value. 
                   
                   return parentJson[editor.val()];
                }
            },
            {
                text: '智车料号', dataField: 'zc_part_number', width: 160,columnType: "template",
                createEditor: function (row, cellvalue, editor, cellText, width, height) {
                    editor.attr('id','selectcbom');
                    // construct the editor. 
                    editor.jqxDropDownList({filterable: true,width: '100%', height: '100%' });
                    
                    editor.on('select', function (event)
                    {
                        if($("#checkboxadd").val() == false)
                        {
                            $.get('/ecn/get-pos-qty',{id:event.args.item.value},function(json) {
                                $("#textqty").val(json.qty);
                                $("#textpos").val(json.ref_no);
                            },"json");   
                        }
                        else 
                        {
                            $("#textqty").val('');
                            $("#textpos").val('');
                        }
                    });

                    
                },
                initEditor: function (row, cellvalue, editor, celltext, width, height) {
                    // set the editor's current value. The callback is called each time the editor is displayed.
                    editor.jqxDropDownList('selectItem', cellvalue);
                },
                getEditorValue: function (row, cellvalue, editor) {
                    // return the editor's value.
                    if(editor.val()!='')
                         return editor.jqxDropDownList('getItem', editor.val()).label;
                }
            },
            { text: '数量', dataField: 'Qty', width: 100 ,editable:true,
              createEditor: function (row, cellvalue, editor, cellText, width, height) {
                  editor.attr('id','textqty');
              }
            },
            { text: '位置', dataField: 'Pos', width: 800 ,editable:true,
              createEditor: function (row, cellvalue, editor, cellText, width, height) {
                  editor.attr('id','textpos');
              }
            }

        ]
    });

JS;

$this->registerJs($js);
$this->registerJsFile('/jqwidgets_assets/jqwidgets/jqxlistbox.js',['depends'=>['yii\web\JqueryAsset']]);
$this->registerJsFile('/jqwidgets_assets/jqwidgets/jqxdropdownlist.js',['depends'=>['yii\web\JqueryAsset']]);
$this->registerJsFile('/jqwidgets_assets/jqwidgets/jqxcheckbox.js',['depends'=>['yii\web\JqueryAsset']]);
$this->registerJsFile('/jqwidgets_assets/jqwidgets/jqxtextarea.js',['depends'=>['yii\web\JqueryAsset']]);
$this->registerJsFile('/jqwidgets_assets/jqwidgets/jqxcheckbox.js',['depends'=>['yii\web\JqueryAsset']]);
$this->registerJsFile('/jqwidgets_assets/jqwidgets/jqxinput.js',['depends'=>['yii\web\JqueryAsset']]);
$this->registerJsFile('/jqwidgets_assets/jqwidgets/jqxslider.js',['depends'=>['yii\web\JqueryAsset']]);
$this->registerJsFile('/jqwidgets_assets/jqwidgets/jqxcheckbox.js',['depends'=>['yii\web\JqueryAsset']]);

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

/**
* 提交表单的按钮
*/
$('#btn-submit').on('click',function() {
    // var dataall = $("#treegridbom").jqxTreeGrid('getRows');
    // $('#bbb').val(dataall);
    console.log(location.href);
    krajeeDialog.prompt({label:'备注', placeholder:'任务的备注...'}, function (result) {
        if (result!=null) 
        {
            $('#taskRemark').val(result);
            $('#taskCommit').val('1');
        }
        else 
        {
            $('#taskRemark').val('');
            $('#taskCommit').val('0');
        }   
        //提交的参数
        var getData = {$_GET['TaskId']};
        var postUrl = '/ecn/create?TaskId='+getData;//url
        
        var ecn={reason:$('#ecn-reason').val(),detail:$('#ecn-detail').val(),module:$('#ecn-module').val(),
            project_process:$('#ecn-project_process').val(),change_now:$('#ecn-change_now').val(),affect_stock:$('#ecn-affect_stock').val(),remark:$('#ecn-remark').val(),};

        var dataBom = $("#treegridbom").jqxTreeGrid('getRows');
        
        var postData = {ecn:ecn,dataBom:dataBom};
        
        console.log(postData);
        
        
        
        $.post(postUrl,postData,function() {
          
        },'json');return;
    });
});
JS;
$this->registerJs($js);




