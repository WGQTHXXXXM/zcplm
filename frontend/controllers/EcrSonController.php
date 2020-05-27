<?php

namespace frontend\controllers;

use frontend\models\ProjectProcessTemplate;


class EcrSonController extends EcrController
{
    /*
     * ecr影响范围设置
     */
    public function actionSetEffectRange()
    {
        return $this->render('/ecr/set-effect-range',['data'=>json_encode($this->getDataEffectRange())]);
    }


}
