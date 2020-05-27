<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "projects".
 *
 * @property string $id
 * @property string $name
 * @property integer $created_at
 * @property integer $end_at
 * @property integer $status
 * @property integer $precent
 * @property integer $working
 */
class Projects extends \yii\db\ActiveRecord
{
    const STATUS_PROJECT = [0=>'暂停',1=>'在研',2=>'量产'];
    const STATUS_STOP = 0;
    const STATUS_START = 1;
    const STATUS_COMPLETE = 2;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'projects';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'end_at', 'status', 'precent','working'], 'required'],
            [['status', 'precent'], 'integer'],
            [['name'], 'string', 'max' => 40],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('material', 'ID'),
            'name' => Yii::t('material', '项目名'),
            'created_at' => Yii::t('material', '项目启动时间'),
            'end_at' => Yii::t('material', '项目完成时间'),
            'status' => Yii::t('material', '项目状态'),
            'precent' => Yii::t('material', '项目进度'),
        ];
    }

    static public function figureWorking($root)
    {
        //把所有阶段找出来
        $mdlProjectProcess = ProjectProcess::find()->where(['root'=>$root,'lvl'=>1])->orderBy('lft')->
            select('lft,rgt,id')->all();
        //看每个阶段是否都过了
        foreach ($mdlProjectProcess as $val)
        {
            $mdlP = ProjectProcess::find()->select('project_process.id,min(tasks.status) as status')
                ->leftJoin('tasks','tasks.type_id=project_process.id and tasks.type=5')
                ->where('project_process.root='.$root.' and project_process.lvl=3')
                ->andWhere('project_process.lft>'.$val->lft.' and project_process.rgt<'.$val->rgt)
                ->groupBy('project_process.id')->asArray()->all();
            foreach ($mdlP as $val1)
            {
                if($val1['status']!=3)//如果有一个没有审批通过，说明就到这个阶段
                    return $val->id;
            }
        }
        return end($mdlProjectProcess)->id;//到这里说明这个项目已经完成
    }


    //////////////关联表///////////////////
    public function getProcess()
    {
        return $this->hasOne(ProjectProcess::className(),['id'=>'working']);
    }


}
