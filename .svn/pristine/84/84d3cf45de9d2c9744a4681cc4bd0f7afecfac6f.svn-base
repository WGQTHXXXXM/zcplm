<?php

namespace frontend\controllers;

use common\components\CommonFunc;
use frontend\models\MaterialAttachment;
use frontend\models\Materials;
use frontend\models\PjtPcsTempApprover;
use frontend\models\ProjectMaterial;
use frontend\models\Tasks;
use frontend\models\UserTask;
use Yii;
use frontend\models\Projects;
use frontend\models\ProjectsSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\ProjectProcess;
use frontend\models\ProjectProcessTemplate;
use PHPExcel;

/**
 * ProjectsController implements the CRUD actions for Projects model.
 */
class ProjectsController extends Controller
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
     * Lists all Projects models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Projects model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Projects model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Projects();
        $model->status = 1;
        $model->precent = 0;
        $model->working = 0;

        $trans = Yii::$app->db->beginTransaction();
        if ($model->load(Yii::$app->request->post()) )
        {
            $model->created_at = strtotime(substr(str_replace(['年','月','日'],'-',$model->created_at),0,-1));
            $model->end_at = strtotime(substr(str_replace(['年','月','日'],'-',$model->end_at),0,-1));

            if($model->save())
            {
                $id = $model->attributes['id'];
                $name = $model->attributes['name'];
                //获得里程碑的模板的数据
                $dataTreeTpl = ProjectProcessTemplate::find()->select('id,root,lft,rgt,lvl,name')->OrderBy('root,lft')->all();
                //生成SQL语句
                $strSQL = "INSERT INTO project_process(project_id,root,lft,rgt,lvl,name) VALUES";
                //$dataTreeTpl[0]->name = $name;
                foreach($dataTreeTpl as $key=>$val)
                    $strSQL .= "($id,$val->root,$val->lft,$val->rgt,$val->lvl,'$val->name'),";
                //echo $strSQL;die;
                if(Yii::$app->db->createCommand(trim($strSQL,','))->execute()){
                    //更新项目阶段
                    $workingid = ProjectProcess::find()->where(['project_id'=>$id,'lvl'=>1])->OrderBy('root,lft')->One()->id;
                    $model->working = $workingid;
                    if($model->save())
                    {
                        $trans->commit();
                        return $this->redirect(['project-manage-modify', 'id' => $id]);
                    }
                }
                $trans->rollBack();
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Projects model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);


        if ($model->load(Yii::$app->request->post()))
        {
            $model->created_at = strtotime(substr(str_replace(['年','月','日'],'-',$model->created_at),0,-1));
            $model->end_at = strtotime(substr(str_replace(['年','月','日'],'-',$model->end_at),0,-1));
            if($model->save()) {
                //ProjectProcess::UpdateAll(['name'=>$model->name],['project_id'=>$id,'lvl'=>0]);
                return $this->redirect(['project-manage-modify', 'id' => $id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Projects model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Projects model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Projects the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Projects::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * 项目管理模板
     */
    public function actionShowTreeTemplate()
    {
        $dataMsView = ProjectProcessTemplate::find()->select('id,root,lft,rgt,lvl,name,disabled,visible,icon_type,icon')->OrderBy('root,lft');
        return $this->render('show-tree-template',['dataMsView'=>$dataMsView]);
    }

    /*
     * 项目管理修改
     */
    public function actionProjectManageModify($id)
    {
        $dataMsView = ProjectProcess::find()->select('id,root,lft,rgt,lvl,name,disabled,selected')
            ->OrderBy('root,lft')->where(['project_id'=>$id]);

        $strProName = Projects::findOne($id)->name;

        return $this->render('project-manage-modify',['dataMsView'=>$dataMsView,'strProName'=>$strProName]);
    }

    /*
     * 项目管理查看(徐竹波版本，不用了)
     */
    /*    public function actionProjectManageView($id,$lft=null,$rgt=null)
    {
        $model = Projects::findOne($id);
        if($lft == null)
        {//如果没有这个参数说明是默认的阶段
            $curMdl = $model->process;
            $lft = $curMdl->lft;
            $rgt = $curMdl->rgt;
        }
        else//否则是点击的模块
        {
            $curMdl = ProjectProcess::find()->where(['root'=>$id,'lvl'=>1,'lft'=>$lft,'rgt'=>$rgt])->one();
        }

        $tblTasks = Tasks::find()->where(['type'=>Tasks::TASK_TYPE_PROJECT_FILE_UPLOAD])->groupBy('type_id')
            ->select('type_id,remark,min(status) as status');

        $query = ProjectProcess::find()->alias('l3')->select('l3.name as name,l2.id as pid,
            tasks.status as taskStatus,tasks.remark as taskRemark,
            file.created_at as ctime,file.updated_at as utime,l3.id,user.username as submitter')
            ->leftJoin(['l2'=>'project_process'],
                'l3.lvl=3 and l2.lvl=2 and l3.lft>l2.lft and l3.rgt<l2.rgt and l3.root='.$id.' and l2.root='.$id)
            ->leftJoin(['file'=>'project_attachment'],'file.file_id = l3.id')
            ->leftJoin(['tasks'=>$tblTasks],'tasks.type_id=l3.id')
            ->leftJoin('user','user.id=file.user_id')
            ->where('l2.lft>'.$lft.' and l3.rgt<'.$rgt)->orderby('l3.root,l3.lft,l3.rgt,l3.lvl');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => '100',
            ],
        ]);

        $mdl = ProjectProcess::find()->where(['root'=>$id,'lvl'=>1])->OrderBy('lft')->all();

        return $this->render('project-manage-view',
            ['dataProvider'=>$dataProvider,'mdl'=>$mdl,'curMdl'=>$curMdl,'project'=>$model->name]);
    }
*/

    /*
     * 项目管理查看
     */
    public function actionProjectManageView($id)
    {
        $dataMsView = ProjectProcess::find()->select('id,root,lft,rgt,lvl,name,disabled,selected')
            ->OrderBy('root,lft')->where(['project_id'=>$id]);

        $strProName = Projects::findOne($id)->name;

        return $this->render('project-manage-view',['dataMsView'=>$dataMsView,'strProName'=>$strProName]);
    }

    /*
     * 物料某一个文件历史版本
     */
    public function actionMtrSingleFileDetail()
    {
        $data = new ActiveDataProvider([
            'query'=>MaterialAttachment::find()->where(['file_class_name'=>$_POST['nodename'],'material_id'=>$_POST['mtrid']])
            ->andWhere('modify_material_id<>-1')->orderBy('version')
        ]);
        return $this->renderAjax('mtr-single-file-detail',['data'=>$data]);
    }

    /*
     * 获得最高版本
     */
    public function getMaxVersion($id,$name)
    {
        $model = MaterialAttachment::find()->where(['material_id'=>$id,'file_class_name'=>$name])
            ->andWhere('modify_material_id<>-1')
            ->select('max(version) as version')->groupBy('material_id')->one();
        if(empty($model))
            return 1;
        else
            return $model->version+1;
    }

    /*
     * 物料文件浏览
     */
    public function actionMtrFileView($id,$idUserTask=-1)
    {
        $mdlUserTask = null;
        if($idUserTask != -1)
        {
            $mdlUserTask = UserTask::find()->leftJoin('tasks','user_task.task_id=tasks.id')
                ->select('*,user_task.status as userTaskStatus,tasks.status as taskStatus')
                ->where(['user_task.id'=>$idUserTask])->one();
        }

        $model = MaterialAttachment::findOne($id);
        return $this->render('mtr-file-view',['model'=>$model,'mdlUserTask'=>$mdlUserTask]);
    }

    /*
     * 物料文件更新
     */
    public function actionMtrFileUpdate($id)
    {
        $model = MaterialAttachment::findOne($id);
        $time = time();
        $mdlTask = Tasks::findOne(['type_id'=>$model->id,'type'=>Tasks::TASK_TYPE_MTR_FILE_UPLOAD]);
        $tran = Yii::$app->db->beginTransaction();
        if (isset($_POST['radio-type-upload']))//提交
        {
            //得到最大版本
            $MaxVersion = $this->getMaxVersion($_POST['MaterialAttachment']['material_id'],$_POST['MaterialAttachment']['file_class_name']);

            if($_POST['radio-type-upload'] == 1){//上传
                $attachments = $_FILES['uploadFile'];
                $fileName = $attachments['name'];
                if(!empty($fileName)){
                    //生成文件名和保存的路径
                    $path = '../uploads/materials/';
                    if (!is_dir($path))
                        mkdir($path);
                    //保存时的随机名
                    $nameRandom = CommonFunc::genRandomString(9) . '.' . pathinfo(basename($fileName))['extension'];
                    while (file_exists('../uploads/materials/' . $nameRandom))//看文件是否存在
                        $nameRandom = CommonFunc::genRandomString(9) . '.' . pathinfo(basename($fileName))['extension'];
                    $path = $path . $nameRandom;


                    //保存附件数据库
                    $model->modify_material_id = -1;
                    $model->name = $fileName;
                    $model->path = $path;
                    $model->material_id = $_POST['MaterialAttachment']['material_id'];
                    $model->file_class_name = $_POST['MaterialAttachment']['file_class_name'];
                    $model->updated_at = $time;
                    $model->version = $MaxVersion;
                    $model->remark = $_POST['attachment_remark_sc'];
                    if ($model->save()) {
                        //上传文件
                        if (move_uploaded_file($attachments['tmp_name'], $path)){

                            if($model->updateTask($mdlTask)){
                                $tran->commit();
                                Yii::$app->getSession()->setFlash('success', "成功提交任务");
                                return $this->redirect(['/tasks/index']);
                            }
                        }
                    }
                    if(file_exists($path))//如果没上传成功删掉上传的文件
                        unlink($path);
                    Yii::$app->getSession()->setFlash('warning', "提交失败，联系管理员");
                    $tran->rollBack();
                }else
                    Yii::$app->getSession()->setFlash('warning', "提交失败，附件不可以为空");

            }else if($_POST['radio-type-upload'] == 2){//复用
                $tempMdl = MaterialAttachment::findOne($_POST['material_attachemt_id']);
                if(!empty($_POST['material_attachemt_id'])) {
                    $model->path = $tempMdl->path;
                    $model->name = $tempMdl->name;
                    $model->version = $MaxVersion;
                    $model->remark = $_POST['attachment_remark_fy'];
                    $model->updated_at = $time;
                    $model->modify_material_id = -1;
                    $model->material_id = $_POST['MaterialAttachment']['material_id'];
                    $model->file_class_name = $_POST['MaterialAttachment']['file_class_name'];
                    if ($model->save()) {
                        if ($model->updateTask($mdlTask)) {
                            $tran->commit();
                            Yii::$app->getSession()->setFlash('success', "成功提交任务");
                            return $this->redirect(['/tasks/index']);
                        }

                    }
                    Yii::$app->getSession()->setFlash('warning', "提交失败，联系管理员");
                    $tran->rollBack();
                }else
                    Yii::$app->getSession()->setFlash('warning', "提交失败，复用的附件不可以为空");
            }
        }

        //提交界面
        $allMtr = Materials::find()->select('zc_part_number,material_id')->indexby('material_id')->column();

        $departmentApprove = $model->getDepartmentApprove();

        $mdlUserTasks = UserTask::find()->where(['task_id'=>$mdlTask->id])->all();
        $userApprove = [];
        foreach ($mdlUserTasks as $mdlUserTask){
            $userApprove[$mdlUserTask->lvl][] = $mdlUserTask->user_id;
        }

        $model->departLvl = $userApprove;
        return $this->render('mtr-file-upload', [
            'model' => $model,
            'allMtr' => $allMtr,
            'departmentApprove'=>$departmentApprove,
        ]);
    }

    /*
     * 检查物料是否可以上传（方法是这个物料的文件有没有在审批 ）
     */
    public function actionCheckMtrFileUpload()
    {
        $mtrid = $_POST['mtrid'];
        $nodename = $_POST['nodename'];
        $model = MaterialAttachment::find()->where(['material_id'=>$mtrid,'file_class_name'=>$nodename,'modify_material_id'=>-1])->one();
        if(empty($model))
            return json_encode(['status'=>1,'message'=>'可以上传','data'=>[]]);
        else
            return json_encode(['status'=>0,'message'=>'已经上传过了，等通过后再传','data'=>[]]);
    }

    /*
     * 物料文件上传
     */
    public function actionMtrFileUpload()
    {
        $model = new MaterialAttachment();
        $time = time();


        if (isset($_POST['radio-type-upload']))//提交
        {
            $tran = Yii::$app->db->beginTransaction();
            //得到最大版本
            $MaxVersion = $this->getMaxVersion($_POST['MaterialAttachment']['material_id'],$_POST['MaterialAttachment']['file_class_name']);
            $model->material_id = $_POST['MaterialAttachment']['material_id'];
            $model->file_class_name = $_POST['MaterialAttachment']['file_class_name'];

            if($_POST['radio-type-upload'] == 1){//上传
                $attachments = $_FILES['uploadFile'];
                $fileName = $attachments['name'];
                if(!empty($fileName)){
                    //生成文件名和保存的路径
                    $path = '../uploads/materials/';
                    if (!is_dir($path))
                        mkdir($path);
                    //保存时的随机名
                    $nameRandom = CommonFunc::genRandomString(9) . '.' . pathinfo(basename($fileName))['extension'];
                    while (file_exists('../uploads/materials/' . $nameRandom))//看文件是否存在
                        $nameRandom = CommonFunc::genRandomString(9) . '.' . pathinfo(basename($fileName))['extension'];
                    $path = $path . $nameRandom;


                    //保存附件数据库
                    $model->name = $fileName;
                    $model->path = $path;
                    $model->updated_at = $time;
                    $model->version = $MaxVersion;
                    $model->remark = $_POST['attachment_remark_sc'];
                    $model->modify_material_id = -1;
                    if ($model->save()) {
                        //上传文件
                        if (move_uploaded_file($attachments['tmp_name'], $path)){
                            if($model->createTask()){
                                $tran->commit();
                                Yii::$app->getSession()->setFlash('success', "成功提交任务");
                                return $this->redirect(['/tasks/index']);
                            }else{
                                if(file_exists($path))//如果没上传成功删掉上传的文件
                                    unlink($path);
                            }
                        }
                    }
                    Yii::$app->getSession()->setFlash('warning', "提交失败，联系管理员");
                    $tran->rollBack();
                }else
                    Yii::$app->getSession()->setFlash('warning', "提交失败，附件不可以为空");
            }else if($_POST['radio-type-upload'] == 2){//复用
                if(!empty($_POST['material_attachemt_id'])){
                    $tempMdl = MaterialAttachment::findOne($_POST['material_attachemt_id']);
                    $model->path = $tempMdl->path;
                    $model->name = $tempMdl->name;
                    $model->version = $MaxVersion;
                    $model->remark = $_POST['attachment_remark_fy'];
                    $model->updated_at = $time;
                    $model->modify_material_id = -1;
                    if($model->save()){
                        if($model->createTask()){
                            $tran->commit();
                            Yii::$app->getSession()->setFlash('success', "成功提交任务");
                            return $this->redirect(['/tasks/index']);
                        }
                    }
                    Yii::$app->getSession()->setFlash('warning', "提交失败，联系管理员");
                    $tran->rollBack();
                }else
                    Yii::$app->getSession()->setFlash('warning', "提交失败，复用的附件不可以为空");
            }
            $_GET['mtrid'] = $model->material_id;
            $_GET['file_class_name'] = $model->file_class_name;
        }

        //提交界面
        $model->material_id = $_GET['mtrid'];
        $model->file_class_name=$_GET['file_class_name'];

        $allMtr = Materials::find()->select('zc_part_number,material_id')->indexby('material_id')->column();

        $departmentApprove = $model->getDepartmentApprove();

        return $this->render('mtr-file-upload', [
            'model' => $model,
            'allMtr' => $allMtr,
            'departmentApprove'=>$departmentApprove
        ]);
    }

    public function actionDownloadPercent($id)
    {
        require_once (Yii::getAlias("@common")."/components/phpexcel/PHPExcel.php");
        //第一行的表头
        $headerArr = ['remark'=>'智车料号','file_class_name'=>'物料名称','desc'=>'描述','version'=>'最新版本','name'=>'附件名'];
        //所有的表的名字
        $tableName = UserTask::$tableName;
        array_pop($tableName);
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->createSheet(0);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('所选项目文件上传统计');
        $cells = 0;
        //第不nc的一行
        foreach($headerArr as $v)
        {
            $colum = \PHPExcel_Cell::stringFromColumnIndex($cells);
            //字体
            $objPHPExcel->getActiveSheet()->setCellValue($colum.'1',$v)->getStyle($colum.'1')
                ->getFont()->setBold(true)->setSize(12);
            //宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setAutoSize(true);
            //设置背景色
            $objPHPExcel->getActiveSheet()->getStyle($colum.'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle($colum.'1')->getFill()->getStartColor()->setRGB('C5D9F1');
            $cells++;
        }
        //加不nc的数据，去对应的数据库
        $node=ProjectProcess::findOne($id);
        $hardwareModel = ProjectMaterial::figureProcess($node,true);
        $row = 2;
        foreach ($hardwareModel as $material)
        {
            $cells = 0;
            foreach ($headerArr as $key=>$v)
            {
                $colum = \PHPExcel_Cell::stringFromColumnIndex($cells);
                $objPHPExcel->getActiveSheet()->setCellValue($colum.$row,$material->$key);
                $cells++;
            }
            $row++;
        }

        //下载文件的名字，要体现文件的路径和日期
        $fileName = $node->name.date('-m月d日').'.xlsx';
        $pnames = $node->parents()->andWhere(['project_id'=>$this->project_id]);

        if(!empty($pnames)) {
            $pnames = array_reverse($pnames->all());
            foreach ($pnames as $val){
                $fileName = $val->name.'-'.$fileName;
            }
        }
        //下载
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        $writer = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        $writer->save('php://output');
    }

}

























