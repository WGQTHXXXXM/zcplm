<?php

namespace frontend\components;

use yii\rbac\Rule;

//smart Q是否通过的规则
class ProjectM11Rule extends Rule
{
    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        
        if($params['id'] == 5)
            return true;
        return false;
    }
}
