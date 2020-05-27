<?php
namespace frontend\models;

use common\components\CommonFunc;
use Yii;

abstract class TaskAbstract extends \yii\db\ActiveRecord
{

    protected $nameTask,$typeTask;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->initMdl();
    }

    //每个任务通过后的处理
    abstract public function doPass();

    //得到任务名
    abstract public function getTaskMail();

    //构造时调用的函数
    abstract public function initMdl();


    //创建任务
    protected function createTask()
    {
        // TODO: Implement createTask() method.
        $status = $_POST['taskCommit'];//是否立即提交
        $remark = $_POST['taskRemark'];//备注
        $Author = Yii::$app->user->id;//创建者

        $task = new Tasks();
        $task->name = $this->nameTask;
        $task->status=Tasks::STATUS_COMMITED;
        $task->remark = $remark;
        $task->type = $this->typeTask;
        $task->type_id = $this->id;
        $task->date = time();
        $task->user_id = $Author;
        //如果任务保存成功再生成用户任务表
        if($task->save())
        {
            if(empty($_POST[substr(strrchr($this->classname(),'\\'),1)]['departLvl'])){//如果没有设定审批
                return false;
            }
            //得到审批人                  当前类名
            $dataPostApprover = $_POST[substr(strrchr($this->classname(),'\\'),1)]['departLvl'];
            foreach ($dataPostApprover as $key=>$value){
                $able = 0;
                if($key == 1)
                    $able=1;
                if(!UserTask::GenerateUserTask($value,$task->id,$able,$key))
                    return false;
            }

            //发邮件
            $arrApprovers = Tasks::getApprovers($this->typeTask,$task->id);
            CommonFunc::sendMail(CommonFunc::APPROVE_NOTICE,$arrApprovers['mail'],$task->name,
                $arrApprovers['code'],'user-task/index');

            return true;
        }
        else
            return false;

    }

    //被退回的任务更新
    protected function updateTask(Tasks $mdlTask)
    {
        // TODO: Implement updateTask() method.
        //得到审批人
        UserTask::deleteAll(['task_id'=>$mdlTask->id]);
        $dataPostApprover = $_POST[substr(strrchr($this->classname(),'\\'),1)]['departLvl'];
        foreach ($dataPostApprover as $key=>$value){
            $able = 0;
            if($key == 1)
                $able=1;
            if(!UserTask::GenerateUserTask($value,$mdlTask->id,$able,$key))
                return false;
        }

        //发邮件
        $arrApprovers = Tasks::getApprovers($this->typeTask,$mdlTask->id);
        CommonFunc::sendMail(CommonFunc::APPROVE_NOTICE,$arrApprovers['mail'],$mdlTask->name,
            $arrApprovers['code'],'user-task/index');


        $mdlTask->status = Tasks::STATUS_COMMITED;
        $mdlTask->remark = $_POST['taskRemark'];
        if($mdlTask->save()){
            return true;
        }
        return false;

    }

    //任务通过的处理
    protected function doPassTask(Tasks $mdlTask,UserTask $mdlUserTask=null)
    {
        $strCode = $this->getTaskMail();
        $noApproveTasks = UserTask::find()->where(['task_id'=>$mdlUserTask->task_id,'lvl'=>$mdlUserTask->lvl+1])->all();
        if(!empty($noApproveTasks)){//根据是否有下一级审批人来判断任务是否通过
            $mdlTask->status = Tasks::STATUS_COMMITED;
            if($mdlTask->save()){
                //把下一级的审批人设置成可以审批状态
                if(!UserTask::updateAll(['approve_able'=>1,'created_at'=>time()],['task_id'=>$mdlUserTask->task_id,'lvl'=>$mdlUserTask->lvl+1]))
                    return ['status'=>false,'msg'=>'保存附件表时出错3'];
                //发信
                $mail = UserTask::find()->leftJoin('user','user.id=user_task.user_id')
                    ->where(['user_task.task_id'=>$mdlUserTask->task_id,'user_task.lvl'=>$mdlUserTask->lvl+1])
                    ->select('user.email')->column();
                CommonFunc::sendMail(CommonFunc::APPROVE_NOTICE,$mail,$mdlTask->name,
                    $strCode,'user-task/index',$mdlTask->user->username);
                return ['status'=>true,'msg'=>"审批成功，任务已经通过"];
            }
            return ['status'=>false,'msg'=>'保存附件表时出错1'];
        }

        if(!$this->doPass())
            return ['status'=>false,'msg'=>'保存附件表时出错2'];
        //保存成功后提交
        $strAddr = $mdlTask->user->email;
        CommonFunc::sendMail(CommonFunc::APPROVE_PASS,$strAddr,$mdlTask->name,$strCode,'tasks/index');
        return ['status'=>true,'msg'=>"审批成功，任务已经通过"];
    }

}