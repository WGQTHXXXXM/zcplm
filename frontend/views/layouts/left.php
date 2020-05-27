<?php
use mdm\admin\components\MenuHelper;
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    [
                        'label' => '项目管理',
                        'icon' => 'fa fa-automobile',
                        'url' => '#',
                        'items' => [
                            ['label' => '项目', 'icon' => 'fa fa-circle-o', 'url' => ['/projects/index'],],
                            ['label' => 'APQP清单', 'icon' => 'fa fa-circle-o', 'url' => ['/projects/show-tree-template'],],
                            /*   ['label' => '模块', 'icon' => 'fa fa-circle-o', 'url' => ['/modules/index'],],
                               ['label' => 'BOM', 'icon' => 'fa fa-circle-o', 'url' => ['/boms/index'],],*/
                        ],
                    ],
                    [
                        'label' => 'BOM管理',
                        'icon' => 'fa fa-sitemap',
                        'url' => '#',
                        'items' => [
                            ['label' => 'BOM搜索', 'icon' => 'fa fa-search', 'url' => ['/search/bom-view'],],
                            ['label' => 'BOM比较', 'icon' => 'fa fa-circle-o', 'url' => ['/boms/compare'],],
                            ['label' => '初版BOM创建', 'icon' => 'glyphicon glyphicon-upload',
                                'url' => ['/boms/upload-bom'],
                                'options'=>['id'=>'uploadbom'],
                            ],
                        ],
                    ],
                    [
                        'label' => '物料管理',
                        'icon' => 'fa fa-th',
                        'url' => '#',
                        'items' => [
                            ['label' => '物料', 'icon' => 'fa fa-circle-o', 'url' => ['/materials/index'],],
                            //['label' => '附件', 'icon' => 'fa fa-circle-o', 'url' => ['/attachments/index'],],
                            ['label' => '物料规则管理', 'icon' => 'fa fa-circle-o', 'url' => ['/materials/material-encode-rule'],],
                            ['label' => '物料导出', 'icon' => 'fa fa-circle-o', 'url' => ['/modify-material/export-material'],],
                            ['label' => '物料批量导入', 'icon' => 'fa fa-circle-o', 'url' => ['/modify-material/lead-material'],],
                        ],
                    ],
                    [
                        'label' => '个人任务',
                        'icon' => 'fa fa-user',
                        'url' => '#',
                        'options'=>['title'=>'如欲撤销任务，请联系管理员','id'=>'grrw','class'=>'active'],
                        'items' => [
                            [
                                'label' => '任务管理',
                                'icon' => 'fa fa-circle-o',
                                'url' => ['/tasks/admin-index'],
                                'options'=>[
                                    'title'=>'如欲撤销任务，请联系管理员',
                                    'class'=>Yii::$app->authManager->checkAccess(Yii::$app->user->id, '/tasks/admin-index')?'':'hide'
                                ]
                            ],
                            [
                                'label' => '审批管理',
                                'icon' => 'fa fa-circle-o',
                                'url' => ['/user-task/admin-index'],
                                'options'=>['class'=>Yii::$app->authManager->checkAccess(Yii::$app->user->id,'/user-task/admin-index')?'':'hide']
                            ],
                            ['label' => '我的任务', 'icon' => 'fa fa-circle-o', 'url' => ['/tasks/index'],'options'=>['id'=>'rwqd']],
                            ['label' => '我的审批', 'icon' => 'fa fa-circle-o', 'url' => ['/user-task/index'],'options'=>['id'=>'spqd']],
                        ],
                    ],
                    [
                        'label' => '变更管理',
                        'icon' => 'glyphicon glyphicon-retweet',
                        'url' => '#',
                        'items' => [
                            ['label' => 'ECR', 'icon' => 'fa fa-circle-o', 'url' => '/ecr/index'],
                            ['label' => 'ECN', 'icon' => 'fa fa-circle-o', 'url' => '/ecn/index'],
                            ['label' => 'ECR影响范围设置', 'icon' => 'fa fa-circle-o', 'url' => '/ecr/set-effect-range']
                        ],

                    ],
                    [
                        'label' => '质量体系',
                        'icon' => 'glyphicon glyphicon-glass',
                        'url' => '#',
                        'items' => [
                            ['label' => '总览看板', 'icon' => 'fa fa-circle-o', 'url' => '/quality-system-manage/statistics'],
                            ['label' => '文件查看', 'icon' => 'fa fa-circle-o', 'url' => '/quality-system-manage/index'],
                        ],

                    ],
                    [
                        'label' => '系统版本',
                        'icon' => 'fa fa-qrcode',
                        'url' => '/version/index',
                    ],
                ],
            ]
        ) ?>

    </section>

</aside>