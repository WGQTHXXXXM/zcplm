<?php

namespace console\controllers;

use frontend\models\Tasks;
use frontend\models\UserTask;
use yii\console\Controller;
use Yii;

/**
 * EcrController implements the CRUD actions for Ecr model.
 */
class AutoSendEmailController extends Controller
{

    /**自动催发没审批的人。
     * Lists all Ecr models.
     *
     */
    public function actionAutoSend()
    {
        $arrEmail = UserTask::find()->leftJoin('tasks','tasks.id=user_task.task_id')
            ->leftJoin('user','user_task.user_id=user.id')
            ->where(['user_task.status'=>UserTask::STATUS_UNAPPROVE,'tasks.status'=>Tasks::STATUS_COMMITED,'user_task.approve_able'=>1])
            ->select('user.email')->column();
        $objCompose = Yii::$app->mailer->compose(['html' => 'push']);        //发送
        $objCompose->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($arrEmail)->setSubject('通知——来自' . Yii::$app->name)->send();

    }

    /*
     * 自动催发任务不进行下一步的人。
     */
    public function actionTaskRejected()
    {
        $arrEmail = Tasks::find()->leftJoin('user','tasks.user_id=user.id')
            ->where(['tasks.status'=>Tasks::STATUS_REJECTED])->select('user.email')->column();
        $objCompose = Yii::$app->mailer->compose(['html' => 'pushTask']);        //发送
        $objCompose->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($arrEmail)->setSubject('通知——来自' . Yii::$app->name)->send();
    }

}
