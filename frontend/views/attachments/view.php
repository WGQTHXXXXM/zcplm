<?php

use kartik\file\FileInput;
//use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Attachments */

$this->title = $material->mfr_part_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Materials'), 'url' => ['/materials/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attachments-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="box table-responsive">
    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

         //   'attachment_id',
         //   'material_id',
         //   'attachment_url:url',
            [
                'attribute' => 'attachment_url',
                'value' => function($model) {
                    $filename = substr($model->attachment_url, strrpos($model->attachment_url, '/')+1);
                    return Html::a($filename, Url::to(['attachments/download', 'pathFile' => $model->attachment_url, 'filename' => $filename]));
                },
                'format' => 'raw',
            ],
            'version',
            'updated_at:date',
            'created_at:date',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
    </div>

    <!--?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?-->

    <!--?= $form->field($model, 'upload_file[]')->widget(FileInput::classname(), [ -->
    <?php
        // With model & without ActiveForm
        // Note for multiple file upload, the attribute name must be appended with
        // `[]` for PHP to be able to read an array of files
        $action = Url::to(['/attachments/view', 'id' => $id]);
        $title = Yii::t('common', 'Upload File');
        echo '<label class="control-label">'. $title .'</label>';
        echo FileInput::widget([
            'model' => $model,
            'attribute' => 'upload_file[]',
            'options' => ['multiple' => true],
            'pluginOptions' => [
                // 需要预览的文件格式
                'previewFileType' => 'any',
                'allowedFileExtensions' => [
                    'jpg', 'bmp', 'png', 'txt', 'pdf', 'zip', 'rar', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
                'maxFileSize'=>10486,
                // 预览的文件
                'initialPreview' => $p1,
                // 需要展示的图片设置，比如图片的宽度等
                'initialPreviewConfig' => $p2,
                // 是否展示预览图
                'initialPreviewAsData' => true,
                // 异步上传的接口地址设置
                'uploadUrl' => Url::toRoute(['/attachments/async-upload']),
                // 异步上传需要携带的其他参数，比如material_id等
                'uploadExtraData' => [
                    'material_id' => $id,
                ],
                'uploadAsync' => true,
                // 最少上传的文件个数限制
                'minFileCount' => 1,
                // 最多上传的文件个数限制
                'maxFileCount' => 5,
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
    ?>
</div>

<!--?php ActiveForm::end(); ?-->
<?php
$msg = Yii::t('common', 'Are you sure you want to delete this file?');
$js = <<<JS
$("#upload-upload_file").on("filepredelete", function(jqXHR) {
    var abort = true;
    if (confirm('$msg')) {
        abort = false;
    }
    return abort; // you can also send any data/object that you can receive on `filecustomerror` event
});
JS;
$this->registerJs($js);
?>
