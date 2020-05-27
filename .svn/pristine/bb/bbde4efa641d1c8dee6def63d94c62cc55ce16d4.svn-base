<?php

namespace frontend\models;

use common\models\User;

class ProjectProcess extends \kartik\tree\models\Tree
{
    //表格显示时需要的
    public $pid;//父id，分组时用
    public $taskStatus;//任务状态
    public $taskRemark;//任务备注
    public $ctime;//创建时间
    public $utime;//更新时间
    public $submitter;//上传人

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_process';
    }

    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    /////////////关联表///////////////////
    public function getFile()
    {
        return $this->hasOne(ProjectAttachment::className(), ['file_id' => 'id']);
    }



    public function getPidname()
    {
        return $this->hasOne(ProjectProcess::className(),['id'=>'pid']);
    }

    /**
     * 当审批通过时计算项目完成的百分比
     */
    static public function figureComplatePre($id)
    {
        $root = ProjectProcess::findOne($id)->root;
        //找到这个项目的所有文件通过的个数
        $mdlFile = ProjectProcess::find()->select('project_process.id,min(tasks.status) as tstatus')
            ->leftJoin('tasks','tasks.type_id=project_process.id and tasks.type=5')
            ->where('project_process.root='.$root.' and project_process.lvl=3')
            ->groupBy('project_process.id')->asArray()->all();
        //计算百分比,四舍五入后取整
        $fenzi=0;
        $fenmu=0;
        foreach ($mdlFile as $val)
        {
            $fenmu++;
            if($val['tstatus'] == 3)
            {
                $fenzi++;
            }
        }
        $model = Projects::findOne($root);

        $working = Projects::figureWorking($root);//计算到哪个阶段
        $model->working = $working;
        $model->precent = intval(($fenzi/$fenmu)*100+0.5);
        $model->save();

    }


}
