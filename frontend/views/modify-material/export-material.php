<?php
$this->title = Yii::t('material', 'Export Materials');
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="cnt">
<div style="float:left;"><br/><br/><br/><br/><br/><br/><label>这可能会花费你2分钟时间,耐心等。</label><br/>
    <a target="_blank" href="/modify-material/write-excel-elec"  style="margin:0 30px;" id="elec" class="btn btn-success">下载电子物料</a>
    <a target="_blank" href="/modify-material/write-excel-waigou" style="margin:0 30px;" id="waigou" class="btn btn-success">下载外购部件物料</a>
    <a target="_blank" href="/modify-material/write-excel-struct"  style="margin:0 30px;" id="struct" class="btn btn-success">下载结构物料</a>
</div></div>
<div style="margin:0 auto;width: 200px;" >
    <div style="margin:0 auto"  id="img" class="hide" >
        <img id="img" src="/css/images/loading.gif"><br/><br/><br/>
        <label>正在从数据库导出，</label><br/><br/><label>这可能会花费你2分钟时间。</label><br/><br/><br/><br/>
    </div>
</div>
<?php
$Js = <<<JS
// $('#elec').on('click',function() {
//     $('.btn').attr("disabled", true); 
// });
// $('#waigou').on('click',function() {
//     $('.btn').attr("disabled", true); 
// });
// $('#struct').on('click',function() {
//     $('.btn').attr("disabled", true); 
// });

JS;
$this->registerJs($Js);
