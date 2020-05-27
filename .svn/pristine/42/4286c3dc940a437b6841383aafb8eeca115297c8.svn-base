<?php

namespace backend\components;

use Yii;
use yii\rbac\Rule;
use mdm\admin\models\User;

class AssignmentRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'assignment_rule';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $id = isset($params['id']) ? $params['id'] : null;
        if (!$id) {
            return false;
        }

        $model = User::findOne($id);
        if (!$model) {
            return false;
        }

        $username = Yii::$app->user->identity->username;
    //    $role = Yii::$app->user->identity->role;
    //    $role = Yii::$app->getAuthManager()->getRole($username);

        // 不是超级管理员
        if ($username != 'admin') {
            // 不能给超级管理员分配权限
            if ($model->username == 'admin') {
                return false;
            }

            // 不能给任何人分配超级管理员权限
            $select_items = Yii::$app->getRequest()->post('items', []);
            foreach ($select_items as $select_item) {
                if ('超级管理员' === $select_item) {
                    return false;
                }
            }
        }

        return true;
    }
}
