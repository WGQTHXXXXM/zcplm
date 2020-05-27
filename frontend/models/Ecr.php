<?php

namespace frontend\models;

use common\components\CommonFunc;
use Yii;

/**
 * This is the model class for table "ecr".
 *
 * @property integer $id
 * @property string $serial_number
 * @property string $reason
 * @property string $detail
 * @property string $module
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $effect_range
 */
class Ecr extends EcrAndEcn
{
    //审批状态
    //const STATUS_ECR = [0=>'待提交',1=>'审核中',2=>'被退回',3=>'已通过'];

    //上传的文件
    public $uploadFile;

    //ECN的参数
    public $change_now,$affect_stock,$remark;
    //
    public $projectName,$projectProcessName,$bom;


    //任务名称
    const ECR_CREATE1 = '新增ECR一审';
    const ECR_CREATE2 = '新增ECR二审';
    const ECR_CREATE3 = '新增ECR三审';
    const ECR_CREATE4 = '新增ECR四审';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecr';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $arr = parent::rules();
        return array_merge($arr,[
            [['serial_number','reason','detail','bom_id','project_process_id','created_at',
                'updated_at','project_id','change_now','affect_stock','approver1','approver2','approver4dcc'],'required'],
            [['reason', 'detail'], 'string'],
            [['created_at', 'updated_at','bom_id','project_id','project_process_id'], 'integer'],
            [['serial_number'], 'string', 'max' => 20],
            [['uploadFile'], 'file','maxFiles' => 5],
            [['uploadFile'], 'safe']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $arr = parent::attributeLabels();
        return array_merge($arr,[
            'id' => Yii::t('material', 'ID'),
            'serial_number' => Yii::t('material', '编号'),
            'reason' => Yii::t('material', '变更背景'),
            'detail' => Yii::t('material', '变更内容'),
            'effect_range'=>'变更影响范围',
            'bom_id' => Yii::t('material', '机种智车料号'),
            'project_process_id' => Yii::t('material', '项目阶段'),
            'created_at' => Yii::t('material', '创建时间'),
            'updated_at' => Yii::t('material', '更新时间'),
            'project_id'=>'项目名称',
            'user'=>'创建者',
            'status'=>'审批情况',
            //index页面查询用
            'bom' => Yii::t('material', '机种智车料号'),
            'projectProcessName' => Yii::t('material', '项目阶段'),
            'projectName' => Yii::t('material', '项目名称'),
            //ECN
            'change_now'=>'是否立即变更',
            'affect_stock'=>'是否影响库存产品',
            'remark'=>'备注',
        ]);
    }

    //以下变量为了联合审批人表用,type=2的都是ECR的;
    public $type = [Tasks::TASK_TYPE_ECR1,Tasks::TASK_TYPE_ECR3,Tasks::TASK_TYPE_ECR2,Tasks::TASK_TYPE_ECR4];

    //关联附件表
    public function getAttachments()
    {
        return $this->hasMany(EcrAttachment::className(),['ecr_id'=>'id']);
    }

    //////////////////////////////////////////////////////////////////////
    /**3.4=9=5.3=1.6
     * 在表格里显示变更原因
     */
    public function getCutReason()
    {
        return $this->reason? (mb_strlen($this->reason)<=10? $this->reason : mb_substr($this->reason,0,10).'...') : '';
    }

    /**
     * 在表格里显示变更内容
     */
    public function getCutDetail()
    {
        return $this->detail? (mb_strlen($this->detail)<=10? $this->detail : mb_substr($this->detail,0,10).'...') : '';
    }


    /////////////////////关联表/////////////////////////////////////////////////
    public function getEcn()
    {
        return $this->hasOne(Ecn::className(),['ecr_id'=>'id']);
    }

    public function getProjectProcess()
    {
        return $this->hasOne(ProjectProcess::className(),['id'=>'project_process_id']);
    }

    public function getProjects()
    {
        return $this->hasOne(Projects::className(),['id'=>'project_id']);
    }

    public function getBoms()
    {
        return $this->hasOne(Materials::className(),['material_id'=>'bom_id']);
    }

    /*
     * 任务通过的处理
     */
    public function doPassTask($mdlTask,$mdlUserTask=null)
    {
        $_POST['taskCommit']=1;//是否立即提交
        $_POST['taskRemark']='';//备注
        $type = Tasks::TASK_TYPE_ECR4;//任务类型
        if($mdlTask->type == Tasks::TASK_TYPE_ECR1)
            $type = Tasks::TASK_TYPE_ECR2;
        else if($mdlTask->type == Tasks::TASK_TYPE_ECR2)
            $type = Tasks::TASK_TYPE_ECR3;
        else if($mdlTask->type == Tasks::TASK_TYPE_ECR3)
            $type = Tasks::TASK_TYPE_ECR4;
        else if($mdlTask->type == Tasks::TASK_TYPE_ECR4)//任务完成
        {
            if(!ExtBomsParent::approveEcn())
                return ['status'=>false,'msg'=>'改变ECN表时出错'];
            Yii::$app->getSession()->setFlash('success', "任务通过");
            $strCode = $this->serial_number;
            //保存成功后提交
            $strAddr = $mdlTask->user->email;
            CommonFunc::sendMail(CommonFunc::APPROVE_PASS,$strAddr,$mdlTask->name,$strCode,'tasks/index');
            return ['status'=>true,'msg'=>"审批成功，任务已经通过"];
        }
        //任务名称
        if($mdlTask->name == self::ECR_CREATE1)
            $mdlTask->name = self::ECR_CREATE2;
        else if($mdlTask->name == self::ECR_CREATE2)
            $mdlTask->name = self::ECR_CREATE3;
        else if($mdlTask->name == self::ECR_CREATE3)
            $mdlTask->name = self::ECR_CREATE4;


        $approvers=Tasks::getApprovers($type,$mdlTask->type_id);
        if(!UserTask::GenerateUserTask($approvers['approvers'],$mdlTask->id))
            return ['status'=>false,'msg'=>'保存审批表时出错，请找下管理员2'];

        $mdlTask->type = $type;
        $mdlTask->status = Tasks::STATUS_COMMITED;
        if(!$mdlTask->save())
            return ['status'=>false,'msg'=>'保存审批表时出错，请找下管理员2'];;
        Yii::$app->getSession()->setFlash('success', "审批成功");
        CommonFunc::sendMail(CommonFunc::APPROVE_NOTICE,$approvers['mail'],$mdlTask->name,
            $approvers['code'],'user-task/index',$mdlTask->user->username);

        return ['status'=>true,'msg'=>"审批成功，任务进到下一级"];
    }

    /*
     * ECR里的ECN的保存
     */
    public function saveEcn($EcnId = null)
    {
        if(!isset($_SESSION['ecnAttachment']))
            return true;
        $isSuc = true;//标志着是否保存成功
        if($EcnId == null)//新建
            $model = new Ecn();
        else//更新
            $model = Ecn::findOne(['ecr_id'=>$EcnId]);

        $time = time();//当前时间截
        $model->addAutoData($this->id);
        $model->change_now = $_POST['Ecr']['change_now'];
        $model->affect_stock = $_POST['Ecr']['affect_stock'];
        $model->remark = $_POST['Ecr']['remark'];
        if(!$model->save())
            return false;

        $objChangeSet = $_SESSION['ecnAttachment'];
        $arrBomParentId = [];
        //重新整理ECN变更数据，看是不是PCBA，PCBA变更有没有PCB变化
        $mtrPcb = '';//看是不是更换的pcb,如果为空就不是，如果不为空就是pcb
        foreach ($objChangeSet as $key1=>$value1)
        {
            $mtr = Materials::find()->where(['zc_part_number'=>$key1])->asArray()->one();
            $arrBomParentId[] = $mtr;
            foreach ($value1 as $key2=>$value2)
            {
                $tempMtr = Materials::findOne(['zc_part_number'=>trim($value2['zcNo'])]);
                if($tempMtr->part_type == 1093)//如果类型是pcb
                    $mtrPcb = trim($value2['zcNo']);
                $objChangeSet[$key1][$key2]['mtr_id'] = $tempMtr->material_id;
            }
        }
        //上面（BOM物料存在）如果过了,要查出每个BOM的料
        foreach ($arrBomParentId as $pMtr)
        {
            //找到要变更的BOM
            $mdlParent = ExtBomsParent::findOne(['real_material'=>$pMtr['material_id']]);
            //新建上面所有的BOM物料，并建立好联接。
            $cache = [];
            $newpMtr = $mdlParent->generateUpBomMtr($model->id,true,$mtrPcb,$cache);
            if(!is_object($newpMtr))
                $isSuc = false;
            //新建本ECN的boms_parent
            $newPbom = new BomsParent();
            if($isSuc){
                $newPbom->parent_id = $mdlParent->parent_id;
                $newPbom->parent_version = intval($mdlParent->parent_version)+1;
                $newPbom->status = ExtBomsParent::STATUS_UNRELEASE;
                $newPbom->pv_effect_date = ExtBomsParent::EXPIRE_DATE_TEMPORARY;
                $newPbom->pv_expire_date = ExtBomsParent::EXPIRE_DATE_MAX;
                $newPbom->type = ExtBomsParent::BOM_TYPE_TRIAL;
                $newPbom->creater_id = Yii::$app->user->id;
                $newPbom->created_at = $newPbom->updated_at = $time;
                $newPbom->real_material = $newpMtr->material_id;
                if(!$newPbom->save())
                    $isSuc = false;
            }
            if($isSuc){
                $mdlParent->pv_expire_date = ExtBomsParent::EXPIRE_DATE_TEMPORARY;
                if(!$mdlParent->save())
                    $isSuc = false;
            }
            if(!$isSuc)
                break;
            //存ecn_pbom_attachment
            $mdlEcnPbomAttachment = new EcnPbomAttachment();
            $mdlEcnPbomAttachment->ecn_id = $model->id;
            $mdlEcnPbomAttachment->pbom_id = $newPbom->id;
            if(!$mdlEcnPbomAttachment->save())
                $isSuc = false;

            //存boms_child,ECN的主要变更
            foreach ($objChangeSet[$pMtr['zc_part_number']] as $infoChange)
            {
                if($isSuc == false)//如果不成功就跳出
                    break;
                $arrBomsChild=[];
                if($infoChange['qtyOld']==0){//增加
                    $arrBomsChild = [
                        'boms_parent_id'=>$newPbom->id,
                        'child_id'=>$infoChange['mtr_id'],
                        'qty'=>$infoChange['qtyNew'],
                        'ref_no'=>$infoChange['addRef'],
                        'zc_part_number2_id'=> empty($infoChange['zcNo2'])?null:Materials::findOne(['zc_part_number' => $infoChange['zcNo2']])->material_id,
                        'ecn_id'=>$model->id
                    ];
                    $newcBom = new ExtBomsChild();
                    $isSuc = $newcBom->generateSelfByEcn($arrBomsChild);
                } else if ($infoChange['qtyNew'] == 0) {//删除

                    $tempMdlBomsChil = BomsChild::find()->where(['boms_parent.parent_id'=>$mdlParent->parent_id])
                        ->andWhere('boms_child.bom_expire_date='.ExtBomsParent::EXPIRE_DATE_MAX)
                        ->andWhere(['boms_child.child_id'=>$infoChange['mtr_id']])
                        ->leftJoin('boms_parent','boms_child.boms_parent_id=boms_parent.id')->one();

                    $tempMdlBomsChil->bom_expire_date = ExtBomsParent::EXPIRE_DATE_TEMPORARY;
                    if(!$tempMdlBomsChil->save())
                        $isSuc = false;

                } else {//替换二供或变数量位置
                    //变更数量和位置
                    $tempMdlBomsChil = BomsChild::find()->where(['boms_parent.parent_id' => $mdlParent->parent_id])
                        ->andWhere('boms_child.bom_expire_date=' . ExtBomsParent::EXPIRE_DATE_MAX)
                        ->andWhere(['boms_child.child_id' => $infoChange['mtr_id']])
                        ->leftJoin('boms_parent', 'boms_child.boms_parent_id=boms_parent.id')->one();
                    //结构电子都要变的地方
                    $arrBomsChild['qty'] = floatval($infoChange['qtyNew']);
                    $arrBomsChild['ref_no'] = $tempMdlBomsChil->ref_no;
                    if (trim($tempMdlBomsChil->ref_no) != '') {//电子料，要考虑位置的变化
                        $subRef = empty($infoChange['subRef']) ? '' : trim($infoChange['subRef']);
                        $addRef = empty($infoChange['addRef']) ? '' : trim($infoChange['addRef']);
                        if ($addRef != $subRef) {//如果位置不一样，说明有替换料
                            $subRef = str_replace('，', ',', $subRef);
                            $arrSubRef = empty(trim($subRef)) ? [] : explode(',', $subRef);
                            $addRef = str_replace('，', ',', $addRef);
                            $arrAddRef = empty(trim($addRef)) ? [] : explode(',', $addRef);

                            //看减掉的料是否在原位置上
                            $arrDataRefNo = explode(',', $tempMdlBomsChil->ref_no);
                            foreach ($arrSubRef as $ref) {
                                $arrDataRefNo = array_diff($arrDataRefNo, [$ref]);
                            }
                            foreach ($arrAddRef as $ref) {
                                $arrDataRefNo[] = $ref;
                            }
                            natsort($arrDataRefNo);
                            $arrBomsChild['ref_no'] = implode(',', $arrDataRefNo);
                        }
                    }
                    //变更二供
                    $arrBomsChild['zc_part_number2_id'] = $tempMdlBomsChil->zc_part_number2_id;
                    if (!empty(trim($infoChange['zcNo2']))) {//只有不为空时才对二供处理处理
                        if ($tempMdlBomsChil->zc_part_number2_id != trim($infoChange['zcNo2'])) {

                            $tempMtrId = Materials::findOne(['zc_part_number' => $infoChange['zcNo2']])->material_id;
                            if ($tempMdlBomsChil->zc_part_number2_id != $tempMtrId)
                                $arrBomsChild['zc_part_number2_id'] = $tempMtrId;
                        }
                    }

                    $arrBomsChild['boms_parent_id']=$newPbom->id;
                    $arrBomsChild['child_id']=$infoChange['mtr_id'];
                    $arrBomsChild['ecn_id']=$model->id;

                    $newcBom = new ExtBomsChild();
                    $isSuc = $newcBom->generateSelfByEcn($arrBomsChild);
                    //上个版本减一天
                    $tempMdlBomsChil->bom_expire_date = ExtBomsParent::EXPIRE_DATE_TEMPORARY;
                    if(!$tempMdlBomsChil->save())
                        $isSuc = false;
                }
            }
        }
        if($isSuc){//上传附件
            $mdlEcnAttach = new EcnAttachment();
            if(!$mdlEcnAttach->saveAttachment($model->id))
                $isSuc = false;
        }
        return $isSuc;
    }


}
