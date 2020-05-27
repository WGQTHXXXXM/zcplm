<?php

namespace frontend\components;

use yii\rbac\Rule;

//smart T是否通过的规则
class Project2Rule extends Rule
{
    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        if($params['id'] == 2)
            return true;
        return false;
    }
}
