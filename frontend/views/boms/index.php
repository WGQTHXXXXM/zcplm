<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\web\JQWidgetsAsset;
use kartik\dialog\Dialog;
use frontend\models\BomsParent;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('bom', 'BOM');
$this->params['breadcrumbs'][] = ['label' => Yii::t('bom', 'BOM Search'), 'url' => ['search/bom-index']];
$this->params['breadcrumbs'][] = $this->title;

JQWidgetsAsset::register($this);
$getBomData = Url::toRoute("/boms/index");
$getBomDataUrl = Url::toRoute("/boms/index?material_id=$model->real_material&forward=$forward&multiLevel=$multiLevel");
$exportData = Url::toRoute("/boms/export-data");
?>
<div class="boms-index">
    <?php
    $pbomStatus = '';
    if(isset($model->status))
        $pbomStatus = '('.BomsParent::STATUS[$model->status].')';
    ?>
    <h1><?= Html::encode($this->title.($forward==1? Yii::t('bom', 'Forward Query'):Yii::t('bom', 'Reverse Query'))).': '.
        $model->realMaterial->zc_part_number.$pbomStatus ?></h1>

    <p>
        <!--?= Html::a(Yii::t('bom', 'Create Boms'), ['create'], ['class' => 'btn btn-success']) ?-->
    </p>
    <!--?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'parent_id',
            'parent_version',
            'child_id',
            'child_version',
            // 'status',
            // 'release_time:datetime',
            // 'effect_date',
            // 'expire_date',
            // 'qty',
            // 'ref_no',
            // 'zc_part_number2_id',
            // 'zc_part_number3_id',
            // 'zc_part_number4_id',
            // 'type',
            // 'creater_id',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?-->
    <div class="row">
        <div class="col-md-2 expandAll">
            <a href="#expandAll">全部展开</a>
        </div>
        <div class="col-md-2 collapseAll">
            <a href="#collapseAll">全部折叠</a>
        </div>
    </div>
    <p></p>
    <div class="row">
        <div class="col-md-2">
            <a href="<?= $getBomData.'?material_id='.$model->real_material.'&forward=0&multiLevel=0' ?>">查找上级使用者</a>
        </div>
        <div class="col-md-2">
            <a href="<?= $getBomData.'?material_id='.$model->real_material.'&forward=0&multiLevel=1' ?>">查找所有使用者</a>
        </div>
    </div><p></p>
    <div class="row" id="exportData">
        <div class="col-md-2">
            <a href="<?php
            if ($model->status == BomsParent::STATUS_FORBIDDEN)
                echo 'javascript:void(0)" style="color:#666';
            else
                echo $exportData.'?material_id='.$model->real_material.'&multiLevel=0';
            ?>">下载单级BOM</a>
        </div>
        <div class="col-md-2">
            <a href="<?php
            if ($model->status == BomsParent::STATUS_FORBIDDEN)
                echo 'javascript:void(0)" style="color:#666';
            else
                echo $exportData.'?material_id='.$model->real_material.'&multiLevel=1';
            ?>">下载多级BOM</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <label>BOM Version:</label>
            <select id="version"></select>
        </div>
    </div>
    <p></p>
    <div id="treeGrid" class="box">
    </div>


    <div class="row">
        <div class="col-md-2 expandAll">
            <a href="#expandAll">全部展开</a>
        </div>
        <div class="col-md-2 collapseAll">
            <a href="#collapseAll">全部折叠</a>
        </div>
    </div>
</div>
<?php
$getVersionByChildId = Url::toRoute("/boms/get-version-by-child-id");
$goMaterialView = Url::toRoute("/materials/view");
$zc_part_number = Yii::t('material', 'Zhiche Part Number');
$zc_part_number2 = Yii::t('material', 'Second Zhiche Part Number');
$zc_part_number3 = Yii::t('material', 'third Zhiche Part Number');
$zc_part_number4 = Yii::t('material', 'fourth Zhiche Part Number');
$mfr_part_number = Yii::t('material', 'Manufacturer Part Number');
$mfr_part_number2 = Yii::t('material', 'Second Manufacturer Part Number');
$mfr_part_number3 = Yii::t('material', 'third Manufacturer Part Number');
$mfr_part_number4 = Yii::t('material', 'fourth Manufacturer Part Number');
$manufacturer = Yii::t('material', 'Manufacturer');
$manufacturer2 = Yii::t('bom', 'Second Manufacturer');
$manufacturer3 = Yii::t('bom', 'Third Manufacturer');
$manufacturer4 = Yii::t('bom', 'Fourth Manufacturer');
$purchase_level = Yii::t('material', 'Purchase Level');
$part_name = Yii::t('material', 'Part Name');
$description = Yii::t('material', 'Description');
$unit = Yii::t('material', 'Unit');
$pcb_footprint = Yii::t('material', 'Pcb Footprint');
$qty = Yii::t('bom', 'Qty');
$ref_no = Yii::t('bom', 'Reference No.');
$assy_level = Yii::t('bom', 'Assy Level');
$status = Yii::t('bom', 'Status');
$notRelease = Yii::t('bom', 'Not Release');
$release = Yii::t('bom', 'Release');

$BomStatus = json_encode(BomsParent::STATUS);

$Js = <<<JS
    // prepare the data
    var source =
    {
        dataType: "json",
        dataFields: [
            { name: 'id', type: 'number' },
            { name: 'level', type: 'number' },
            { name: 'parent_id', type: 'number' },
            { name: 'child_id', type: 'number' },
            { name: 'parent_version', type: 'number' },
         //   { name: 'child_version', type: 'number' },
            { name: 'zc_part_number', type: 'string' },
            { name: 'purchase_level', type: 'string' },
            { name: 'part_name', type: 'string' },
            { name: 'description', type: 'string' },
            { name: 'unit', type: 'string' },
            { name: 'pcb_footprint', type: 'string' },
            { name: 'qty', type: 'number' },
            { name: 'ref_no', type: 'string' },
            { name: 'mfr_part_number', type: 'string' },
            { name: 'manufacturer', type: 'string' },
            { name: 'zc_part_number2', type: 'string' },
            { name: 'mfr_part_number2', type: 'string' },
            { name: 'manufacturer2', type: 'string' },
            { name: 'zc_part_number3', type: 'string' },
            { name: 'mfr_part_number3', type: 'string' },
            { name: 'manufacturer3', type: 'string' },
            { name: 'zc_part_number4', type: 'string' },
            { name: 'mfr_part_number4', type: 'string' },
            { name: 'manufacturer4', type: 'string' },
            { name: 'status', type: 'number' },
            { name: 'pv_release_time', type: 'date' },
            { name: 'pv_effect_date', type: 'date' },
            { name: 'pv_expire_date', type: 'date' },
            { name: 'bom_expire_date', type: 'date' },
            { name: 'real_material', type: 'number' },
          //  { name: 'type', type: 'number' },
          //  { name: 'creater_id', type: 'number' },
          //  { name: 'creater', type: 'string' },
          //  { name: 'created_at', type: 'date' },
          //  { name: 'updated_at', type: 'date' }
            { name: 'children', type: 'array' }
        ],
        hierarchy:
        {
            root: 'children'
        },
        id: 'child_id',
        url: "$getBomDataUrl"
    };
    var dataAdapter = new $.jqx.dataAdapter(source);
    ////////自适应屏幕高度////////////////
    var h = document.documentElement.clientHeight || document.body.clientHeight;//屏幕的高
    /////////////
    // create Tree Grid
    var BomStatus = $BomStatus;
    $("#treeGrid").jqxTreeGrid(
    {
    //    width: 850,
        width: '100%',
        height: h-350,
        source: dataAdapter,
        sortable: true,
        columnsResize: true,
        altRows: true,
        icons: true,
        ready: function()
        {
            $('#treeGrid').jqxTreeGrid({enableBrowserSelection: true });
            
            if ($forward)
                $("#treeGrid").jqxTreeGrid('expandRow', '$model->real_material');
            else 
                $("#treeGrid").jqxTreeGrid('expandAll');
        },
        columns: [
          { 
            text: '$zc_part_number', dataField: 'zc_part_number', width: 260, 
            cellsRenderer: function (rowKey, dataField, value, data) {
                var bom_expire_date = new Date(data.bom_expire_date);
                bom_expire_date = bom_expire_date.getTime();
                return "<a href='#' onclick='rowClick("+rowKey+', '+data.real_material+', '+bom_expire_date+")'>"+value+"</a>"; //+rowKey+', '+data.parent_id+', '+value+', '+data.child_id+
            }
          },
//          {
//            text: '$status', dataField: 'status', width: 60,
//            cellsRenderer: function (rowKey, dataField, value, data) {
//                return BomStatus[value];
//            }
//          },
          { text: '$assy_level', dataField: 'level', width: 60 },
          { text: '$purchase_level', dataField: 'purchase_level', width: 60 },
          { text: '$part_name', dataField: 'part_name', width: 100 },
          { text: '$description', dataField: 'description', width: 300 },
          { text: '$pcb_footprint', dataField: 'pcb_footprint', width: 100 },
          { text: '$qty', dataField: 'qty', width: 50 },
          { text: '$unit', dataField: 'unit', width: 50 },
          { text: '$ref_no', dataField: 'ref_no', width: 150 },
          { text: '$mfr_part_number', dataField: 'mfr_part_number', width: 150 },
          { text: '$manufacturer', dataField: 'manufacturer', width: 100 },
          { text: '$zc_part_number2', dataField: 'zc_part_number2', width: 150 },
          { text: '$mfr_part_number2', dataField: 'mfr_part_number2', width: 150 },
          { text: '$manufacturer2', dataField: 'manufacturer2', width: 110 },
          { text: '$zc_part_number3', dataField: 'zc_part_number3', width: 150 },
          { text: '$mfr_part_number3', dataField: 'mfr_part_number3', width: 150 },
          { text: '$manufacturer3', dataField: 'manufacturer3', width: 110 },
          { text: '$zc_part_number4', dataField: 'zc_part_number4', width: 150 },
          { text: '$mfr_part_number4', dataField: 'mfr_part_number4', width: 150 },
          { text: '$manufacturer4', dataField: 'manufacturer4', width: 110 }
        ],
    });
    //全部展开
    $(".expandAll").click(function(){
        $("#treeGrid").jqxTreeGrid('expandAll');
    });
    //全部折叠
    $(".collapseAll").click(function(){
        $("#treeGrid").jqxTreeGrid('collapseAll');
     //   $("#treeGrid").jqxTreeGrid('expandRow', '$model->id');
    });
    
    //向BOM Version下拉框里添加数据
    $.each($versionList, function(key,val) {
        var select = "";
        if (val.parent_version == '$model->parent_version') {
            select = " selected";
        }
        option = "<option value=\"" + val.real_material + "\"" + select + ">" + val.parent_version+"——"+val.zc_part_number+ "</option>";
        $("#version").append(option);
    });
    //版本改变时显示相应版本的BOM
    $("#version").change(function() {
        var real_material = $('#version').val();
        window.location.href='$getBomData?material_id='+real_material+'&forward=1';
    });
    
    //逆向查询时不显示导出BOM相关的链接
    if ($forward==0) $("#exportData").addClass('hide');
    //自适应屏幕高度
JS;
$this->registerJs($Js);

$Js = <<<JS
    //点击Bom行时决定页面显示Bom信息还是物料信息
    function rowClick(rowKey,child_id,bom_expire_date) {
        var rows = $("#treeGrid").jqxTreeGrid('getRows');
        if (rows[0].records.length) {//如果当前页面顶层有子级（即length大于0）
            window.location.href='$getBomData?material_id='+child_id+'&forward=1';
            //通过ajax获得该bom项对应的版本
//            $.get(
//                "$getVersionByChildId",
//                {child_id:child_id, bom_expire_date:bom_expire_date},//传的参数
//                function(json) {//返回数据后
//                    if(json.status == 1){//
//                        
//                        window.location.href='$getBomData?material_id='+json.data.real_material+'&forward=1';
//                    }
//                    else
//                        krajeeDialog.alert(json.message);
//                },
//                "json"
//            );
        } else {//如果当前页面顶层无子级（即length等于0）
            window.location.href='$goMaterialView?id='+rowKey;
        }
    }
JS;
$this->registerJs($Js,\yii\web\View::POS_BEGIN);
?>
