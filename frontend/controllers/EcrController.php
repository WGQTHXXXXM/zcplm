<?php

namespace frontend\controllers;

use common\models\User;
use frontend\models\Approver;
use frontend\models\BomsParent;
use frontend\models\EcApproval;
use frontend\models\Materials;
use frontend\models\ProjectProcess;
use frontend\models\ProjectProcessTemplate;
use frontend\models\Projects;
use Yii;
use frontend\models\Ecr;
use frontend\models\EcrSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\components\CommonFunc;
use frontend\models\EcrAttachment;
use frontend\models\Tasks;
use frontend\models\UserTask;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/**
 * EcrController implements the CRUD actions for Ecr model.
 */
class EcrController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Ecr models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EcrSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ecr model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id,$type=[21,22,23,24],$idUserTask=-1)
    {
        //审批人
        $taskId = Tasks::find()->where(['type_id'=>$id])->andWhere(['in','type',$type])->one()->id;

        $arrUser = User::find()->select('username,id')->indexBy('id')->column();

        //这个ECR的审批人
        $mdlEcApproval = EcApproval::findOne(['ec_id'=>$id,'type'=>EcApproval::TYPE_ECR]);
        //第一级审批
        $mdlUTTemp = UserTask::find()->where(['task_id'=>$taskId,'user_id'=>$mdlEcApproval->approver1])->one();
        $arrData[] = [
            'pid'=>'部门内一级审批',
            'user_id' =>  $arrUser[$mdlEcApproval->approver1],
            'status' =>  empty($mdlUTTemp)?null:$mdlUTTemp->status,
            'remark'=>   empty($mdlUTTemp)?null:$mdlUTTemp->remark,
            'updated_at' =>  empty($mdlUTTemp)?null:$mdlUTTemp->updated_at,
            'created_at' =>  empty($mdlUTTemp)?null:$mdlUTTemp->created_at,
        ];
        //第二级审批
        $mdlUTTemp = UserTask::find()->where(['task_id'=>$taskId,'user_id'=>$mdlEcApproval->approver2])->one();
        $arrData[] = [
            'pid'=>'部门内二级审批',
            'user_id' =>  $arrUser[$mdlEcApproval->approver2],
            'status' =>  empty($mdlUTTemp)?null:$mdlUTTemp->status,
            'remark'=>   empty($mdlUTTemp)?null:$mdlUTTemp->remark,
            'updated_at' =>  empty($mdlUTTemp)?null:$mdlUTTemp->updated_at,
            'created_at' =>  empty($mdlUTTemp)?null:$mdlUTTemp->created_at,
        ];
        //第三级审批
        $mdlTemp = Approver::find()->where(['type'=>Approver::TYPE_LEADERS_ECR])->select('user_id')->indexBy('user_id')->column();
        unset($mdlTemp[$mdlEcApproval->approver1]);
        unset($mdlTemp[$mdlEcApproval->approver2]);
        unset($mdlTemp[$mdlEcApproval->approver4dcc]);
        $mdlUTTemp = UserTask::find()->where(['task_id'=>$taskId])->andWhere(['in','user_id',$mdlTemp])->all();
        if(empty($mdlUTTemp))
        {
            foreach ($mdlTemp as $val)
            {
                $arrData[] = [
                    'pid'=>'跨部门会签',
                    'user_id' =>  $arrUser[$val],
                    'status' =>  null,
                    'remark'=>   null,
                    'updated_at' =>  null ,
                    'created_at' =>  null ,
                ];
            }
        }
        else
        {

            foreach ($mdlUTTemp as $val)
            {
                $arrData[] = [
                    'pid'=>'跨部门会签',
                    'user_id' =>  $arrUser[$val->user_id],
                    'status' =>  $val->status,
                    'remark'=>   $val->remark,
                    'updated_at' =>  $val->updated_at,
                    'created_at' =>  $val->created_at,
                ];
            }
        }
        //第四级审批
        $mdlUTTemp = UserTask::find()->where(['task_id'=>$taskId,'user_id'=>$mdlEcApproval->approver4dcc])->one();
        $arrData[] = [
            'pid'=>'Dcc审批',
            'user_id' =>  $arrUser[$mdlEcApproval->approver4dcc],
            'status' =>  empty($mdlUTTemp)?null:$mdlUTTemp->status,
            'remark'=>   empty($mdlUTTemp)?null:$mdlUTTemp->remark,
            'updated_at' =>  empty($mdlUTTemp)?null:$mdlUTTemp->updated_at,
            'created_at' =>  empty($mdlUTTemp)?null:$mdlUTTemp->created_at,
        ];

        $dataProvider = new ArrayDataProvider([
            'allModels' =>  $arrData
        ]);


        //附件
        $dataAttachment = new ActiveDataProvider([
            'query' => EcrAttachment::find()->where(['ecr_id'=>$id])
        ]);
        $mdlUserTask = null;
        if($idUserTask != -1)
        {
            $mdlUserTask = UserTask::find()->leftJoin('tasks','user_task.task_id=tasks.id')
                ->select('*,user_task.status as userTaskStatus,tasks.status as taskStatus')
                ->where(['user_task.id'=>$idUserTask])->one();
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'dataAttachment'=>$dataAttachment,
            'mdlUserTask'=>$mdlUserTask
        ]);

    }

    /**
     * Creates a new Ecr model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Ecr();
        $model->addAutoData();//给模型添加上编号，时间，状态，用户等数据
        //存effect_range
        if(isset($_POST['taskCommit'])){
            $model->effect_range = implode(',',$_POST['Ecr']['effect_range']);
        }

        //开启事务
        $transaction = Yii::$app->db->beginTransaction();
        if ($model->load(Yii::$app->request->post()) && $model->save()&& $model->saveEcn())//保存界面
        {

            //存审批人到数据库和上传文件
            if($this->saveAttachments($model))
            {
                if($model->saveApprover())//保存审批人
                {
                    //提交任务//立即提交或稍后提交成功后就保存到数据库
                    if(Tasks::generateTask(Tasks::TASK_TYPE_ECR1,$model->id,Ecr::ECR_CREATE1))
                    {

                        //提交到数据库
                        $transaction->commit();
                        return $this->redirect(['/tasks/index']);
                    }
                }
                else
                {
                    echo '保存审批人失败';die;
                }
            }
            else
            {
                var_dump($model->getErrors());
                echo '保存附件失败';die;
            }

            $transaction->rollBack();
        }
        //物料数据
        $dataMtr['part_no'] = Materials::find()->select('zc_part_number,material_id')->indexBy('material_id')->column();
        $dataMtr['description'] = Materials::find()->select('part_name,material_id')->indexBy('material_id')->column();
        //项目名称
        $pjtName = Projects::find()->select('name,id')->indexBy('id')->column();

        //影响范围
        $arrEffectRangeOrg = ProjectProcessTemplate::find()->alias('ppt')->leftJoin('department as dpt','dpt.id=ppt.department_id')
            ->where(['lvl'=>3,'selected'=>1])->select('ppt.name,dpt.name as dname')->asArray()->all();
        $arrEffectRange=[];
        foreach ($arrEffectRangeOrg as $val){
            $arrEffectRange[$val['dname']][$val['name']]=$val['name'];
        }
        //创建界面
        return $this->render('create', [
            'model' => $model,
            'dataMtrDescription'=>json_encode($dataMtr['description']),
            'dataMtrPartNo'=>$dataMtr['part_no'],
            'pjtName'=>$pjtName,
            'dataUser'=>$model->getApprovers(),
            'arrEffectRange'=>$arrEffectRange
        ]);
    }

    /*
     * 通过项目得到该项目的阶段
     */
    public function actionGetProjectProcess()
    {
        $projectProcess = ProjectProcess::find()->where(['lvl'=>1,'project_id'=>$_POST['id']])->select('name,id')->all();
        $res = '';
        foreach ($projectProcess as $val)
        {
            $res .= '<option value="' . $val->id . '">'. $val->name . '</option>';
        }
        return $res;
    }


    /**
     * Updates an existing Ecr model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->addAutoData();//给模型添加上编号，时间，状态，用户等数据
        $model->addEcnDate();
        //开启事务
        $transaction = Yii::$app->db->beginTransaction();
        //存effect_range
        if(isset($_POST['taskCommit'])){
            $model->effect_range = implode(',',$_POST['Ecr']['effect_range']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()&& $model->saveEcn($model->id))
        {
            $isSuc = true;
            //更新附件
            if(!$this->saveAttachments($model))//更新附件
                $isSuc = false;
            if($isSuc&&$_POST['taskCommit'] == 0)//稍后提交
            {
                if(!$model->saveApprover(true))//保存审批人后结束
                    $isSuc = false;
                else{
                    $transaction->commit();
                    return $this->redirect(['/tasks/index']);
                }
            }
            else if($isSuc&&$_POST['taskCommit'] == 1)//立即提交:把以前的审批人审批的数据删掉,按重新选的审批人的数据分配
            {
                if(!$model->saveApprover(true))//保存审批人
                    $isSuc = false;
                if($isSuc)
                {
                    $taskId = $model->tasks->id;
                    UserTask::deleteAll(['task_id'=>$taskId]);
                    //把以前的二审三审变回到一审
                    $mdlTasks = Tasks::findOne($taskId);
                    $mdlTasks->name = Ecr::ECR_CREATE1;
                    $mdlTasks->status = Tasks::STATUS_COMMITED;
                    $mdlTasks->type = Tasks::TASK_TYPE_ECR1;
                    $mdlTasks->remark = $_POST['taskRemark'];
                    if(!$mdlTasks->save())
                        $isSuc = false;
                    //按更新后的审批人生成新的审批任务
                    if($isSuc&&!UserTask::GenerateUserTask([$model->approver1],$taskId))
                        $isSuc=false;
                }
                if($isSuc)
                {
                    $transaction->commit();
                    return $this->redirect(['/tasks/index']);
                }
            }
            Yii::$app->getSession()->setFlash('error', "更新失败");
            $transaction->rollBack();
        }
        //给uploadFile插件数据17000
        $ecrAttachments = EcrAttachment::findAll(['ecr_id'=>$model->id]);
        $preview = $previewCfg = [];
        foreach ($ecrAttachments as $att)
        {
            $preview[] = $att->path;
            $previewCfg[] =['caption' => $att->name, 'url'=>'/ecr/delete-attachment?id='.$att->id];
        }
        //物料数据
        $dataMtr['part_no'] = Materials::find()->select('zc_part_number,material_id')->indexBy('material_id')->column();
        $dataMtr['description'] = Materials::find()->select('description,material_id')->indexBy('material_id')->column();
        //项目名称
        $pjtName = Projects::find()->select('name,id')->indexBy('id')->column();
        //项目阶段
        $projectProcess = ProjectProcess::find()->where(['lvl'=>1,'root'=>$model->project_id])->select('name,id')->all();
        //审批人数据
        $mdlEcApp = EcApproval::findOne(['ec_id'=>$model->id,'type'=>EcApproval::TYPE_ECR]);
        $model->approver1 = $mdlEcApp->approver1;
        $model->approver2 = $mdlEcApp->approver2;
        $model->approver4dcc = $mdlEcApp->approver4dcc;
        //影响范围
        $arrEffectRangeOrg = ProjectProcessTemplate::find()->alias('ppt')->leftJoin('department as dpt','dpt.id=ppt.department_id')
            ->where(['lvl'=>3,'selected'=>1])->select('ppt.name,dpt.name as dname')->asArray()->all();
        $arrEffectRange=[];
        foreach ($arrEffectRangeOrg as $val){
            $arrEffectRange[$val['dname']][$val['name']]=$val['name'];
        }

        return $this->render('update', [
            'model' => $model,
            'preview'=>$preview,
            'previewCfg'=>$previewCfg,
            'dataMtrDescription'=>json_encode($dataMtr['description']),
            'dataMtrPartNo'=>$dataMtr['part_no'],
            'pjtName'=>$pjtName,
            'projectProcess'=>$projectProcess,
            'dataUser'=>$model->getApprovers(),
            'arrEffectRange'=>$arrEffectRange
        ]);
    }

    /**
     * Deletes an existing Ecr model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ecr model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ecr the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ecr::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 文件上传
     */
    public function actionDeleteAttachment($id)
    {
        $model = EcrAttachment::findOne($id);
        if($model->delete())
        {
            unlink($model->path);
        }
        $preview = $previewCfg = [];
        $ecrAttachments = EcrAttachment::findAll($model->ecr_id);
        foreach ($ecrAttachments as $att)
        {
            $preview[] = $att->path;
            $previewCfg[] =['caption' => $att->name, 'url'=>'/ecr/delete-attachment?id='.$att->id];
        }

        echo json_encode([
            'initialPreview' => $preview,
            'initialPreviewConfig' => $previewCfg,
            'append' => false,
        ]);
    }

    /**
     * 保存文件
     */
    private function  saveAttachments($model)
    {
        $uploadFiles = UploadedFile::getInstances($model,'uploadFile');
        foreach ($uploadFiles as $key=>$file)
        {
            //生成文件名和保存的路径
            $path = '../uploads/ecr/';
            if (!is_dir($path))
                mkdir($path);
            //保存时的随机名
            $nameRandom = CommonFunc::genRandomString(9).'.'.$file->extension;
            while(file_exists('../uploads/ecr/'.$nameRandom))//看文件是否存在
                $nameRandom = CommonFunc::genRandomString(9).'.'.$file->extension;
            $path = $path . $nameRandom;
            //上传文件
            if(!$file->saveAs($path))
                return false;
            //保存附件数据库
            $mdlAttachment = new EcrAttachment();
            $mdlAttachment->ecr_id = $model->id;
            $mdlAttachment->name = $file->name;
            $mdlAttachment->path = $path;
            $mdlAttachment->updated_at = time();
            if(!$mdlAttachment->save())
            {
                $mdlAttachment->getErrors();die;
                return false;
            }
        }
        return true;
    }

    /**
     * 下载附件
     */
    public function actionDownload($pathFile,$filename)
    {
        if (file_exists($pathFile))
        {
            $flag=$_SERVER['HTTP_USER_AGENT'];
            if(strpos($flag,'Trident')||strpos($flag,'Edge'))
                $filename = urlencode($filename);
            return Yii::$app->response->sendFile($pathFile, $filename);
        }
    }

    /*
     * ecr影响范围设置
     */
    public function actionSetEffectRange()
    {
        return $this->render('set-effect-range',['data'=>json_encode($this->getDataEffectRange())]);
    }


    /*
     * ecr影响范围设置的分配文件
     */
    public function actionAssignFile()
    {
        if(!ProjectProcessTemplate::updateAll(['selected'=>1],['in','id',$_POST['items']]))
            return json_encode(['status'=>0,'message'=>'分配时出错，刷新重试。','data'=>$this->getDataEffectRange()]);
        return json_encode(['status'=>1,'message'=>'','data'=>$this->getDataEffectRange()]);
    }

    /**
     * ecr影响范围设置的删除文件
     */
    public function actionRemoveFile()
    {
        if(!ProjectProcessTemplate::updateAll(['selected'=>0],['in','id',$_POST['items']]))
            return json_encode(['status'=>0,'message'=>'分配时出错，刷新重试。','data'=>$this->getDataEffectRange()]);
        return json_encode(['status'=>1,'message'=>'','data'=>$this->getDataEffectRange()]);
    }

    /**
     * ECR影响范围设置的数据
     */
    public function getDataEffectRange()
    {
        $data['avaliable'] = ProjectProcessTemplate::find()->where(['lvl'=>3,'selected'=>0])
            ->select('name,id')->indexby('id')->column();
        $data['assigned'] = ProjectProcessTemplate::find()->where(['lvl'=>3,'selected'=>1])
            ->select('name,id')->indexby('id')->column();
        return $data;

    }

    /**
     * ECR创建时检查，是否有未完成的ECN
     * 如果任务列表中，有未通过的ECR就不可以新建
     */
    public function actionCreateCheck()
    {
        $taskType = '('.Tasks::TASK_TYPE_ECR1.','.Tasks::TASK_TYPE_ECR2.','.Tasks::TASK_TYPE_ECR3.','.Tasks::TASK_TYPE_ECR4.')';
        $task = Tasks::find()->where('status <> 3 and type in '.$taskType)->all();
        if(empty($task)) {
            $task = Tasks::find()->where('status <> 3 and type = '.Tasks::TASK_TYPE_BOM_UPLOAD)->all();
            if(empty($task)){
                return true;
            }
            return false;
        }
        else
            return false;
    }

}
