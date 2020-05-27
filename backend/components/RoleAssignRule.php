<?php

namespace backend\components;

use Yii;
use yii\rbac\Rule;

class RoleAssignRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'role_assign_rule';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $id = isset($params['id']) ? $params['id'] : null;
        if (!$id) {
            return false;
        }

        $manager = Yii::$app->getAuthManager();
        $assignments = $manager->getAssignments($user);

        // 超级管理员有权分配任何权限
        if (array_key_exists('超级管理员', $assignments)) {
            return true;
        }

        // 普通管理员有权分配除'超级管理员'及以'/'开头以外的权限
        if (array_key_exists('普通管理员', $assignments)) {
            $select_items = Yii::$app->getRequest()->post('items', []);
            foreach ($select_items as $select_item) {
                if ('超级管理员' === $select_item || strpos($select_item, '/') === 0) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }
}
