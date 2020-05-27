<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

?>
<h1><?php echo '智车料号：'.$model->zc_part_number.'-------厂家料号：'.$model->mfr_part_number ?></h1>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <input type="button" class="btn btn-success add-upload" value="点击增加上传附件">
    <div id="upload">
        <br>
        <div class="row">
            <div class="col-md-4"><label class="control-label">附件</label>
                <input type="file" class="filefirst" name="attachment[]" data-show-preview="false" data-show-upload="false">
            </div>
            <div class="col-md-4"><label class="control-label">备注</label>
                <input type="text" class="form-control" name="attachment_remark[]">
            </div>
        </div>
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
            ?>
        </div>
    </div>

<label for=""><input type="checkbox" name="ismany" id="ismany">多物料用</label>
<div id="mtrtable">

</div>
    <br><br><br>
    <div class="form-group">
        <?= Html::submitButton('上传',['class' => 'btn btn-success','id'=>'btn-submit']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS

//上传规格书的控件
$('.filefirst').fileinput({
        'data-show-preview':false,
        'showUpload':false,
        'maxFileSize':60000
    });

///////////////////增加上传的插件///////////////////////   

var countUpload = 1;

$('.add-upload').on('click',function() {
    
      $('#upload').append('<div class="row" id="del-upload'+countUpload+'"><br>' +
           '<div class="col-md-4"><label class="control-label">附件</label>' +
            '<input type="file" class="file" name="attachment[]" data-show-preview="false"></div>' +
           '<div class="col-md-4"><label class="control-label">备注</label>' +
            '<input type="text" class="form-control" name="attachment_remark[]"></div>' +
            '<div class="col-md-1"><label class="control-label">&emsp;</label>' +
             '<input type="button" class="btn btn-default form-control" value="删除附件" id="btn-del-upload'+countUpload+'" ></div>'+
       '</div>');
      $('.file').fileinput({
          'data-show-preview':false,
          'showUpload':false      
      });
      
      $('#btn-del-upload'+countUpload).on('click',function() {
            $(this).parent().parent().remove();
      });
      countUpload++;
});

//点击多物料用
$('#ismany').on('click',function() {
    if($('.file-caption-name').prop('title')==''){
        $('#mtrtable').html('');
        return;
    }
    if($(this).is(':checked')){
        var fileName = $('.file-caption-name').prop('title');
        var pos = fileName.lastIndexOf('.');
        var mtrName = fileName.substring(0,pos);
       
        $.post('/modify-material/material-like',{mtrLike:mtrName},function(json) {
            var strHtml = '<table border="1" cellpadding="20" ><tr><td>智车料号</td><td>厂家料号</td></tr>';
            for(var arrMtr in json.data){
                console.log(arrMtr);
                strHtml += '<tr><td><div style="width:150px;height:30px;">'+json.data[arrMtr].zc_part_number+
                '</div></td><td><div>'+json.data[arrMtr].mfr_part_number+'</div></td></tr>';
            }
            strHtml += '</table>';
            $('#mtrtable').html(strHtml);
        },'json');
    } else {
        $('#mtrtable').html('');
    }
})


JS;
$this->registerJs($js);


?>


