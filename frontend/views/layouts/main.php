<?php
use yii\helpers\Html;
use frontend\models\Tasks;
use frontend\models\UserTask;
/* @var $this \yii\web\View */
/* @var $content string */


if (Yii::$app->controller->action->id === 'login') {
/**
 * Do not use this code in your template. Remove it. 
 * Instead, use the code  $this->layout = '//main-login'; in your controller.
 */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    if (class_exists('backend\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    } else {
        app\assets\AppAsset::register($this);
    }

    dmstr\web\AdminLteAsset::register($this);

    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta name="renderer" content="webkit"/>

        <meta name="force-rendering" content="webkit"/>

        <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"/>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head(); ?>
        <style>
.table-hover > tbody > tr:hover {
    background-color: #c4e3f3;
}
        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>

        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset]
        )
        ?>

        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>

    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
    <?php
    //这个php块是显示个人任务的数据气泡的功能
    if(!empty(Yii::$app->user->id)){
        $arrRwqd = [Tasks::STATUS_UNCOMMIT,Tasks::STATUS_REJECTED,Tasks::STATUS_CREATE_ECN];
        $rwqd = Tasks::find()->where(['in','status',$arrRwqd])->andWhere('user_id='.Yii::$app->user->id)->count();

        $spqd = UserTask::find()->leftJoin('tasks','tasks.id=user_task.task_id')->
        where(['user_task.status'=>UserTask::STATUS_UNAPPROVE,
            "user_task.user_id"=>Yii::$app->user->id,
            'tasks.status'=>Tasks::STATUS_COMMITED,
            'user_task.approve_able'=>1])->count();

$js = <<<JS
    $("#grrw span").eq(0).after('<span class="badge" style="margin-top: -20px;">'+($rwqd+$spqd)+'</span>');
    $("#rwqd span").after('&emsp;<span class="badge" style="margin-top: -20px;">'+$rwqd+'</span>');
    $("#spqd span").after('&emsp;<span class="badge" style="margin-top: -20px;">'+$spqd+'</span>');
    $('#uploadbom').on('click',function() {
        $.post('/ecr/create-check',function(obj) {
            if(obj==true)
                location.href = '/boms/upload-bom';
            else
                alert('有未处理完的BOM，请处理完再新建');
        });   
        return false;  
    });
JS;
        $this->registerJs($js);

    }

    ?>
    <?php $this->endPage() ?>
<?php } ?>
