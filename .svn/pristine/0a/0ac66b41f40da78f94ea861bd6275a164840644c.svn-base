<?php

namespace frontend\models;

use backend\models\Department;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "quality_system_manage".
 *
 * @property integer $id
 * @property string $parent_name
 * @property string $name
 * @property string $son_name
 * @property integer $department_belong_id
 * @property string $file_code
 * @property integer $file_class
 * @property integer $status_submit
 * @property integer $visible
 * @property integer $updated_at
 */
class QualitySystemManage extends \yii\db\ActiveRecord
{
    //文件分类
    const FILE_CLASS = [2=>'二级文件',3=>'三级文件',4=>'四级文件'];
    //文件状态
    const FILE_STATUS =[0=>'需提交',1=>'审批中',2=>'定版'];
    const FILE_STATUS_NEED=0;
    const FILE_STATUS_APPROVE=1;
    const FILE_STATUS_SURE=2;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'quality_system_manage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','department_belong_id', 'file_code', 'file_class', 'status_submit', 'visible','updated_at'], 'required'],
            [['department_belong_id', 'file_class', 'status_submit', 'visible','updated_at'], 'integer'],
            [['parent_name', 'son_name', 'file_code','name',], 'string', 'max' => 50],
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
            'name' => Yii::t('material', '文件名'),
            'parent_name' => Yii::t('material', '主过程名称'),
            'son_name' => Yii::t('material', '子过程名称'),
            'department_belong_id' => Yii::t('material', '归属部门'),
            'file_code' => Yii::t('material', '文件编码'),
            'file_class' => Yii::t('material', '文件分类'),
            'status_submit' => Yii::t('material', '文件状态'),
            'visible' => Yii::t('material', '首页计算'),
            'updated_at' => Yii::t('material', '更新时间'),
        ];
    }

    /**
     * 反回文件所属部门名字
     */
    public function getBelongDepart()
    {
        return $this->hasOne(Department::className(),['id'=>'department_belong_id']);
    }

    /**
     * 上传了的最大版本
     */
    public function getMaxVersion()
    {
        $ver = QsmAttachment::find()->where(['qsm_id'=>$this->id])->select('max(version) as version')->one();

        return $ver->version;
    }

    /**
     * 返回这个质量文件审批所有部门和部门内的人
     */
    public function getApproverDepartment()
    {
        $arrDepart = QsmApprover::find()->alias('qsmApp')->leftJoin(['dpt'=>'department'],'dpt.id=qsmApp.department_id')
            ->leftJoin('department_user as dptUser','dptUser.department_id=dpt.id')
            ->leftJoin('user','user.id=dptUser.user_id')
            ->where(['qsmApp.qsm_id'=>$this->id])->select('dpt.name,user.username,qsmApp.lvl,user.id')->asArray()->all();
        $department=[];
        foreach ($arrDepart as $value){
            if($value['id'] == Yii::$app->user->id)
                continue;
            $department[$value['lvl']][$value['name']][$value['id']] = $value['username'];
        }

        return $department;

    }

    /**
     * 总进度
     */
    public function allProcess()
    {
        $mu = QualitySystemManage::find()->alias('qsm')->select('qsmA.qsm_id,qsm.id')
            ->leftJoin(['qsmA'=>'qsm_attachment'],'qsm.id=qsmA.qsm_id')->groupBy('qsm.id')->count();
        $zi = QualitySystemManage::find()->alias('qsm')->select('qsmA.qsm_id,qsm.id')
            ->leftJoin(['qsmA'=>'qsm_attachment'],'qsm.id=qsmA.qsm_id')->groupBy('qsm.id')->having('qsmA.qsm_id is not null')
            ->count();
        if($mu==0)//没有数据时
            return 0;
        return intval(($zi/$mu)*100);
    }


    /**
     * 返回总览看板按文件分类的统计
     */
    public function fileClassCount()
    {
        $muAll = QualitySystemManage::find()->select('count(file_class) as mu,file_class')->groupBy('file_class')
            ->orderBy(['file_class'=>SORT_ASC])->indexBy('file_class')->asArray()->all();

        if(empty($muAll))//没有数据时
            return new ArrayDataProvider(['allModels'=>[]]);

        $arrTemp = array_column($muAll,'file_class');
        $fileClassAll = implode(',',$arrTemp);
        //上传总数
        $ziAll = QualitySystemManage::find()->alias('qsm')->leftJoin(['qsmA'=>'qsm_attachment'],'qsm.id=qsmA.qsm_id')
            ->select('count(qsm.file_class) as zi,qsm.file_class')->groupBy('qsm.file_class')
            ->where('qsmA.id is null and qsm.file_class in ('.$fileClassAll.')')
            ->orderBy(['qsm.file_class'=>SORT_ASC])->indexBy('file_class')->asArray()->all();

        //上传后通过的总数
        $ziPassAll = QualitySystemManage::find()->alias('qsm')->leftJoin(['qsmA'=>'qsm_attachment'],'qsm.id=qsmA.qsm_id')
            ->select('count(qsm.file_class) as zi,qsm.file_class')->groupBy('qsm.file_class')
            ->where('qsm.file_class in ('.$fileClassAll.') and (qsmA.id is null or qsmA.status=0)')
            ->orderBy(['qsm.file_class'=>SORT_ASC])->indexBy('file_class')->asArray()->all();


        foreach ($muAll as $key=>$value){
            if(empty($ziAll[$key]['zi'])){
                $zi = 0;
                $ziPass = 0;
            } else{
                $zi = $ziAll[$key]['zi'];
                $ziPass = $ziPassAll[$key]['zi'];
            }
            $muAll[$key]['zi'] = $zi;
            $muAll[$key]['ziPass'] = $ziPass;
        }


        return new ArrayDataProvider(['allModels'=>$muAll]);
    }

    /**
     * 返回总览看板按部门分类的统计
     */
    public function departmentCount()
    {
        $muAll = QualitySystemManage::find()->alias('qsm')->leftJoin(['dpt'=>'department'],'dpt.id=qsm.department_belong_id')
            ->select('count(department_belong_id) as mu,department_belong_id,dpt.name as dptName')
            ->groupBy('qsm.department_belong_id')->asArray()->all();
        if(empty($muAll))//没有数据时
            return new ArrayDataProvider(['allModels'=>[]]);

        $tblTemp1 = QsmAttachment::find()->select('max(version) as version,qsm_id')->groupBy('qsm_id');
        $tblTemp2 = QsmAttachment::find()->alias('tblTemp2')
            ->innerJoin(['tblTemp1'=>$tblTemp1],'tblTemp1.version=tblTemp2.version and tblTemp1.qsm_id=tblTemp2.qsm_id');

        $tbla = QualitySystemManage::find()->alias('qsm')->select('qsmA.qsm_id,qsm.department_belong_id,qsmA.status')
            ->leftJoin(['qsmA'=>$tblTemp2],'qsm.id=qsmA.qsm_id')->groupBy('qsm.id');

        $ziAll = (new Query())->from(['tbla'=>$tbla])
            ->select('count(tbla.department_belong_id) as zi,tbla.qsm_id,tbla.department_belong_id as dptid')
            ->groupBy('tbla.department_belong_id')->where('tbla.qsm_id is not null')
            ->indexBy('dptid')->column();
        $ziPassAll = (new Query())->from(['tbla'=>$tbla])
            ->select('count(tbla.department_belong_id) as zi,tbla.qsm_id,tbla.department_belong_id as dptid')
            ->groupBy('tbla.department_belong_id')->where('tbla.status=1')
            ->indexBy('dptid')->column();

        foreach ($muAll as $key=>$value){
            $zi = 0;
            $ziPass = 0;
            if(isset($ziAll[$value['department_belong_id']])){
                $zi = $ziAll[$value['department_belong_id']];
            }
            if(isset($ziPassAll[$value['department_belong_id']])){
                $ziPass = $ziPassAll[$value['department_belong_id']];
            }

            $muAll[$key]['zi'] = $zi;
            $muAll[$key]['ziPass'] = $ziPass;
        }

        return new ArrayDataProvider(['allModels'=>$muAll]);
    }

    /*
     * 返回总览看板中的部门执行效率数据
     */
    public function EfficiencyCount()
    {
        $muAll = QualitySystemManage::find()->alias('qsm')->leftJoin(['dpt'=>'department'],'dpt.id=qsm.department_belong_id')
            ->select('count(department_belong_id) as mu,department_belong_id,dpt.name as dptName')
            ->groupBy('qsm.department_belong_id')->asArray()->all();
        if(empty($muAll))//没有数据时
            return new ArrayDataProvider(['allModels'=>[]]);


        $tblTemp1 = QsmAttachment::find()->select('max(version) as version,qsm_id')->groupBy('qsm_id');
        $tblTemp2 = QsmAttachment::find()->alias('tblTemp2')
            ->innerJoin(['tblTemp1'=>$tblTemp1],'tblTemp1.version=tblTemp2.version and tblTemp1.qsm_id=tblTemp2.qsm_id');

        $tbla = QualitySystemManage::find()->alias('qsm')->leftJoin(['qsmA'=>$tblTemp2],'qsm.id=qsmA.qsm_id')
            ->select('(qsmA.updated_at-qsm.updated_at) as udate,qsmA.qsm_id,qsm.department_belong_id,qsmA.status')
            ->groupBy('qsm.id');

        $ziAll = (new Query())->from(['tbla'=>$tbla])
            ->select('sum(udate) as adate,count(department_belong_id) as num,qsm_id,department_belong_id as dptid')
            ->groupBy('department_belong_id')->where('status=1')
            ->indexBy('dptid')->all();

        foreach ($muAll as $key=>$value){
            $num = 0;
            $adate = 0;
            if(isset($ziAll[$value['department_belong_id']])){
                $num = $ziAll[$value['department_belong_id']]['num'];
                $adate = $ziAll[$value['department_belong_id']]['adate'];
            }
            $muAll[$key]['num'] = $num;
            $muAll[$key]['adate'] = $adate;
        }

        return new ArrayDataProvider(['allModels'=>$muAll]);
    }

}
