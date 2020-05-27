<?php

namespace backend\components;

use Yii;
use yii\rbac\Rule;
use mdm\admin\models\User;

class UserRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'user_rule';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $id = isset($params['id']) ? $params['id'] : null;
        if ($id) {
            $model = User::findOne($id);
            if (!$model) {
                return false;
            }

            $username = Yii::$app->user->identity->username;

            // 不是超级管理员不能删除超级管理员(admin)用户
            if ($username != 'admin' && $model['username'] == 'admin') {
                return false;
            }
        }

        return true;
    }
}
