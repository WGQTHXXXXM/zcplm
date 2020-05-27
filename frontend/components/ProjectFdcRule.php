<?php

namespace frontend\components;

use yii\rbac\Rule;

//smart Q是否通过的规则
class ProjectFdcRule extends Rule
{
    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {

        if($params['id'] == 6)
            return true;
        return false;
    }
}
