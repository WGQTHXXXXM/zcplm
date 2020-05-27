<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use kartik\dialog\Dialog;

/* @var $this yii\web\View */
/* @var $mdlQsm frontend\models\QualitySystemManage */

$this->title = '上传';
$this->params['showTitle']=0;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Quality System Manages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('/css/self/self.css');
$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);

?>
    <input id="taskRemark" type="hidden" name="taskRemark">
    <input id="taskCommit" type="hidden" name="taskCommit" value="1" >

<div class="row">
    <div class="container-sw col-md-5">
        <span class="title-sw">上传文件</span>
        <br>
        <div class="row form-group">
            <div class="col-md-3">
                <label class="control-label" for="qualitysystemmanage-parent_name">选择上传的文件</label>：
            </div>
            <div class="col-md-8">
                <?php echo \kartik\file\FileInput::widget(['name' => 'uploadFile', 'pluginOptions' => [
                    'showPreview' => false, 'showCaption' => true, 'showRemove' => true,'showUpload' => false]]); ?>
            </div>
        </div>
        <div class="row form-group">
            <?php
            $temp = ['template'=>'<div class="col-md-3">{label}：</div><div class="col-md-8">{input}</div>{error}'];
            echo $form->field($mdlQsmAttachment, 'remark',$temp)->textarea(['rows' => 4,'placeholder'=>'文件描述']);
            ?>
        </div>


    </div>

    <div class="container-sw col-md-5">
        <span class="title-sw">基本信息</span>
        <br>
        <?= DetailView::widget([
            'model' => $mdlQsm,
            'options' => ['class'=>'table table-striped table-bordered detail-view','style'=>'width:90%;'],
            'template' => '<tr><th width="100px">{label}:</th><td>{value}</td></tr>',
            'attributes' => [
                'name',
                'parent_name',
                'son_name',
                [
                    'attribute' => 'department_belong_id',
                    'value' => $mdlQsm->belongDepart->name,
                ],
                [
                    'attribute' => 'file_code',
                ],
                [
                    'attribute' => 'file_class',
                    'value' => $mdlQsm::FILE_CLASS[$mdlQsm->file_class],
                ],
                [
                    'attribute' => 'maxVersion',
                    'label'=>'最高版本'
                ]
            ],
        ]) ?>
    </div>

</div>

<hr style="border-top:2px solid #000">
<h3 style="color:#367EA9">审批流</h3>

<div class="row">
    <?php

    //审批人
    foreach ($departmentApprove as $lvl=>$lvlApproves){
        echo '<div class="col-md-2 container-sw-approver" >';
        echo '<span class="title-sw-approver">第'.$lvl.'级审批人</span>';
        foreach ($lvlApproves as $dep=>$lvlApprove){
            echo $form->field($mdlQsmAttachment, 'departLvl['.$lvl.']['.$dep.']')->widget(Select2::className(),[
                'data' => $lvlApprove,
                'options' => ['placeholder' => '请选择审批人 ...'],
                'pluginOptions' => ['tokenSeparators' => [',', ' ']],
            ])->label($dep);
        }
        echo '</div>';
    }

    ?>
</div>
<?php
echo '<div class="form-group">';
echo Html::Button('提交',['class' => 'btn btn-success','id'=>'btn-submit']);
echo '</div>';
ActiveForm::end();

echo Dialog::widget([
    'libName' => 'submitprompt',
    'dialogDefaults'=>[
        Dialog::DIALOG_PROMPT => [
            'draggable' => true,
            'closable' => true,
            'title' => '确定提交',
            'buttons' => [
                [
                    'label' => '稍后提交任务',
                    'icon' => Dialog::ICON_CANCEL,
                    'cssClass'=>'hide'
                ],
                [
                    'label' => '确定',
                    'icon' => Dialog::ICON_OK,
                ],
            ]
        ],

    ]
]);


$js=<<<JS
$('#btn-submit').on('click',function() {  
    submitprompt.prompt({label:'备注', placeholder:'任务的备注...',title:'是否马上提交任务'}, function (result) {
        var remark = $("input[name='krajee-dialog-prompt']").val();
        $('#taskRemark').val(remark);        
        $('#w0').submit();
    });
});

JS;

$this->registerJs($js)


?>