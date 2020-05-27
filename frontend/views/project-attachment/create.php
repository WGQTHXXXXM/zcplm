<?php


use kartik\file\FileInput;
use yii\helpers\Url;
use kartik\dialog\Dialog;


$this->title ='项目文件上传';
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Projects'), 'url' => ['/projects/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common','Project Manage View'),
    'url' => ['/projects/project-manage-view','id'=>$mdlPjtPcs->root]];
$this->params['breadcrumbs'][] = $mdlPjtPcs->name;

$action = Url::to(['/project-attachment/view', 'id' => $id]);
echo '<br><br><label class="control-label">上传的文件：'. $mdlPjtPcs->name .'</label><br><br><div class="row"><div class="col-md-7">';
echo FileInput::widget([
    'model' => $model,
    'attribute' => 'upload_file',
    'pluginOptions' => [
        // 需要预览的文件格式
        'previewFileType' => 'any',
        'allowedFileExtensions' => [
            'jpg', 'bmp', 'png', 'txt', 'pdf', 'zip', 'rar', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
        'maxFileSize'=>10486,
        // 是否展示预览图
        'initialPreviewAsData' => true,
        // 异步上传的接口地址设置
        'uploadUrl' => Url::toRoute(['/project-attachment/upload?id='.$_GET['id']]),
        'uploadAsync' => true,
        // 最少上传的文件个数限制
        'minFileCount' => 1,
        //
        'dropZoneTitle'=>'',
        'dropZoneClickTitle'=>'点击可选择上传多个文件',
        // 是否显示[移除]按钮，指input上面的[移除]按钮，非具体图片上的移除按钮
        'showRemove' => true,
        // 是否显示[上传]按钮，指input上面的[上传]按钮，非具体图片上的上传按钮
        'showUpload' => true,
        // 是否显示[选择]按钮，指input上面的[选择]按钮，非具体图片上的按钮
        'showBrowse' => true,
        // 展示图片区域是否可点击选择多文件
        'browseOnZoneClick' => true,
        // 如果要设置具体图片上的移除、上传和展示按钮，需要设置该选项
        'fileActionSettings' => [
            // 设置具体图片的查看属性为false,默认为true
            'showZoom' => false,
            // 设置具体图片的上传属性为true,默认为true
            'showUpload' => false,
            // 设置具体图片的移除属性为true,默认为true
            'showRemove' => true,
        ],
    ],
    // 一些事件行为
    'pluginEvents' => [
        // 上传成功后的回调方法，需要的可查看data后再做具体操作，一般不需要设置
        // window.location.assign加载新的url
        'fileuploaded' => 'function (event, data, id, index) {
                    window.location.assign("'.$action.'");
                }',
    ],
]);

echo '</div></div>';


?>




