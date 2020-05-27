<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
Yii::$app->name = '智车优行PLM';
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">PLM</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">


                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $directoryAsset ?>/img/boxed-bg.jpg" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?=Yii::$app->user->identity['username']?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-footer">
                            <div class="pull-left">
                                <!--a href="#" class="btn btn-default btn-flat">Profile</a-->
                                <?= Html::a(
                                    Yii::t('rbac-admin', 'Change Password'),
                                    ['/admin/user/change-password'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    Yii::t('rbac-admin', 'Sign out'),
                                    ['/admin/user/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>

                <li>
                    <a></a>
                </li>
            </ul>
        </div>
    </nav>
</header>
