<?php

namespace backend\components;

use Yii;
use yii\rbac\Rule;

class RoleViewRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'role_view_rule';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $id = isset($params['id']) ? $params['id'] : null;
        if (!$id) {
            return false;
        }

        $username = Yii::$app->user->identity->username;

        // 不是超级管理员不能查看超级管理员及普通管理员的权限
        if ($username != 'admin' && ($id == '超级管理员' || $id == '普通管理员')) {
                return false;
        }
        return true;
    }
}
