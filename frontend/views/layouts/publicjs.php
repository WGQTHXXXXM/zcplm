<?php
use yii\bootstrap\Modal;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\helpers\Url;

echo Dialog::widget();//提示框

//隐藏的按钮，显示物料信息
echo Html::a('查看', '#', [
    'id' => 'view',
    'data-toggle' => 'modal',
    'data-target' => '#create-modal',
    'class' => 'btn btn-success hidden',
]);

//模型的初化
Modal::begin([
    'id' => 'create-modal',
    'header' => '<h4 class="modal-title">查看</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]);

$getPartData = Url::toRoute("/modify-material/get-part-data");//根据输入的厂家料号，获得这个料的其它数据

$js = <<<JS

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
                               $('.modal-body').html(data);
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
JS;
$this->registerJs($js);
Modal::end();
?>