<?php

namespace frontend\controllers;

use common\components\CommonFunc;
use common\models\User;
use frontend\models\ExtBomsParent;
use frontend\models\ImportMtrForm;
use frontend\models\MaterialAttachment;
use frontend\models\ProjectProcessTemplate;
use Yii;
use frontend\models\ModifyMaterial;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\Tasks;
use frontend\models\MaterialEncodeRule;
use yii\helpers\ArrayHelper;
use frontend\models\Materials;
use frontend\models\UserTask;
use frontend\models\MaterialApprover;
use yii\web\UploadedFile;

require_once (Yii::getAlias("@common")."/components/phpexcel/PHPExcel.php");


/**
 * ModifyMaterialController implements the CRUD actions for ModifyMaterial model.
 */
class ModifyMaterialController extends Controller
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
                    'del-attachment'=>['POST'],
                    'upload-attachment'=>['POST'],
                    //'mtr-upgrade'=>['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all ModifyMaterial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ModifyMaterial::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ModifyMaterial model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id,$idUserTask=-1)
    {
        $model = $this->findModel($id);
        $mdlMtrApprover = MaterialApprover::findOne(['material_id'=>$id]);
        if(!empty($mdlMtrApprover)){//最早的一批没有分级审批，何冰最早建的一批料
            $model->approver1 = User::findOne($mdlMtrApprover->approver1)->username;
            $model->approver2 = User::findOne($mdlMtrApprover->approver2)->username;
            if($mdlMtrApprover->approver3dcc!=0){//三级审批可选之前是固定的，没有三级
                $model->approver3dcc = User::findOne($mdlMtrApprover->approver3dcc)->username;
                $model->approver3purchase = User::findOne($mdlMtrApprover->approver3purchase)->username;
            }
        }
        $mdlUserTask = null;
        if($idUserTask != -1)
        {
            $mdlUserTask = UserTask::find()->leftJoin('tasks','user_task.task_id=tasks.id')
                ->select('*,user_task.status as userTaskStatus,tasks.status as taskStatus')
                ->where(['user_task.id'=>$idUserTask])->one();
        }

        //附件
        $mdlMdfMaterial = ModifyMaterial::findOne(['id'=>$id]);
        //附件
        $dataAttachmentNew = new ActiveDataProvider([
            'query' => MaterialAttachment::find()->where(['modify_material_id'=>$model->id])
        ]);

        $idTemp = -2;
        if(!empty($model->material_id))//防止为null时
            $idTemp = $model->material_id;

        $dataAttachmentOld = new ActiveDataProvider([
            'query' => MaterialAttachment::find()->
            where(['material_id'=>$idTemp])->andWhere('modify_material_id<>-1')
        ]);
        return $this->render('view', [
            'model' => $model,
            'mdlUserTask'=>$mdlUserTask,
            'dataAttachmentNew'=>$dataAttachmentNew,
            'dataAttachmentOld'=>$dataAttachmentOld,
        ]);
    }


    /**
     * 上传附件的脚本
     */
    public function actionUploadAttachment($id)
    {
        if (!empty($_FILES)) {
            $attachments = $_FILES['attachment'];
            $trans = Yii::$app->db->beginTransaction();
            $isSuc = true;
            $arrPath = [];
            foreach ($attachments['name'] as $key=>$fileName)
            {
                if(empty($fileName))//没有上传时会为空
                    continue;
                //生成文件名和保存的路径
                $path = '../uploads/materials/';
                if (!is_dir($path))
                    mkdir($path);
                //保存时的随机名
                $nameRandom = CommonFunc::genRandomString(9).'.'.pathinfo(basename($fileName))['extension'];
                while(file_exists('../uploads/materials/'.$nameRandom))//看文件是否存在
                    $nameRandom = CommonFunc::genRandomString(9).'.'.pathinfo(basename($fileName))['extension'];
                $path = $path . $nameRandom;
                $arrPath[] = $path;

                //保存附件数据库
                $mdlAttachment = new MaterialAttachment();
                $mdlAttachment->material_id = $id;
                $mdlAttachment->modify_material_id = 0;
                $mdlAttachment->name = $fileName;
                $mdlAttachment->path = $path;
                $mdlAttachment->file_class_name = '';
                $mdlAttachment->updated_at = time();
                $mdlAttachment->version = 1;
                $mdlAttachment->remark = $_POST['attachment_remark'][$key];
                //上传文件
                if(!move_uploaded_file($attachments['tmp_name'][$key],$path))
                {
                    $isSuc = false;
                    var_dump('附件上传错误');die;
                }

                if($isSuc&&!$mdlAttachment->save())
                {
                    $isSuc = false;
                    var_dump($mdlAttachment->getErrors());die;
                }
                //如果是一个附件多物料用
                if(isset($_POST['ismany'])){
                    $mtrLike = pathinfo(basename($fileName))['filename'];
                    $mdlMtrs = Materials::find()->select('materials.material_id as material_id')
                        ->leftJoin('material_attachment','material_attachment.material_id=materials.material_id')
                        ->where('material_attachment.material_id is null')
                        ->andWhere(['like','materials.mfr_part_number',$mtrLike.'%',false])->all();
                    foreach ($mdlMtrs as $value){
                        if($value->material_id == $id)
                            continue;
                        $mdlAttachment = new MaterialAttachment();
                        $mdlAttachment->material_id = $value->material_id;
                        $mdlAttachment->modify_material_id = 0;
                        $mdlAttachment->name = $fileName;
                        $mdlAttachment->path = $path;
                        $mdlAttachment->file_class_name = '';
                        $mdlAttachment->updated_at = time();
                        $mdlAttachment->version = 1;
                        $mdlAttachment->remark = $_POST['attachment_remark'][$key];
                        if(!$mdlAttachment->save()){
                            $isSuc = false;
                            var_dump($mdlAttachment->getErrors());die;
                            break;
                        }
                    }
                }

                if(!$isSuc)
                    break;
            }
            if($isSuc)
            {
                Yii::$app->getSession()->setFlash('success', "上传成功");
                $trans->commit();
            } else {
                Yii::$app->getSession()->setFlash('error', "上传失败");
                $trans->rollBack();
                foreach ($arrPath as $path)
                    if(MaterialAttachment::find()->where(['path'=>$path])->count() == 1)
                        unlink($path);
            }
            return $this->redirect('/materials/noattach');
        }
        $model = Materials::find()->select('zc_part_number,mfr_part_number')->where(['material_id'=>$id])->one();

        return $this->render('upload-attachment', ['model'=>$model]);
    }


    /**
     * Creates a new ModifyMaterial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     *
     */
    public function actionCreate()
    {
        $modelMtr = new ModifyMaterial();
        $transaction = Yii::$app->getDb()->beginTransaction();//开启事务
        if ($modelMtr->load(Yii::$app->request->post())) {
            $isSuc = true;
            $msg ='保存失败';

            if(!empty(ModifyMaterial::findOne(['zc_part_number'=>$modelMtr->zc_part_number])))//防止重复，时间差上别人也建了些料号
            {
                $msg = '此智车料号已经被别人提交了，请刷新页面重新建';
                $isSuc = false;
            }
            //存二三四供
            if(!empty(Yii::$app->request->post()['ModifyMaterial']['mfrPartNo2']))
                $modelMtr->manufacturer2_id = Materials::findOne(['zc_part_number'=>Yii::$app->request->post()['ModifyMaterial']['mfrPartNo2']])->material_id;
            if(!empty(Yii::$app->request->post()['ModifyMaterial']['mfrPartNo3']))
                $modelMtr->manufacturer3_id = Materials::findOne(['zc_part_number'=>Yii::$app->request->post()['ModifyMaterial']['mfrPartNo3']])->material_id;
            if(!empty(Yii::$app->request->post()['ModifyMaterial']['mfrPartNo4']))
                $modelMtr->manufacturer4_id = Materials::findOne(['zc_part_number'=>Yii::$app->request->post()['ModifyMaterial']['mfrPartNo4']])->material_id;
            if($isSuc){
                $isSuc = $modelMtr->save();
            }
            if($isSuc)
            {
                $mdlMtrApprover = new MaterialApprover();
                $mdlMtrApprover->approver1 = $modelMtr->approver1;
                $mdlMtrApprover->approver2 = $modelMtr->approver2;
                $mdlMtrApprover->approver3dcc = $modelMtr->approver3dcc;
                $mdlMtrApprover->approver3purchase = $modelMtr->approver3purchase;
                $mdlMtrApprover->material_id = $modelMtr->id;
                if(!$mdlMtrApprover->save())
                    $isSuc = false;
            }
            //上传规格书及添加数据
            if($isSuc)
            {
                if(!$this->saveAttachment($modelMtr))
                    $isSuc = false;
            }
            if($isSuc && !Tasks::generateTask(Tasks::TASK_TYPE_MTR_APPROVER1,$modelMtr->id,ModifyMaterial::MATERIAL_CREATE1))//立即提交
                $isSuc = false;
            if($isSuc)
            {
                Yii::$app->getSession()->setFlash('success', "成功，提交任务");
                $transaction->commit();
                return $this->redirect(['/tasks/index']);
            }
            else
            {
                Yii::$app->getSession()->setFlash('error', $msg);
                $transaction->rollBack();
            }
        }
        $class1 = MaterialEncodeRule::find()->select('name,root')->where(['lvl'=>0])->OrderBy('root')->asArray()->all();
        $dataUser = self::getUserApprover();
        //文件类名
        $fileClassName = ProjectProcessTemplate::find()->where(['lvl'=>3])->select('name')->indexby('name')->column();
        //所有物料
        $allMtr = Materials::find()->select('zc_part_number,material_id')->indexby('material_id')->column();

        return $this->render('create', [
            'model' => $modelMtr,
            'class1' => $class1,
            'dataUser'=>$dataUser,
            'fileClassName'=>$fileClassName,
            'allMtr'=>$allMtr
        ]);
    }

    /**
     * 保存规格书
     */
    public function saveAttachment($model)
    {
        $time = time();
        //处理上传的文件
        if(isset($_FILES['attachment'])) {//当控件为空时ie浏览器检测不到这个
            $attachments = $_FILES['attachment'];
            foreach ($attachments['name'] as $key => $fileName) {
                if (empty($fileName))//没有上传时会为空
                    continue;
                $fileClassNameA = $_POST['fileClassNameA'][$key];
                //生成文件名和保存的路径
                $path = '../uploads/materials/';
                if (!is_dir($path))
                    mkdir($path);
                //保存时的随机名
                $nameRandom = CommonFunc::genRandomString(9) . '.' . pathinfo(basename($fileName))['extension'];
                while (file_exists('../uploads/materials/' . $nameRandom))//看文件是否存在
                    $nameRandom = CommonFunc::genRandomString(9) . '.' . pathinfo(basename($fileName))['extension'];
                $path = $path . $nameRandom;
                //得到版本
                $version = $this->getAttachmentMaxVersion($fileClassNameA, $model->material_id);

                //保存附件数据库
                $mdlAttachment = new MaterialAttachment();
                $mdlAttachment->modify_material_id = $model->id;
                $mdlAttachment->name = $fileName;
                $mdlAttachment->path = $path;
                $mdlAttachment->file_class_name = $fileClassNameA;
                $mdlAttachment->updated_at = $time;
                $mdlAttachment->version = $version;
                $mdlAttachment->remark = $_POST['attachment_remarkA'][$key];
                if (!$mdlAttachment->save()) {
                    var_dump($mdlAttachment->getErrors());
                    die;
                    return false;
                }
                //上传文件
                if (!move_uploaded_file($attachments['tmp_name'][$key], $path))
                    return false;
            }
        }
        //处理复用的文件
        $arrAttachmentId = isset($_POST['attachmentId'])?$_POST['attachmentId']:[];
        foreach ($arrAttachmentId as $key=>$attachmentId){
            $fileClassNameB = $_POST['fileClassNameB'][$key];
            //得到版本
            $version = $this->getAttachmentMaxVersion($fileClassNameB,$model->material_id);
            $mdlCopyAttachment = MaterialAttachment::findOne($attachmentId);
            $mdlAttachment = new MaterialAttachment();
            $mdlAttachment->modify_material_id = $model->id;
            $mdlAttachment->name = $mdlCopyAttachment->name;
            $mdlAttachment->path = $mdlCopyAttachment->path;
            $mdlAttachment->file_class_name = $fileClassNameB;
            $mdlAttachment->updated_at = $time;
            $mdlAttachment->version = $version;
            $mdlAttachment->remark = $_POST['attachment_remarkB'][$key];
            if(!$mdlAttachment->save())
            {
                var_dump($mdlAttachment->getErrors());die;
                return false;
            }
        }

        return true;
    }

    /**
     * 得到上传附件的版本
     */
    private function getAttachmentMaxVersion($fileName,$id)
    {
        if(empty($id))//如果为空说明是新建的料，版本为1
            return 1;

        $version = MaterialAttachment::find()->where(['file_class_name'=>$fileName,'material_id'=>$id])
            ->orderBy(['version'=>SORT_DESC])->one();
        if(empty($version))
            $version = 1;
        else{
            $version = $version->version+1;
        }

        return $version;
    }

    /**
     * 物料的三级审批的数据
     */
    public function getUserApprover()
    {
//        $dataUser[] = User::getDepartmentUser();
//        $dataUser[] = $dataUser[0];
//        $dataUser[] = [42=>'wangchong'];
//        $dataUser[] = [43=>'qiushancheng',31=>'gaoyanjuan'];
        $a = User::getDepartmentUser(1);
        unset($a[Yii::$app->user->id]);
        $dataUser[] = $a;
        $dataUser[] = $a;
        $dataUser[] = $a;
        $dataUser[] = $a;
        return $dataUser;
    }

    /**
     *检查这个料是否在审批
     */
    public function actionCheckMaterial($id)
    {
        $model = ModifyMaterial::find()->where(['material_id'=>$id])->orderBy('id desc')->all();
        if(empty($model))//没有这个料就可以新建了
            return json_encode(['status' => 1, 'message' => '', 'data' => '']);
        else//说明中间物料库里有这个料，
        {
            //看这个中间料在审批中是否被通过
            $mdltask = Tasks::findOne(['type'=>Tasks::TASK_TYPE_MTR_APPROVER1,'type_id'=>$model[0]->id]);
            if(empty($mdltask))
            {
                $mdltask = Tasks::findOne(['type'=>Tasks::TASK_TYPE_MTR_APPROVER2,'type_id'=>$model[0]->id]);
                if(empty($mdltask))
                    $mdltask = Tasks::findOne(['type'=>Tasks::TASK_TYPE_MATERIAL,'type_id'=>$model[0]->id]);
            }

            if($mdltask->status == Tasks::STATUS_APPROVED)//如果是已经审批通过就可以新建再更新
                return json_encode(['status' => 1, 'message' => '', 'data' => '']);
            else//否则不允许更新
                return json_encode(['status' => 0, 'message' => '这颗料正在被审批', 'data' => '']);
        }
    }

    /**
     * 这个料的状态
     */
    public function actionGetMaterialStat($id)
    {
        $mdlMtr = Materials::findOne($id);
        $models = ModifyMaterial::find()->where(['zc_part_number'=>$mdlMtr->zc_part_number])->orderBy('id desc')->all();
        if(empty($models))//没有这个料就显示：此料为导入料
            return $this->renderAjax('stat',['status'=>0]);
        else//说明中间物料库里有这个料
        {
            //看这个中间料在审批中是否被通过
            $mdltasks = Tasks::find()->where(['type'=>Tasks::TASK_TYPE_MTR_APPROVER1,'type_id'=>$models[0]->id])->all();
            if(empty($mdltasks))
            {
                $mdltasks = Tasks::find()->where(['type'=>Tasks::TASK_TYPE_MTR_APPROVER2,'type_id'=>$models[0]->id])->all();
                if(empty($mdltasks))
                    $mdltasks = Tasks::find()->where(['type'=>Tasks::TASK_TYPE_MATERIAL,'type_id'=>$models[0]->id])->all();
            }
            $query = UserTask::find()->where(['task_id'=>$mdltasks[0]->id]);

            $dataProvider = new ActiveDataProvider(['query'=>$query]);

            $dataPorviderTask = new ArrayDataProvider(['allModels'=>$mdltasks]);

            return $this->renderAjax('stat',
                ['status'=>1,'dataProvider'=>$dataProvider,'dataPorviderTask'=>$dataPorviderTask]);
        }
    }

    /**
     * @param string $id:为物料的id：ModifyMaterial或Materials这两个表的id
     * @param string $material:如果为1时说明是点击的是materials的料，如果为零点击的是ModifyMaterial的料
     * @return mixed
     */
    public function actionUpdate($id,$material)
    {
        $msg = "更新出错";
        ///////////更新界面的数据//////////////////
        if($material == 1)//说明是从物料库来的链接
        {
            $mdlMtr = Materials::findOne($id);
            $model = new ModifyMaterial();
            $model->assy_level = $mdlMtr->assy_level;
            $model->purchase_level = $mdlMtr->purchase_level;
            $model->mfr_part_number = $mdlMtr->mfr_part_number;
            $model->part_name = $mdlMtr->part_name;
            $model->description = $mdlMtr->description;
            $model->unit = $mdlMtr->unit;
            $model->pcb_footprint = $mdlMtr->pcb_footprint;
            $model->manufacturer = $mdlMtr->manufacturer;
            $model->zc_part_number = $mdlMtr->zc_part_number;
            $model->date_entered = $mdlMtr->date_entered;
            $model->vehicle_standard = $mdlMtr->vehicle_standard;
            $model->part_type = $mdlMtr->part_type;
            $model->value = $mdlMtr->value;
            $model->schematic_part = $mdlMtr->schematic_part;
            $model->price = $mdlMtr->price;
            $model->recommend_purchase = $mdlMtr->recommend_purchase;
            $model->minimum_packing_quantity = $mdlMtr->minimum_packing_quantity;
            $model->lead_time = $mdlMtr->lead_time;
            $model->manufacturer2_id = $mdlMtr->manufacturer2_id;
            $model->manufacturer3_id = $mdlMtr->manufacturer3_id;
            $model->manufacturer4_id = $mdlMtr->manufacturer4_id;
            $model->material_id = $mdlMtr->material_id;
            $model->is_first_mfr = $mdlMtr->is_first_mfr;
            $model->remark = $mdlMtr->remark;
            $model->car_number = $mdlMtr->car_number;
        }
        else if($material == 0)//说明不是从物料库来的链接，从中间物料库来的，被退回的物料
        {
            $model = $this->findModel($id);
            $mdlMtrApprover = MaterialApprover::findOne(['material_id'=>$id]);
            $model->approver1 = $mdlMtrApprover->approver1;
            $model->approver2 = $mdlMtrApprover->approver2;
            $model->approver3dcc = $mdlMtrApprover->approver3dcc;
            $model->approver3purchase = $mdlMtrApprover->approver3purchase;
        }
        //////////提交时的处理////////////////////
        if($model->load(Yii::$app->request->post()))//点击的是更新按钮
        {


            $transaction = Yii::$app->getDb()->beginTransaction();//开启事务
            $isSuc = true;
            //保存二，三，四供
            if(!empty(Yii::$app->request->post()['ModifyMaterial']['mfrPartNo2']))
                $model->manufacturer2_id = Materials::findOne(['zc_part_number'=>Yii::$app->request->post()['ModifyMaterial']['mfrPartNo2']])->material_id;
            else
                $model->manufacturer2_id = "";
            if(!empty(Yii::$app->request->post()['ModifyMaterial']['mfrPartNo3']))
                $model->manufacturer3_id = Materials::findOne(['zc_part_number'=>Yii::$app->request->post()['ModifyMaterial']['mfrPartNo3']])->material_id;
            else
                $model->manufacturer3_id = "";
            if(!empty(Yii::$app->request->post()['ModifyMaterial']['mfrPartNo4']))
                $model->manufacturer4_id = Materials::findOne(['zc_part_number'=>Yii::$app->request->post()['ModifyMaterial']['mfrPartNo4']])->material_id;
            else
                $model->manufacturer4_id = "";
            //排除两人同时提交
            if($isSuc&&$material == 1){
                $str = Tasks::TASK_TYPE_MATERIAL.','.Tasks::TASK_TYPE_MTR_APPROVER1.','.Tasks::TASK_TYPE_MTR_APPROVER2.')';
                $mtrTemp = ModifyMaterial::find()->leftJoin('tasks','tasks.type_id=modify_material.id and tasks.type in ('.$str)
                    ->where(['modify_material.zc_part_number'=>$model->zc_part_number])->orderBy('tasks.id desc')
                    ->select('tasks.status as id')->one();
                if(!empty($mtrTemp)&&$mtrTemp->id != Tasks::STATUS_APPROVED)//如果有任务没有走完
                {
                    $msg = '已经有人更新了这个料，流程还没走完';
                    $isSuc = false;
                }
            }
            if(!$model->save())//保存或更新大表
            {
                $isSuc = false;
                $msg = "保存中间物料表时出错";
            }
            if($isSuc&&$material==0)//保存审批人,更新审批人
            {
                $mdlMtrApprover = MaterialApprover::findOne(['material_id'=>$id]);
                $arrChange = [];
                if($mdlMtrApprover->approver1 != $model->approver1)
                    $arrChange[] = $mdlMtrApprover->approver1;
                if($mdlMtrApprover->approver2 != $model->approver2)
                    $arrChange[] = $mdlMtrApprover->approver2;
                if($mdlMtrApprover->approver3dcc != $model->approver3dcc)
                    $arrChange[] = $mdlMtrApprover->approver3dcc;
                if($mdlMtrApprover->approver3purchase != $model->approver3purchase)
                    $arrChange[] = $mdlMtrApprover->approver3purchase;
                if(!empty($arrChange))//如果更新时审批人改变，就要把改变的这个审批人的审批任务删掉
                {
                    $mdlTask = Tasks::find()->where(['type_id'=>$model->id])
                        ->andWhere(['type'=>[Tasks::TASK_TYPE_MTR_APPROVER1,Tasks::TASK_TYPE_MTR_APPROVER2,Tasks::TASK_TYPE_MATERIAL]])->one();
                    $mdlUserTask = UserTask::find()->where(['task_id'=>$mdlTask->id])->all();
                    foreach ($mdlUserTask as $userTask)
                        $userTask->delete();
                }
                $mdlMtrApprover->approver1 = $model->approver1;
                $mdlMtrApprover->approver2 = $model->approver2;
                $mdlMtrApprover->approver3dcc = $model->approver3dcc;
                $mdlMtrApprover->approver3purchase = $model->approver3purchase;
                if(!$mdlMtrApprover->save())
                    $isSuc = false;
            }
            //生成任务
            if($isSuc&&$material == 1)//如果成功并且是新的更新就要新建一个提交任务
            {
                $mdlMtrApprover = new MaterialApprover();
                $mdlMtrApprover->approver1 = $model->approver1;
                $mdlMtrApprover->approver2 = $model->approver2;
                $mdlMtrApprover->approver3dcc = $model->approver3dcc;
                $mdlMtrApprover->approver3purchase = $model->approver3purchase;

                $mdlMtrApprover->material_id = $model->id;
                if(!$mdlMtrApprover->save())
                {
                    $isSuc = false;
                    $msg = "保存审批人表时出错";
                }
                if($isSuc&&!Tasks::generateTask(Tasks::TASK_TYPE_MTR_APPROVER1,$model->id,ModifyMaterial::MATERIAL_UPDATE))
                {
                    $isSuc = false;
                    $msg = "生成任务表时出错";
                }
            }
            else if($isSuc&&$material == 0)
            {//如果是更新的是被退回的或新建保存的物料并且要马上更新，只改下状态或新建审批就可以
                $mdlTask = Tasks::find()->where(['type_id'=>$model->id])
                    ->andWhere(['type'=>[Tasks::TASK_TYPE_MTR_APPROVER1,Tasks::TASK_TYPE_MTR_APPROVER2,Tasks::TASK_TYPE_MATERIAL]])->one();

                if($_POST['taskCommit']==1){
                    $mdlTask->status = Tasks::STATUS_COMMITED;
                    $mdlTask->remark = $_POST['taskRemark'];
                    $mdlTask->date = time();
                    $mdlTask->type = Tasks::TASK_TYPE_MTR_APPROVER1;
                    if(strpos($mdlTask->name,'新增物料')!==false)//把以前的二审三审变回到一审
                        $mdlTask->name = ModifyMaterial::MATERIAL_CREATE1;
                    else
                        $mdlTask->name = ModifyMaterial::MATERIAL_UPDATE;
                    if(!$mdlTask->save())
                    {
                        $isSuc = false;
                        $msg = "保存任务表时出错";
                    }

                    //找到更新后的审批人
                    $arrApprovers = Tasks::getApprovers(Tasks::TASK_TYPE_MTR_APPROVER1,$mdlTask->type_id);
                    $mdlUserTask = UserTask::findOne(['task_id'=>$mdlTask->id,'user_id'=>$arrApprovers['approvers'][0]]);
                    if(empty($mdlUserTask))//说明是新建的，稍后保存的，然后再点马上提交
                    {
                        if(!UserTask::GenerateUserTask($arrApprovers['approvers'],$mdlTask->id))
                        {
                            $isSuc = false;
                            $msg = "生成审批表表时出错";
                        }
                    }
                    else
                    {//被退回的
                        $mdlUserTask->status = UserTask::STATUS_UNAPPROVE;
                        $mdlUserTask->remark = '';
                        $mdlUserTask->approve_able = 1;
                        if(!$mdlUserTask->save())
                        {
                            $isSuc = false;
                            $msg = "保存审批表表时出错";
                        }
                    }
                    CommonFunc::sendMail(CommonFunc::APPROVE_NOTICE,$arrApprovers['mail'],$mdlTask->name,
                        $arrApprovers['code'],'user-task/index');

                }else if($_POST['taskCommit']==0){
                    $mdlTask->remark = $_POST['taskRemark'];
                    $mdlTask->date = time();
                    if(!$mdlTask->save())
                    {
                        $isSuc = false;
                        $msg = "保存任务表时出错";
                    }
                }

            }
            //对已经上传的附件处理
            if($isSuc&&isset($_POST['rdo']))
            {
                $delFiles = $_POST['rdo'];
                foreach ($delFiles as $key=>$val)
                {
                    MaterialAttachment::updateAll(['status'=>$val],['id'=>$key]);
                }
            }
            //保存上传的附件
            if($isSuc)
            {
                if(!$this->saveAttachment($model))
                    $isSuc = false;
            }
            //如果保存成功提交，不成功撤回并提示
            if($isSuc)
            {
                Yii::$app->getSession()->setFlash('success', "成功，提交任务");
                $transaction->commit();
                return $this->redirect(['/tasks/index']);
            }
            else
            {
                Yii::$app->getSession()->setFlash('error', $msg);
                $transaction->rollBack();
                return $this->showUpdateView($model);
            }
        }
        else//更新界面
            return $this->showUpdateView($model);
    }

    /**
     * 更新界面
     */
    public function showUpdateView($model)
    {
        $partType = MaterialEncodeRule::findOne($model->part_type);
        $model->class1 = MaterialEncodeRule::find()->where(['root'=>$partType->root,'lvl'=>0])->select('id')->all()[0]->id;
        $class2data = MaterialEncodeRule::find()->where(['lvl'=>1,'root'=>$partType->root])->andWhere(['<','lft',$partType->lft])
            ->andWhere(['>','rgt',$partType->rgt])->select('id,lft,rgt,remark')->all()[0];
        $model->class2 = $class2data->id;
        //一类
        $class1 = MaterialEncodeRule::find()->select('name,root')->where(['lvl'=>0])->OrderBy('root')->all();
        //二类
        $class2 = MaterialEncodeRule::find()->select('name,id')->where(['lvl'=>1,'root'=>$partType->root])->all();
        //以下是形成智车料号的下拉框
        $dataDropMsg = [];
        $componentPos = MaterialEncodeRule::find()->where(['lvl'=>2,'root'=>$partType->root])->andWhere(['>','lft',$class2data->lft])
            ->andWhere(['<','rgt',$class2data->rgt])->OrderBy('lft')->select('name,root,lft,rgt')->all();//三级分类
        $zcPartNum = $model->zc_part_number;
        if(strtolower($class2data->remark) == 'pcba'||strtolower($class2data->remark) == 'pcb')
        {//如果是pcb的，只给后面的几个input框分配值
            $model->mer4 = substr($zcPartNum,1,1);
            $model->mer7 = substr($zcPartNum,2,4);
            $model->mer8 = substr($zcPartNum,6,2);
            $model->mer9 = substr($zcPartNum,8,2);

        }
        else//其它要给下拉框分配值
        {
            foreach ($componentPos as $key=>$val)
            {
                //得到下拉框的数据
                $componentDetail = MaterialEncodeRule::find()->where(['lvl'=>3,'root'=>$val->root])->andWhere(['>','lft',$val->lft])
                    ->andWhere(['<','rgt',$val->rgt])->select('id,name,remark')->OrderBy('lft')->asArray()->all();
                if($class2data->id == 205)//conn特殊处理
                {//只要这几个元素有值$dataDropMsg[0],[1],[4],[5]。与其它不一样
                    $len = strlen($componentDetail[0]['remark']);
                    if($key == 0)
                    {
                        $dataDropMsg[0] = $componentDetail;
                        $model->mer1 = substr($zcPartNum,0,1);
                        $zcPartNum = substr($zcPartNum,4);
                        array_unshift($dataDropMsg[$key],['remark'=>'','name'=>'请选择'.$val->name.'...']);
                    }
                    else if($key == 1)
                    {
                        $dataDropMsg[1] = $componentDetail;
                        array_unshift($dataDropMsg[$key],['remark'=>'','name'=>'请选择'.$val->name.'...']);
                        $mer = 'mer'.++$key;
                        $model->$mer = substr($zcPartNum,0,$len);
                        $zcPartNum = substr($zcPartNum,$len);
                    }
                    else if($key > 1)
                    {
                        $key = $key+2;
                        $dataDropMsg[$key] = $componentDetail;
                        array_unshift($dataDropMsg[$key],['remark'=>'','name'=>'请选择'.$val->name.'...']);
                        $mer = 'mer'.++$key;
                        $model->$mer = substr($zcPartNum,0,$len);
                        $zcPartNum = substr($zcPartNum,$len);
                    }
                    continue;
                }
                $dataDropMsg[] = $componentDetail;
                //解析智车料号

                if(empty($componentDetail))//是输入的值
                {
                    switch ($class2data->remark)
                    {
                        case 'RES':
                        case 'BEA':
                            $model->mer4 = substr($zcPartNum,0,4);
                            $zcPartNum = substr($zcPartNum,4);
                            break;
                        case 'CAP':
                        case 'IND':
                            $model->mer4 = substr($zcPartNum,0,3);
                            $zcPartNum = substr($zcPartNum,3);
                            break;
                    }
                }
                else if($key<6)//下拉框
                {
                    $len = strlen($componentDetail[0]['remark']);
                    $arrTemp = ArrayHelper::map($componentDetail, 'remark', 'name');
                    if($key == 0)//第一位是组装区域
                    {
                        $model->mer1 = substr($zcPartNum,0,1);
                        $zcPartNum = substr($zcPartNum,4);
                        array_unshift($dataDropMsg[$key],['remark'=>'','name'=>'请选择'.$val->name.'...']);
                    }
                    else if($key != 3)
                    {
                        array_unshift($dataDropMsg[$key],['remark'=>'','name'=>'请选择'.$val->name.'...']);
                        $mer = 'mer'.++$key;
                        $model->$mer = substr($zcPartNum,0,$len);
                        $zcPartNum = substr($zcPartNum,$len);
                    }

                }
            }
            //厂家
            $classManufacturer =end($dataDropMsg);
            array_unshift($classManufacturer,['id'=>'','name'=>'请选择厂家...']);
        }
        //二三四供
        $model->mfrPartNo2 = empty($model->manufacturer2_id)?"":$model->manufacturer2->zc_part_number;
        $model->mfrPartNo3 = empty($model->manufacturer3_id)?"":$model->manufacturer3->zc_part_number;
        $model->mfrPartNo4 = empty($model->manufacturer4_id)?"":$model->manufacturer4->zc_part_number;

        $dataUser = self::getUserApprover();

        //附件
        $dataAttachmentNew = new ActiveDataProvider([
            'query' => MaterialAttachment::find()->where(['modify_material_id'=>$model->id])
        ]);
        $idTemp = -2;
        if(!empty($model->material_id))//防止为null时
            $idTemp = $model->material_id;
        $dataAttachmentOld = new ActiveDataProvider([
            'query' => MaterialAttachment::find()->
            where(['material_id'=>$idTemp])->andWhere('modify_material_id<>-1')
        ]);

        //文件类名
        $fileClassName = ProjectProcessTemplate::find()->where(['lvl'=>3])->select('name')->indexby('name')->column();
        //所有物料
        $allMtr = Materials::find()->select('zc_part_number,material_id')->indexby('material_id')->column();


        return $this->render('update', [
            'model' => $model,
            'class1' => $class1,
            'class2' => $class2,
            'manufacturer'=>empty($classManufacturer)?"":$classManufacturer,
            'dataDropMsg'=>$dataDropMsg,
            'dataUser'=>$dataUser,
            'dataAttachmentNew'=>$dataAttachmentNew,
            'dataAttachmentOld'=>$dataAttachmentOld,
            'fileClassName'=>$fileClassName,
            'allMtr'=>$allMtr
        ]);
    }

    /*
     * 检查物料是否满足升级条件
     */
    public function actionCheckMtrUpgrade($id)
    {
        $model = Materials::findOne($id);
        $mdlMtrEncodeRule = MaterialEncodeRule::findOne($model->part_type);
        if($mdlMtrEncodeRule->getFather()->getFather()->getFather()->id != 612)
            return json_encode(['status' => 0, 'message' => '该物料不支持更新功能，请选择其它物料', 'data' => '']);
        if($model->maxVersion != $model->material_id)
            return json_encode(['status' => 0, 'message' => '请选择最高版本升级', 'data' => '']);

        return json_encode(['status' => 1, 'message' => '', 'data' => '']);
    }

    /*
     * 物料升级
     */
    public function actionMtrUpgrade($id)
    {
        $mdlMaterial = Materials::findOne($id);
        $model = new ModifyMaterial();
        $model->attributes = $mdlMaterial->attributes;
        $model->material_id = null;
        $partType = MaterialEncodeRule::findOne($model->part_type);
        $model->class1 = MaterialEncodeRule::find()->where(['root'=>$partType->root,'lvl'=>0])->select('id')->all()[0]->id;
        $class2data = MaterialEncodeRule::find()->where(['lvl'=>1,'root'=>$partType->root])->andWhere(['<','lft',$partType->lft])
            ->andWhere(['>','rgt',$partType->rgt])->select('id,lft,rgt,remark')->all()[0];
        $model->class2 = $class2data->id;
        //一类
        $class1 = MaterialEncodeRule::find()->select('name,root')->where(['lvl'=>0])->OrderBy('root')->all();
        //二类
        $class2 = MaterialEncodeRule::find()->select('name,id')->where(['lvl'=>1,'root'=>$partType->root])->all();
        //以下是形成智车料号的下拉框
        $dataDropMsg = [];
        $componentPos = MaterialEncodeRule::find()->where(['lvl'=>2,'root'=>$partType->root])->andWhere(['>','lft',$class2data->lft])
            ->andWhere(['<','rgt',$class2data->rgt])->OrderBy('lft')->select('name,root,lft,rgt')->all();//三级分类

        $zcPartNum = $model->zc_part_number = ExtBomsParent::upgradeZcPartNo($model->zc_part_number);
        if(strtolower($class2data->remark) == 'pcba'||strtolower($class2data->remark) == 'pcb')
        {//如果是pcb的，只给后面的几个input框分配值
            $model->mer4 = substr($zcPartNum,1,1);
            $model->mer7 = substr($zcPartNum,2,4);
            $model->mer8 = substr($zcPartNum,6,2);
            $model->mer9 = substr($zcPartNum,8,2);

        }
        else//其它要给下拉框分配值
        {
            foreach ($componentPos as $key=>$val)
            {
                //得到下拉框的数据
                $componentDetail = MaterialEncodeRule::find()->where(['lvl'=>3,'root'=>$val->root])->andWhere(['>','lft',$val->lft])
                    ->andWhere(['<','rgt',$val->rgt])->select('id,name,remark')->OrderBy('lft')->asArray()->all();
                if($class2data->id == 205)//conn特殊处理
                {//只要这几个元素有值$dataDropMsg[0],[1],[4],[5]。与其它不一样
                    $len = strlen($componentDetail[0]['remark']);
                    if($key == 0)
                    {
                        $dataDropMsg[0] = $componentDetail;
                        $model->mer1 = substr($zcPartNum,0,1);
                        $zcPartNum = substr($zcPartNum,4);
                        array_unshift($dataDropMsg[$key],['remark'=>'','name'=>'请选择'.$val->name.'...']);
                    }
                    else if($key == 1)
                    {
                        $dataDropMsg[1] = $componentDetail;
                        array_unshift($dataDropMsg[$key],['remark'=>'','name'=>'请选择'.$val->name.'...']);
                        $mer = 'mer'.++$key;
                        $model->$mer = substr($zcPartNum,0,$len);
                        $zcPartNum = substr($zcPartNum,$len);
                    }
                    else if($key > 1)
                    {
                        $key = $key+2;
                        $dataDropMsg[$key] = $componentDetail;
                        array_unshift($dataDropMsg[$key],['remark'=>'','name'=>'请选择'.$val->name.'...']);
                        $mer = 'mer'.++$key;
                        $model->$mer = substr($zcPartNum,0,$len);
                        $zcPartNum = substr($zcPartNum,$len);
                    }
                    continue;
                }
                $dataDropMsg[] = $componentDetail;
                //解析智车料号

                if(empty($componentDetail))//是输入的值
                {
                    switch ($class2data->remark)
                    {
                        case 'RES':
                        case 'BEA':
                            $model->mer4 = substr($zcPartNum,0,4);
                            $zcPartNum = substr($zcPartNum,4);
                            break;
                        case 'CAP':
                        case 'IND':
                            $model->mer4 = substr($zcPartNum,0,3);
                            $zcPartNum = substr($zcPartNum,3);
                            break;
                    }
                }
                else if($key<6)//下拉框
                {
                    $len = strlen($componentDetail[0]['remark']);
                    $arrTemp = ArrayHelper::map($componentDetail, 'remark', 'name');
                    if($key == 0)//第一位是组装区域
                    {
                        $model->mer1 = substr($zcPartNum,0,1);
                        $zcPartNum = substr($zcPartNum,4);
                        array_unshift($dataDropMsg[$key],['remark'=>'','name'=>'请选择'.$val->name.'...']);
                    }
                    else if($key != 3)
                    {
                        array_unshift($dataDropMsg[$key],['remark' =>'','name'=>'请选择'.$val->name.'...']);
                        $mer = 'mer'.++$key;
                        $model->$mer = substr($zcPartNum,0,$len);
                        $zcPartNum = substr($zcPartNum,$len);
                    }

                }
            }
            //厂家
            $classManufacturer =end($dataDropMsg);
            array_unshift($classManufacturer,['id'=>'','name'=>'请选择厂家...']);
        }
        //二三四供
        $model->mfrPartNo2 = empty($model->manufacturer2_id)?"":$model->manufacturer2->zc_part_number;
        $model->mfrPartNo3 = empty($model->manufacturer3_id)?"":$model->manufacturer3->zc_part_number;
        $model->mfrPartNo4 = empty($model->manufacturer4_id)?"":$model->manufacturer4->zc_part_number;

        $dataUser = self::getUserApprover();

        //附件
//        $dataAttachment = json_encode(MaterialAttachment::find()->where(['material_id'=>$id])
//            ->andWhere('modify_material_id<>-1')->asArray()->all());
        $tbla = MaterialAttachment::find()->where(['material_id'=>$id])
            ->andWhere('modify_material_id<>-1')->select('max(version) as maxv,file_class_name')
            ->groupBy('file_class_name');
        $dataAttachment = json_encode(MaterialAttachment::find()->alias('mtrA')->where(['mtrA.material_id'=>$id])
            ->innerJoin(['tbla'=>$tbla],'tbla.maxv=mtrA.version and tbla.file_class_name = mtrA.file_class_name and mtrA.material_id='.$id)
            ->select('mtrA.file_class_name,mtrA.remark,mtrA.name,mtrA.id')->asArray()->all());
        //文件类名
        $fileClassName = ProjectProcessTemplate::find()->where(['lvl'=>3])->select('name')->indexby('name')->column();
        //所有物料
        $allMtr = Materials::find()->select('zc_part_number,material_id')->indexby('material_id')->column();

        return $this->render('mtr-upgrade', [
            'model' => $model,
            'class1' => $class1,
            'class2' => $class2,
            'manufacturer'=>empty($classManufacturer)?"":$classManufacturer,
            'dataDropMsg'=>$dataDropMsg,
            'dataUser'=>$dataUser,
            'dataAttachment'=>$dataAttachment,
            'fileClassName'=>$fileClassName,
            'allMtr'=>$allMtr
        ]);

    }


    /**
     * Deletes an existing ModifyMaterial model.
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
     * Finds the ModifyMaterial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ModifyMaterial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ModifyMaterial::findOne($id)) !== null) {
            $model->mfrPartNo2 = empty($model->manufacturer2_id)?"":$model->manufacturer2->zc_part_number;
            $model->mfrPartNo3 = empty($model->manufacturer3_id)?"":$model->manufacturer3->zc_part_number;
            $model->mfrPartNo4 = empty($model->manufacturer4_id)?"":$model->manufacturer4->zc_part_number;
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 通过选中第三级分类，确定其下面跟生成描述相关的各项（剩下所有的分类）;
     */
    public function actionGetDescriptionOptionsByClass3id($class3Val)
    {
        $component = MaterialEncodeRule::findOne($class3Val);
        $data = [];
        $status = 1;
        //得到三级分类下描述相关各项的名字
        $componentPos = MaterialEncodeRule::find()->where(['lvl'=>4,'root'=>$component->root])->andWhere(['>','lft',$component->lft])
            ->andWhere(['<','rgt',$component->rgt])->OrderBy('lft')->select('name,root,lft,rgt')->all();
        if(empty($componentPos))//没有数据返回
            return json_encode(['status'=>0,'message'=>'三级分类下没有生成描述相关的各项信息！找管理员添加！','data'=>$data]);

        //获得更深一层各下拉选项信息
        foreach ($componentPos as $key => $val)
        {
            $componentDetail = MaterialEncodeRule::find()->where(['lvl'=>5,'root'=>$val->root])->andWhere(['>','lft',$val->lft])
                ->andWhere(['<','rgt',$val->rgt])->select('id,name,remark')->OrderBy('lft')->all();
            if ($val->name == "无") {
                $val->name = $val->name . $key;
            }
            //环保等级暂时不用，故不创建环保等级这个数组;需要用时再讲该if语句删除
            if ($val->name == "环保等级") {
                continue;
            }
            $data[$val->name] = [];
            foreach ($componentDetail as $code)
            {
                $data[$val->name][] = ['id'=>$code->id,'remark'=>$code->remark,'name'=>$code->name];
            }
        }
        return json_encode(['status'=>$status,'message'=>'','data'=>$data]);
    }

    /**
     * 检测是否有相同的厂家料号
     */
    public function actionCheckMfrPartNo($mfrPartNo,$mfr)
    {
        $data = Materials::find()->select("manufacturer,mfr_part_number")->where(['mfr_part_number'=>$mfrPartNo])->all();
        if(empty($data))
            return json_encode(['status'=>0,'message'=>'','data'=>'']);
        $str = '';
        $status = 0;
        foreach ($data as $partNo)
        {
            if($partNo->manufacturer == $mfr)
                $status = 1;
            $str .= "<br/>厂家：".$partNo->manufacturer1->name;
        }
        return json_encode(['status'=>$status,'message'=>"输入的厂家料号".$data[0]->mfr_part_number."在库里已有相同的".$str,'data'=>'']);
    }

    /**
     * 反回一个料的所有厂家
     */
    public function actionGetMfrByPartno($mfrPartNo)
    {
        $data = Materials::find()->select("manufacturer,mfr_part_number")->where(['mfr_part_number'=>$mfrPartNo])->all();
        if(empty($data))
        {
            $data = ModifyMaterial::find()->select("manufacturer,mfr_part_number")->where(['mfr_part_number'=>$mfrPartNo])->all();
            if(empty($data))
                return json_encode(['status'=>0,'message'=>'','data'=>'']);
        }
        $str = '';
        foreach ($data as $partNo)
        {
            $str .= "<br/>厂家：".$partNo->manufacturer1->name;
        }
        return json_encode(['status'=>1,'message'=>"输入的厂家料号".$data[0]->mfr_part_number."在库里已有相同的".$str,'data'=>'']);
    }


    /*
     * 根据料号一部分名字返回流水号
     * 把物料库和
     */
    public function actionGetSerialNum($code,$num,$ispcb=false)
    {
        $materialLike1 = Materials::find()->select('zc_part_number')
            ->where(['like','zc_part_number',$code.'%',false])->column();

        $materialLike2 = Tasks::find()->leftJoin('modify_material','tasks.type_id=modify_material.id')->where('tasks.status<>3')
            ->andWhere(['tasks.type'=>[Tasks::TASK_TYPE_MTR_APPROVER1,Tasks::TASK_TYPE_MTR_APPROVER2,Tasks::TASK_TYPE_MATERIAL]])
            ->andWhere(['like','zc_part_number',$code.'%',false])
            ->select('modify_material.zc_part_number')->column();
        $materialLike = array_merge($materialLike1,$materialLike2);
        if(empty($materialLike))
            $count = 0;
        else{
            natsort($materialLike);
            if($ispcb){//pcb
                $count = substr(end($materialLike),-2,1)+1;
            }
            else
                $count = substr(end($materialLike),-$num,$num)+1;
        }
        return json_encode(['status'=>1,'message'=>'','data'=>$count]);
    }

    /*
     * 输入结构的智车料号，返回应该的流水号
     */
    public function actionGetStructSn($zcCode,$class2Val)
    {
        $materialLike1 = Materials::find()->select('zc_part_number')->
        where(['like','zc_part_number',$zcCode.'%',false])->column();

        $materialLike2 = Tasks::find()->leftJoin('modify_material','tasks.type_id=modify_material.id')->where('tasks.status<>3')
            ->andWhere(['tasks.type'=>[Tasks::TASK_TYPE_MTR_APPROVER1,Tasks::TASK_TYPE_MTR_APPROVER2,Tasks::TASK_TYPE_MATERIAL]])
            ->andWhere(['like','zc_part_number',$zcCode.'%',false])
            ->select('modify_material.zc_part_number')->column();

        $materialLike = array_merge($materialLike1,$materialLike2);
        if(empty($materialLike))
        {
            switch ($class2Val)
            {
                case 613://613部件
                    $sn = '0';
                    break;
                case 738://总成738
                case 1641://半品1641
                case 1827://半品1641
                    $sn = '0';
                    break;
                case 1664://其它1664
                case 1686://成品出货
                    $sn = '00';
                    break;
                case 1668://包装
                    $sn = '0000';
                    break;
            }
            return json_encode(['status'=>0,'message'=>'','data'=>$sn]);
        }
        else
        {
            $sn=1;//截取位数
            switch ($class2Val)
            {
                case 613://613部件
                    $sn = 1;
                    break;
                case 738://总成738
                case 1827://总成738
                case 1641://半品1641
                    $sn = 1;
                    break;
                case 1664://其它1664
                case 1686://成品出货
                    $sn = 2;
                    break;
                case 1668://包装1668
                    $sn = 4;
                    break;
            }
            foreach ($materialLike as $zcPartNos)
            {
                $arrTemp[] = substr($zcPartNos,-$sn,$sn);
            }
            $data = array_unique($arrTemp);
            natsort($data);
        }
        return json_encode(['status'=>1,'message'=>'','data'=>end($data)+1]);
    }

    /**
     * ajax通过一级分类找二级分类，返回前端
     */
    public function actionGetClass2ByClass1id($class1Val)
    {
        $data = MaterialEncodeRule::find()->select('name,id')->where(['root'=>$class1Val,'lvl'=>1])->OrderBy('lft')->asArray()->all();

        return json_encode(['status'=>1,'message'=>'','data'=>$data]);
    }

    /*
     * 通过选中第二级分类，确定第三级（剩下所有的分类）;
     */
    public function actionGetClass3ByClass2id($class2Val)
    {
        $component = MaterialEncodeRule::findOne($class2Val);
        $data = [];
        $status = 1;
        //如果是结构的物料,现在是做死的，不用规则里的数据
        if($component->getFather()->name == '结构')
            $status = 3;
        //得到三级分类的名字
        $componentPos = MaterialEncodeRule::find()->where(['lvl'=>2,'root'=>$component->root])->andWhere(['>','lft',$component->lft])
            ->andWhere(['<','rgt',$component->rgt])->OrderBy('lft')->select('name,root,lft,rgt')->all();
        if(empty($componentPos))//没有数据返回
            return json_encode(['status'=>0,'message'=>'没有三级分类！找管理员添加！','data'=>$data]);
        //如果是PCB和PCBA
        $componentName = strtolower($component->name);//物料的名字
        if($componentName == 'pcb'||$componentName == 'pcba')
            $status = 2;
        //其它物料
        foreach ($componentPos as $val)
        {
            $componentDetail = MaterialEncodeRule::find()->where(['lvl'=>3,'root'=>$val->root])->andWhere(['>','lft',$val->lft])
                ->andWhere(['<','rgt',$val->rgt])->OrderBy('lft')->select('id,name,remark')->all();
            $data[$val->name] = [];
            foreach ($componentDetail as $code)
            {
                $data[$val->name][] = ['id'=>$code->id,'remark'=>$code->remark,'name'=>$code->name];
            }
        }
        return json_encode(['status'=>$status,'message'=>'','data'=>$data]);
    }

    /**
     * 根据输入的厂家料号，获得这个料的其它数据get-part-data
     */
    public function actionGetPartData($zcPartNo)
    {
        $data = Materials::findOne(['zc_part_number'=>$zcPartNo]);
        if(empty($data))
            return json_encode(['status'=>0,'message'=>'','data'=>$data]);

        $arrMdl = $data->attributes;
        $zcPartNo2 = $data->manufacturer2;
        $zcPartNo3 = $data->manufacturer3;
        $zcPartNo4 = $data->manufacturer4;
        if(empty($zcPartNo2)){
            $arrMdl['zc_part_number2'] = null;
            $arrMdl['mfr_part_number2'] = null;
            $arrMdl['manufacturer2'] = null;
        }else{
            $arrMdl['zc_part_number2'] = $zcPartNo2->zc_part_number;
            $arrMdl['mfr_part_number2'] = $zcPartNo2->mfr_part_number;
            $arrMdl['manufacturer2'] = $zcPartNo2->manufacturer;
        }
        if(empty($zcPartNo3)){
            $arrMdl['zc_part_number3'] = null;
            $arrMdl['mfr_part_number3'] = null;
            $arrMdl['manufacturer3'] = null;
        }else{
            $arrMdl['zc_part_number3'] = $zcPartNo3->zc_part_number;
            $arrMdl['mfr_part_number3'] = $zcPartNo3->mfr_part_number;
            $arrMdl['manufacturer3'] = $zcPartNo3->manufacturer;
        }
        if(empty($zcPartNo4)){
            $arrMdl['zc_part_number4'] = null;
            $arrMdl['mfr_part_number4'] = null;
            $arrMdl['manufacturer4'] = null;
        }else{
            $arrMdl['zc_part_number4'] = $zcPartNo4->zc_part_number;
            $arrMdl['mfr_part_number4'] = $zcPartNo4->mfr_part_number;
            $arrMdl['manufacturer4'] = $zcPartNo4->manufacturer;
        }
        return json_encode(['status'=>1,'message'=>'',
            'data'=>['id'=>$data->material_id,'desc'=>$data->description,'model'=>$arrMdl]]);
    }

    /** 获得模具流水号
     * @param $zcCodePart:智车料号的前几位
     * @param $class2Val：613正常结构物料。614总成物料
     * @param $id：检第四位，还是第五位
     * @return string：ajax接口数据
     */
    public function actionGetStructMoju($zcCodePart,$class2Val,$id)
    {
        $materialLike1 = Materials::find()->select('zc_part_number')
            ->where(['like','zc_part_number',$zcCodePart.'%',false])->column();

        $materialLike2 = Tasks::find()->leftJoin('modify_material','tasks.type_id=modify_material.id')->where('tasks.status<>3')
            ->andWhere(['tasks.type'=>[Tasks::TASK_TYPE_MTR_APPROVER1,Tasks::TASK_TYPE_MTR_APPROVER2,Tasks::TASK_TYPE_MATERIAL]])
            ->andWhere(['like','zc_part_number',$zcCodePart.'%',false])
            ->select('modify_material.zc_part_number')->column();

        $materialLike = array_merge($materialLike1,$materialLike2);

        $data=[];
        if(empty($materialLike))
            return json_encode(['status'=>0,'message'=>'没有匹配的料号','data'=>$data]);
        else
        {
            if($class2Val==613&&$id==4){
                $start = 6;$len = 3;
            }else if($class2Val==613&&$id==5){
                $start = 9;$len = 2;
            }else if($class2Val==738||$class2Val == 1827){
                $start = 6;$len = 3;
            }else if($class2Val==1686){
                $start = 6;$len = 4;
            }else if($class2Val == 1641){
                $start = 7;$len = 4;
            }else if($class2Val == 1664){
                $start = 7;$len = 3;
            }
            foreach ($materialLike as $zcPartNos)
                $arrTemp[] = substr($zcPartNos,$start,$len);
            //重新排序
            $data = array_unique($arrTemp);
            natsort($data);
        }
        return json_encode(['status'=>1,'message'=>'','data'=>array_values($data)]);
    }


    /**
     * 检测是否有相同的一供智车料号
     */
    public function actionCheckZcPartNo($zcPartNo)
    {
        $count = Materials::find()->where(['zc_part_number'=>$zcPartNo])->count();
        if ($count == 0)
            return json_encode(['status'=>1,'message'=>'','data'=>$count]);
        else
            return json_encode(['status'=>0,'message'=>"生成的一供智车料号".$zcPartNo."在库里已存在",'data'=>$count]);
    }

    /**
     * excel导入数据库(结构物料库)
     */
    public function actionUploadExcelStruct()
    {
        //echo "你没权限用这个功能";die;
        //把文件放到入口文件目录，然后执行这个函数
        $file = "jiegou.xlsx";//文件名
        $transaction = Yii::$app->getDb()->beginTransaction();
        $objReader = new \PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($file);
        $hightTable = $objPHPExcel->getSheetCount();
        for($i=0;$i<$hightTable;$i++)
        {
            $objWorksheet = $objPHPExcel->getSheet($i);
            $highestRow = $objWorksheet->getHighestRow();//最大行数，为数字

            $component = MaterialEncodeRule::find()->where(['lvl'=>3,'name'=>htmlspecialchars($objWorksheet->getTitle())])
                ->select('id,root,lft,rgt,lvl,name')->one();
            if(empty($component))
            {
                var_dump('没有这个part_type:'.$objWorksheet->getTitle());
                die;
            }
            $componentFa = $component->getFather()->getFather()->getSon();
            $data = [];
            //把这个料的分类对应该的数字都找出来
            foreach ($componentFa as $value)
            {
                if($value->name == '厂家')
                {
                    foreach ($value->getSon() as $val)
                    {
                        $data[$val->name] = $val->id;
                    }
                }
            }
            //采购等级，只有总成是M
            $purchaseLvl = 'P';
            if($component->name == '总成')
                $purchaseLvl = 'M';
            //生成带数据的SQL语句,并执行
            $strSQL = "INSERT INTO materials(is_first_mfr,assy_level,purchase_level,description,
manufacturer,zc_part_number,part_type,recommend_purchase,remark,part_name,unit) VALUES";

            for($row = 2;$row<=$highestRow;$row++){//得到每行有用的数据存到sql语句里
                $description = $objWorksheet->getCellByColumnAndRow(3,$row)->getValue();
                if(empty(trim($description)))//如果为空说明下面是空行了。
                    break;
                $zc_part_number = $objWorksheet->getCellByColumnAndRow(1,$row)->getValue();
                $part_name = $objWorksheet->getCellByColumnAndRow(2,$row)->getValue();
                $unit = $objWorksheet->getCellByColumnAndRow(4,$row)->getValue();
                $part_type = $component->id;
                $mfr='null';

                if(!empty($objWorksheet->getCellByColumnAndRow(5,$row)->getValue()))
                    $mfr = $data[$objWorksheet->getCellByColumnAndRow(5,$row)->__toString()];
                $remark = $objWorksheet->getCellByColumnAndRow(6,$row)->getValue();
                $strSQL .= "(0,3,'$purchaseLvl','$description',"."$mfr".",'$zc_part_number',".$part_type.",0,'$remark','$part_name','$unit'),";
            }
            if($row == 2)//如果这个表为空就不执行sql语句
                continue;
            //echo $strSQL;die;
            //加一供数据
            try{
                if(!Yii::$app->db->createCommand(trim($strSQL,','))->execute())
                {
                    $transaction->rollBack();
                    var_dump('上传出错！');
                    return;
                }
            }
            catch (Exception $e){
                //echo trim($strSQL,',');

                $transaction->rollBack();
                var_dump('表：'.$objWorksheet->getTitle().'的第'.$row.'行有错');
                var_dump($e->getMessage());
                die;
            }
        }
        var_dump('成功');
        $transaction->commit();
    }

    /**
     * excel导入数据库(电子物料库)
     */
    public function actionUploadExcelElectron()
    {
        echo "你没权限用这个功能";die;
        //把文件放到入口文件目录，然后执行这个函数
        $file = "dianzi.xlsx";//文件名
        //$file = "xinjia.xlsx";//文件名
        $arrPartType = [];//键是part_type如：RES Sample,值是元器件值如：RES
        $transaction = Yii::$app->getDb()->beginTransaction();
        $objReader = new \PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($file);
        $hightTable = $objPHPExcel->getSheetCount();
        /*1.导一供******************************************************/
        for($i=0;$i<$hightTable;$i++)
        {
            $objWorksheet = $objPHPExcel->getSheet($i);
            $highestRow = $objWorksheet->getHighestRow();//最大行数，为数字
            //生成part_type数据
            $component = MaterialEncodeRule::find()->where(['lvl'=>3,'name'=>$objWorksheet->getCellByColumnAndRow(22,2)->getValue()])
                ->select('id,root,lft,rgt,lvl,name')->one()->getFather()->getFather();
            $data = [];
            //把这个料的分类和厂家对应该的数字都找出来
            if($component->name == 'PCBA'||$component->name == 'PCB')//如果是pcb或是pcba,因为没有厂家这么搞
            {
                $data[0]['PCBA'] = 453;
                $data[0]['PCB'] = 1093;
                $arrPartType['PCBA'] = 'PCBA';
                $arrPartType['PCB'] = 'PCB';
            }
            else//其它的要找到厂家和分类
            {
                foreach ($component->getSon() as $value)
                {
                    if(strpos($value->name,'分类')!==false)//说明这是分类
                    {
                        foreach ($value->getSon() as $val)
                        {
                            $data[0][$val->name] = $val->id;
                            $arrPartType[$val->name] = $component->name;
                        }
                    }
                    else if(strpos($value->name,'厂家')!==false)//得到厂家
                    {
                        foreach ($value->getSon() as $val)
                        {
                            $data[1][$val->name] = $val->id;
                        }
                    }
                }
            }
            //生成带数据的SQL语句,并执行
            $strSQL = "INSERT INTO materials(is_first_mfr,assy_level,purchase_level,mfr_part_number,description,pcb_footprint,
manufacturer,zc_part_number,date_entered,vehicle_standard,part_type,value,schematic_part,recommend_purchase) VALUES";

            for($row = 2;$row<=$highestRow;$row++){//得到每行有用的数据存到sql语句里
                $purchase_level = $objWorksheet->getCellByColumnAndRow(7,$row)->getValue();
                $mfr_part_number = $objWorksheet->getCellByColumnAndRow(8,$row)->getValue();
                $description = $objWorksheet->getCellByColumnAndRow(9,$row)->getValue();
                $pcb_footprint = $objWorksheet->getCellByColumnAndRow(10,$row)->getValue();
                $zc_part_number = $objWorksheet->getCellByColumnAndRow(12,$row)->getValue();
                $date_entered = $objWorksheet->getCellByColumnAndRow(20,$row)->getValue();
                $vehicle_standard = $objWorksheet->getCellByColumnAndRow(21,$row)->getValue()=='Y'?2:3;
                $value = $objWorksheet->getCellByColumnAndRow(23,$row)->getValue();
                $schematic_part = $objWorksheet->getCellByColumnAndRow(24,$row)->getValue();
                $schematic_part = str_replace("\\","\\\\",$schematic_part);
                $manufacturer = $objWorksheet->getCellByColumnAndRow(11,$row)->getValue();
                $part_type = $objWorksheet->getCellByColumnAndRow(22,$row)->getValue();
                if(empty(trim($mfr_part_number)))//如果为空说明下面是空行了。
                    break;
                $mfr = empty($data[1])?'null':$data[1][$manufacturer];//如果是pcb或是pcba;
                $strSQL .= "(1,3,'$purchase_level','$mfr_part_number','$description','$pcb_footprint',".$mfr.",
                '$zc_part_number','$date_entered','$vehicle_standard',".$data[0][$part_type].",'$value','$schematic_part',0),";
            }
            //加一供数据
            if(!Yii::$app->db->createCommand(trim($strSQL,','))->execute())
            {
                $transaction->rollBack();
                var_dump('加一供时出错');
                return;
            }
        }

        /*2.导二供******************************************************/
        for($i=0;$i<$hightTable;$i++)
        {
            $objWorksheet = $objPHPExcel->getSheet($i);
            $highestRow = $objWorksheet->getHighestRow();//最大行数，为数字
            //生成part_type数据
            $component = MaterialEncodeRule::find()->where(['lvl'=>3,'name'=>$objWorksheet->getCellByColumnAndRow(22,2)->getValue()])
                ->select('id,root,lft,rgt,lvl,name')->one()->getFather()->getFather();
            $data = [];
            //把这个料的分类和厂家对应该的数字都找出来
            foreach ($component->getSon() as $value)
            {
                if(strpos($value->name,'厂家')!==false)//得到厂家
                {
                    foreach ($value->getSon() as $val)
                    {
                        $data[$val->name] = $val->id;
                    }
                }
            }
            for($row = 2;$row<=$highestRow;$row++){
                //找到二供ID
                $mfrPartNO2 = $objWorksheet->getCellByColumnAndRow(13,$row)->getValue();
                $mfrPartNO1 = $objWorksheet->getCellByColumnAndRow(8,$row)->getValue();
                if(empty(trim($mfrPartNO1)))//如果为空说明下面是空行了。
                    break;
                if(!empty(trim($mfrPartNO2)))//如果有二供
                {
                    $mfr2 = $objWorksheet->getCellByColumnAndRow(15,$row)->getValue();
//                    if(empty($data[$mfr2]))
//                    {
//                        var_dump($mfr2);
//                        var_dump($mfrPartNO1);
//                        var_dump($mfrPartNO2);
//                        die;
//                    }
                    $model2 = Materials::findOne(['mfr_part_number'=>$mfrPartNO2,'manufacturer'=>$data[$mfr2]]);
                    $model2->is_first_mfr = 0;
                    if(!$model2->save())//把这个料的是否是一供的属性改一下
                    {
                        $transaction->rollBack();
                        var_dump('加二供时改属性出错');
                        return;
                    }
                    //找到一供的料
                    $mfr1 = $objWorksheet->getCellByColumnAndRow(11,$row)->getValue();
                    $model1 = Materials::findOne(['mfr_part_number'=>$mfrPartNO1,'manufacturer'=>$data[$mfr1]]);
                    //给这个料添加二供
                    $model1->manufacturer2_id = $model2->material_id;
                    if(!$model1->save())
                    {
                        $transaction->rollBack();
                        var_dump('加二供时出错');
                        return;
                    }
                }
            }
        }
        /*3.同步到研发库******************************************************/
        $mts = Materials::find()->select("*")->where("material_id>0")->all();
        //提取二三四供的信息
        $tables = UserTask::$tableName;
        foreach ($mts as $key=>$mt) {
            //如果是二供就不导了
            if($mt->is_first_mfr == 0)
                continue;
            $partTypeName = MaterialEncodeRule::findOne($mt->part_type)->name;
            $hardwareTable = $tables[strtolower(trim($arrPartType[trim(MaterialEncodeRule::findOne($mt->part_type)->name)]))];
            $hardwareNcTable = $hardwareTable . 'Nc';
            $hardwareModel = new $hardwareTable;
            $hardwareNcModel = new $hardwareNcTable;

            //提取二三四供的信息
            $data234Mfr = [2 => ['mfrPartNo' => '', 'zcPartNo' => '', 'mfr' => '', 'des' => ''], 3 => ['mfrPartNo' => '', 'zcPartNo' => '', 'mfr' => '', 'des' => ''],
                4 => ['mfrPartNo' => '', 'zcPartNo' => '', 'mfr' => '', 'des' => '']];

            if (!empty($mt->manufacturer2_id)) {
                $dataTemp = Materials::findOne($mt->manufacturer2_id);
                $data234Mfr[2]['mfrPartNo'] = $dataTemp->mfr_part_number;
                $data234Mfr[2]['zcPartNo'] = $dataTemp->zc_part_number;
                $data234Mfr[2]['des'] = $dataTemp->description;
                $data234Mfr[2]['mfr'] = $dataTemp->manufacturer1->name;
            }
            if (!empty($mt->manufacturer3_id)) {
                $dataTemp = Materials::findOne($mt->manufacturer3_id);
                $data234Mfr[3]['mfrPartNo'] = $dataTemp->mfr_part_number;
                $data234Mfr[3]['zcPartNo'] = $dataTemp->zc_part_number;
                $data234Mfr[3]['des'] = $dataTemp->description;
                $data234Mfr[3]['mfr'] = $dataTemp->manufacturer1->name;
            }
            if (!empty($mt->manufacturer4_id)) {
                $dataTemp = Materials::findOne($mt->manufacturer4_id);
                $data234Mfr[4]['mfrPartNo'] = $dataTemp->mfr_part_number;
                $data234Mfr[4]['zcPartNo'] = $dataTemp->zc_part_number;
                $data234Mfr[4]['des'] = $dataTemp->description;
                $data234Mfr[4]['mfr'] = $dataTemp->manufacturer1->name;
            }
            //同步不NC的数据
            $hardwareModel->setAttributes([
                "Assy_Level" => $mt->assy_level,
                "Purchase_Level" => $mt->purchase_level,
                "Mfr_part_number" => $mt->mfr_part_number,
                "Description" => $mt->description,
                "Allegro_PCB_Footprint" => $mt->pcb_footprint,
                "Manufacturer" => empty($mt->manufacturer) ? " " : MaterialEncodeRule::findOne($mt->manufacturer)->name,
                "zc_part_number" => $mt->zc_part_number,
                "2Mfr_part_number" => $data234Mfr[2]['mfrPartNo'],
                "2zc_part_number" => $data234Mfr[2]['zcPartNo'],
                "2Manufacturer" => $data234Mfr[2]['mfr'],
                "2Description" => $data234Mfr[2]['des'],
                "3Mfr_part_number" => $data234Mfr[3]['mfrPartNo'],
                "3zc_part_number" => $data234Mfr[3]['zcPartNo'],
                "3Manufacturer" => $data234Mfr[3]['mfr'],
                "3Description" => $data234Mfr[3]['des'],
                "4Mfr_part_number" => $data234Mfr[4]['mfrPartNo'],
                "4zc_part_number" => $data234Mfr[4]['zcPartNo'],
                "4Manufacturer" => $data234Mfr[4]['mfr'],
                "4Description" => $data234Mfr[4]['des'],
                "Version" => date("Y-m-d H:i:s", time()),
                "Automotive" => $mt::VEHICLE_STANDARD[$mt->vehicle_standard],
                "Part_type" => $partTypeName,
                "Value" => $mt->value,
                "Schematic_part" => $mt->schematic_part,
//            "Datasheet"=>"",
                "Price" => $mt->price,
                "recommend_purchase" => $mt::RECOMMEND_PURCHASE[$mt->recommend_purchase],
                "minimum_packing_quantity" => $mt->minimum_packing_quantity,
                "lead_time" => $mt->lead_time,
            ]);
            if (!$hardwareModel->save())
            {
                $transaction->rollBack();
                var_dump('同步时出错');
                return;
            }

            //同步NC数据
            $hardwareNcModel->setAttributes([
                "Assy_Level" => $mt->assy_level,
                "Purchase_Level" => $mt->purchase_level,
                "Mfr_part_number" => $mt->mfr_part_number,
                "Description" => $mt->description,
                "Allegro_PCB_Footprint" => $mt->pcb_footprint,
                "Manufacturer" => empty($mt->manufacturer) ? " " : MaterialEncodeRule::findOne($mt->manufacturer)->name,
                "zc_part_number" => $mt->zc_part_number,
                "2Mfr_part_number" => $data234Mfr[2]['mfrPartNo'],
                "2zc_part_number" => $data234Mfr[2]['zcPartNo'],
                "2Manufacturer" => $data234Mfr[2]['mfr'],
                "2Description" => $data234Mfr[2]['des'],
                "3Mfr_part_number" => $data234Mfr[3]['mfrPartNo'],
                "3zc_part_number" => $data234Mfr[3]['zcPartNo'],
                "3Manufacturer" => $data234Mfr[3]['mfr'],
                "3Description" => $data234Mfr[3]['des'],
                "4Mfr_part_number" => $data234Mfr[4]['mfrPartNo'],
                "4zc_part_number" => $data234Mfr[4]['zcPartNo'],
                "4Manufacturer" => $data234Mfr[4]['mfr'],
                "4Description" => $data234Mfr[4]['des'],
                "Version" => date("Y-m-d H:i:s", time()),
                "Automotive" => $mt::VEHICLE_STANDARD[$mt->vehicle_standard],
                "Part_type" => $partTypeName,
                "Value" => "NC(" . $mt->value . ")",
                "Schematic_part" => $mt->schematic_part,
//            "Datasheet"=>$mt->datasheet,
                "Price" => $mt->price,
                "recommend_purchase" => $mt::RECOMMEND_PURCHASE[$mt->recommend_purchase],
                "minimum_packing_quantity" => $mt->minimum_packing_quantity,
                "lead_time" => $mt->lead_time,
            ]);
            if (!$hardwareNcModel->save())
            {
                $transaction->rollBack();
                var_dump('同步时出错');
                return;
            }
        }
        /*4.如果返回成功才算导入成功*********************************************/
        var_dump('成功');
        $transaction->commit();
    }

    /**
     * 物料导出
     */
    public function actionExportMaterial()
    {
        return $this->render('export-material');
    }



    /**
     * 把电子物料导出execl表格
     */
    public function actionWriteExcelElec($excelName = "电子物料.xlsx")
    {
        //第一行的表头
        $headerArr = ['Item','Assy_Level','Purchase_Level','Mfr_part_number'
            ,'Description','Allegro_PCB_Footprint','Manufacturer','zc_part_number','2Mfr_part_number','2zc_part_number',
            '2Manufacturer','2Description','3Mfr_part_number','3zc_part_number','3Manufacturer','3Description',
            '4Mfr_part_number','4zc_part_number','4Manufacturer','4Description','Version','Automotive','Part_type'
            ,'Value','Schematic_part','Datasheet','Price','recommend_purchase','minimum_packing_quantity','lead_time'];
        //所有的表的名字
        $tableName = UserTask::$tableName;
        array_pop($tableName);
        $objPHPExcel = new \PHPExcel();
        $i=0;
        foreach ($tableName as $key=>$hardwareTable)
        {
            $objPHPExcel->createSheet($i);
            if($key=='module/ic')
                $key = 'module ic';//不允许出现‘/’这个字符
            if($key == 'pcba')
                $key = 'pcba pcb';
            $objPHPExcel->setActiveSheetIndex($i);
            $objPHPExcel->getActiveSheet()->setTitle(strtoupper($key));
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
            $hardwareModel = $hardwareTable::find()->all();
            $row = 2;
            foreach ($hardwareModel as $material)
            {
                $cells = 0;
                foreach ($headerArr as $v)
                {
                    $colum = \PHPExcel_Cell::stringFromColumnIndex($cells);
                    $objPHPExcel->getActiveSheet()->setCellValue($colum.$row,$material->$v);
                    $cells++;
                }
                $row++;
            }
            $i++;
            //第nc的一行
            $objPHPExcel->createSheet($i);
            if($key=='module/ic')
                $key = 'module ic';//不允许出现‘/’这个字符
            $objPHPExcel->setActiveSheetIndex($i);
            $objPHPExcel->getActiveSheet()->setTitle(strtoupper($key." NC"));

            $cells = 0;
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
            //加nc的数据，去对应的数据库
            $hardwareTable = $hardwareTable."Nc";
            $hardwareModel = $hardwareTable::find()->all();
            $row = 2;
            foreach ($hardwareModel as $material)
            {
                $cells = 0;
                foreach ($headerArr as $v)
                {
                    $colum = \PHPExcel_Cell::stringFromColumnIndex($cells);
                    $objPHPExcel->getActiveSheet()->setCellValue($colum.$row,$material->$v);
                    $cells++;
                }
                $row++;
            }
            $i++;
        }

        //下载
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$excelName\"");
        header('Cache-Control: max-age=0');
        $writer = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        $writer->save('php://output');
    }

    /**
     * 把物外购物料导出execl表格
     */
    public function actionWriteExcelWaigou()
    {
        self::writeExcelJiegou('外购物料.xlsx',['外购部件']);
    }

    /**
     * 把物外购物料导出execl表格
     */
    public function actionWriteExcelStruct()
    {
        $arrData = ['塑胶件','钣金件','压铸件','线材','FFC&FPC','触摸屏','液晶屏','泡棉类','导热凝胶类',
            '导电布&防尘网','耗材类','自攻螺钉','机牙螺钉','其它','总成','虚拟半品','半品直接出货','成品', '包材','扬声器'];
        self::writeExcelJiegou('结构物料.xlsx',$arrData);
    }

    /**
     * @param $excelName:下载下来的excel表的名字
     * @param $partTypes:下载的part_type
     */
    public function writeExcelJiegou($excelName,$partTypes)
    {
        //第一行的表头
        $headerArr = ['采购等级','智车料号','part_name','描述','unit','厂家',
            '采购推荐级别','备注','二供智车料号','二供厂家'];
        //所有的表的名字
        $objPHPExcel = new \PHPExcel();
        foreach ($partTypes as $part_type)
        {

            $component = MaterialEncodeRule::find()->where(['lvl'=>3,'name'=>htmlspecialchars($part_type)])
                ->select('id,root,lft,rgt,lvl,name')->one();
            if(empty($component))
            {

                var_dump('没有这个part_type:'.$part_type);
                die;
            }
            $componentFa = $component->getFather()->getFather()->getSon();
            $arrMfr = [];
            //把这个料的分类对应该的数字都找出来
            foreach ($componentFa as $value) {
                if ($value->name == '厂家') {
                    foreach ($value->getSon() as $val) {
                        $arrMfr[$val->id] = $val->name;
                    }
                }
            }

            $objPHPExcel->createSheet(0);
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setTitle($part_type);
            $cells = 0;
            //第不nc的一行
            foreach ($headerArr as $v) {
                $colum = \PHPExcel_Cell::stringFromColumnIndex($cells);
                //字体
                $objPHPExcel->getActiveSheet()->setCellValue($colum . '1', $v)->getStyle($colum . '1')
                    ->getFont()->setSize(10);
                //宽度
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setAutoSize(true);
                //设置背景色
                $objPHPExcel->getActiveSheet()->getStyle($colum . '1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $objPHPExcel->getActiveSheet()->getStyle($colum . '1')->getFill()->getStartColor()->setRGB('C5D9F1');
                $cells++;
            }
            //加不nc的数据，去对应的数据库
            $mdlWaigou = Materials::find()->alias('mtr1')->where(['mtr1.part_type' => $component->id])->
            leftJoin(['mtr2'=>'materials'],'mtr1.manufacturer2_id=mtr2.material_id')->
            select(['mtr1.purchase_level','mtr1.zc_part_number','mtr1.part_name','mtr1.description','mtr1.unit',
                'mtr1.manufacturer','mtr1.recommend_purchase','mtr1.remark','mtr2.zc_part_number as zc_part_number2',
                'mtr2.manufacturer as mfr2'])->asArray()->all();
            $row = 2;
            foreach ($mdlWaigou as $material)
            {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $material['purchase_level']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $material['zc_part_number']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $material['part_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $material['description']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $material['unit']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, empty($material['manufacturer'])?'':$arrMfr[$material['manufacturer']]);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, ModifyMaterial::RECOMMEND_PURCHASE[$material['recommend_purchase']]);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $material['remark']);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $material['zc_part_number2']);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, empty($material['mfr2'])?'':$arrMfr[$material['mfr2']]);
                $row++;
            }
        }
        //下载
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$excelName\"");
        header('Cache-Control: max-age=0');
        $writer = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        $writer->save('php://output');
    }

    /**
     * 删除附件
     */
    public function actionDeleteAttachment($id)
    {
        $delModel = MaterialAttachment::findOne($id);
        $isSuc = true;
        if(!$delModel->delete())
            $isSuc = false;
        $count = MaterialAttachment::find()->where(['path'=>$delModel->path])->count();
        if($count > 0)
            return json_encode(['status'=>$isSuc,'message'=>'','data'=>[]]);
        if(file_exists($delModel->path))
            unlink($delModel->path);
        return json_encode(['status'=>$isSuc,'message'=>'','data'=>[]]);
    }

    /**
     * 物料的模糊查找,上传物料的附件时,一个附件对应该多个物料时用
     */
    public function actionMaterialLike()
    {
        $mtrLike = $_POST['mtrLike'];
        $mdlMtr = Materials::find()->select('materials.zc_part_number,materials.mfr_part_number')
            ->leftJoin('material_attachment','material_attachment.material_id=materials.material_id')
            ->where('material_attachment.material_id is null')
            ->andWhere(['like','materials.mfr_part_number',$mtrLike.'%',false])->asArray()->all();
        return json_encode(['data'=>$mdlMtr]);

    }

    /**
     * 下载附件
     */
    public function actionDownload($pathFile,$filename)
    {
        if (file_exists($pathFile))
        {
            return Yii::$app->response->sendFile($pathFile, $filename);
        }
        var_dump('没有找到文件');die;
    }

    /**
     * 显示物料的所有附件的模态框
     */
    public function actionMtrFile()
    {
        $id = $_POST['id'];

        $dataProvider = new ActiveDataProvider([
            'query' => MaterialAttachment::find()->where(['material_id'=>$id])->andWhere('modify_material_id<>-1')->orderBy('version')
//            'query' => MaterialAttachment::find()->where(['material_attachment.material_id'=>$id])
//                ->leftJoin('materials','materials.material_id=material_attachment.material_id')
//                ->select('material_attachment.*,materials.zc_part_number as zc_part_number')
        ]);

        return $this->renderAjax('mtr-file', [
            'dataProvider' => $dataProvider,
        ]);

    }


    /**物料创建界面的模板下载
     * @param $id2
     * @param $id3
     */
    public function actionGetTemp($id2,$id3)
    {
        //第一行的表头
        $mdl2 = MaterialEncodeRule::findOne($id2);
        $tableName = $mdl2->name;
        $zcRule = $mdl2->getSon();
        foreach ($zcRule as $name){
            if($name->name!='厂家')
                $headerArr['智车料号'][] = $name->name;
        }
        $mdl3 = MaterialEncodeRule::findOne($id3);
        $desRule = $mdl3->getSon();
        foreach ($desRule as $name){
            if ($name->name!='环保等级')
                $headerArr['描述'][] = $name->name;
        }
        $headerArr['其它'] = ['是否可以当一供','采购等级','一供厂家料号','一供厂家','PCB Footprint',
            '物料级别','元器件值','元器件原理图路径','二供厂家料号','二供厂家'];
        //所有的表的名字
        $objPHPExcel = new \PHPExcel();
        $this->generateTable($objPHPExcel,$headerArr,$tableName);//第一个表，添物料数据
        //物料类ID,描述ID
        $objPHPExcel->getActiveSheet()->setCellValue('B1',$id2);
        $objPHPExcel->getActiveSheet()->setCellValue('C1',$id3);
        //第二个表
        $objPHPExcel->createSheet(1);
        $objPHPExcel->setActiveSheetIndex(1);
        $objPHPExcel->getActiveSheet()->setTitle('下拉框数据');
        $cells = 0;
        $colum = \PHPExcel_Cell::stringFromColumnIndex($cells);
        $objPHPExcel->getActiveSheet()->setCellValue($colum . 1,'智车料号');
        foreach ($zcRule as $rowData) {//智车料号
            $colum = \PHPExcel_Cell::stringFromColumnIndex($cells);
            $datas = $rowData->getSon();
            $objPHPExcel->getActiveSheet()->setCellValue($colum . 1,$rowData->name);
            if(strstr($rowData->name,'分类')){
                $objPHPExcel->getActiveSheet()->setCellValue($colum . 2,$mdl3->name);
            }else if(!empty($datas)){
                $row=2;
                foreach ($datas as $data){
                    $row++;
                    $objPHPExcel->getActiveSheet()->setCellValue($colum . $row,$data->name);
                }
            }
            $cells++;
        }
        $colum = \PHPExcel_Cell::stringFromColumnIndex($cells);
        $objPHPExcel->getActiveSheet()->setCellValue($colum . 1,'描述');
        foreach ($desRule as $rowData) {//描述
            $colum = \PHPExcel_Cell::stringFromColumnIndex($cells);
            $datas = $rowData->getSon();
            $objPHPExcel->getActiveSheet()->setCellValue($colum . 2,$rowData->name);
            if(!empty($datas)){
                $row=2;
                foreach ($datas as $data){
                    $row++;
                    $objPHPExcel->getActiveSheet()->setCellValue($colum . $row,$data->name);
                }
            }
            $cells++;
        }
        $colum = \PHPExcel_Cell::stringFromColumnIndex(--$cells);
        $objPHPExcel->getActiveSheet()->setCellValue($colum . 1,'是否可以当一供');
        $objPHPExcel->getActiveSheet()->setCellValue($colum . 2,'是');
        $objPHPExcel->getActiveSheet()->setCellValue($colum . 3,'否');
        $colum = \PHPExcel_Cell::stringFromColumnIndex(++$cells);
        $objPHPExcel->getActiveSheet()->setCellValue($colum . 1,'采购等级');
        $objPHPExcel->getActiveSheet()->setCellValue($colum . 2,'P');
        $objPHPExcel->getActiveSheet()->setCellValue($colum . 3,'M');
        $colum = \PHPExcel_Cell::stringFromColumnIndex(++$cells);
        $objPHPExcel->getActiveSheet()->setCellValue($colum . 1,'物料级别');
        $objPHPExcel->getActiveSheet()->setCellValue($colum . 2,'商业级');
        $objPHPExcel->getActiveSheet()->setCellValue($colum . 3,'工业级');
        $objPHPExcel->getActiveSheet()->setCellValue($colum . 4,'汽车级');


        //下载
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$tableName.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        $writer->save('php://output');
    }

    /**
     * 创建表页，生成表头
     */
    public function generateTable($objPHPExcel,$headerArr,$tableName)
    {
        $objPHPExcel->createSheet(0);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle($tableName);
        $cells = 0;
        //表头
        foreach ($headerArr as $key=>$items) {
            $colum = \PHPExcel_Cell::stringFromColumnIndex($cells);
            //字体
            $objPHPExcel->getActiveSheet()->setCellValue($colum . '1', $key)->getStyle($colum . '1')
                ->getFont()->setSize(10);
            //宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setAutoSize(true);
            //设置背景色
            $objPHPExcel->getActiveSheet()->getStyle($colum . '1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle($colum . '1')->getFill()->getStartColor()->setRGB('C5D9F1');
            foreach ($items as $value){
                $colum = \PHPExcel_Cell::stringFromColumnIndex($cells);
                $objPHPExcel->getActiveSheet()->setCellValue($colum . '2',$value);
                $objPHPExcel->getActiveSheet()->getStyle($colum . '2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $objPHPExcel->getActiveSheet()->getStyle($colum . '2')->getFill()->getStartColor()->setRGB('7AC5CD');

                $cells++;
            }
        }
        return $objPHPExcel;
    }

    const FIELD = ['is_first_mfr','purchase_level','mfr_part_number','manufacturer','pcb_footprint',
        'vehicle_standard','value','schematic_part','manufacturer2_id'];
    /**
     * 电子物料批量导入
     */
    public function actionLeadMaterial()
    {
        $model = new ImportMtrForm();
        $errorMsg = [];//报错
        if (Yii::$app->request->isPost) {
            $arrAnalysis = $this->analysisMtrImport($model);
            /**@var mixed       $sheet
             * @var number       $highestRow
             * @var number $highestColumm
             * @var number $zcNum
             * @var number $desNum
             * @var number $id2
             * @var number $id3
             * @var array $ruleZc
             * @var array $arrMfr
             */
            extract($arrAnalysis);
            //var_dump($ruleZc,$zcNum,$desNum,$id2,$highestRow,$highestColumm);die;
            //把Excel数据保存数组中
            $transaction = Yii::$app->db->beginTransaction();//开启事务
            $arrScd = $this->getScdCode($id2);
            if($arrScd == false){
                $errorMsg['其它'][]='没有此物料规则';
            }
            $partType = MaterialEncodeRule::findOne($id3);
            $tblName = MaterialEncodeRule::findOne($id2)->name;
            $errorMsg = array_merge($errorMsg,$this->analysisRow($highestRow,$highestColumm,$sheet,$ruleZc,
                $zcNum,$arrScd,$desNum,$arrMfr,$partType,$tblName));
            if(empty($errorMsg)){
                $transaction->commit();
                return $this->render('upload-res',['errorMsg'=>$errorMsg]);
            }
            $transaction->rollBack();
            return $this->render('upload-res',['errorMsg'=>$errorMsg]);
        }
        return $this->render('lead-material',['model'=>$model]);
    }

    /**解析每行并赋值保存数据
     * 最大行@param $highestRow
     * 最大列@param $highestColumm
     * excel所有数据@param $sheet
     * excel合成智车料号的列数@param $zcNum
     * 合成智车料号的第二位是什么和流水位是多少@param $arrScd
     * excel合成描述的列数@param $desNum
     * 厂家数据@param $arrMfr
     * 物料类型数据@param $partType
     * 表名@param $tblName
     * @return array
     */
    public function analysisRow($highestRow,$highestColumm,$sheet,$ruleZc,$zcNum,$arrScd,$desNum,$arrMfr,$partType,$tblName)
    {
        $errorMsg = [];
        $ctl = new MaterialsController(1,(new Materials()));
        for($i=3;$i<=$highestRow;$i++){
            $strZc = $partName = '';
            $strDes = $partType->name;
            $mdl = new Materials();
            for ($j=0;$j<$highestColumm;$j++){
                $colum = \PHPExcel_Cell::stringFromColumnIndex($j);
                $cell = $sheet->getCell("$colum".$i)->getValue();
                if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                    $cell = trim($cell->__toString());
                }else
                    $cell = trim($cell);
                if($j<$zcNum){//智车料号
                    if(empty($ruleZc[$j])){
                        $strZc .= $cell;
                    }else if(!isset($ruleZc[$j][$cell])){//添加的在系统里不存在
                        $errorMsg['行'.$i][]='列'.$colum.'添加的选项在系统规则里不存在';
                    }else{
                        $strZc .= $ruleZc[$j][$cell];
                    }
                    if($j==0)
                        $strZc .= $arrScd[0];
                }else if($j>=$zcNum&&$j<$desNum){//描述
                    $strDes = $strDes.'_'.$cell;
                }else if($j>=$desNum){
                    $field = self::FIELD[$j-$desNum];
                    if($field == 'is_first_mfr') {//是否是一供
                        if ($cell == '是')
                            $mdl->$field = 1;
                        else if($cell == '否')
                            $mdl->$field = 0;
                        else
                            $errorMsg['行'.$i][]='列'.$colum.'应该添写是或否';
                    }else if($field == 'purchase_level'){//采购等级
                        if (strtoupper($cell) == 'P')
                            $mdl->$field = "P";
                        else if(strtoupper($cell) == 'M')
                            $mdl->$field = 'M';
                        else
                            $errorMsg['行'.$i][]='列'.$colum.'应该添写P或M';
                    }else if($field == 'manufacturer'){//厂家
                        if(empty($arrMfr[$cell]))
                            $errorMsg['行'.$i][]='列'.$colum.'添写的厂家不存在';
                        else
                            $mdl->$field = $arrMfr[$cell];
                        $colum1 = \PHPExcel_Cell::stringFromColumnIndex($j-1);
                        $mfrNo = $sheet->getCell("$colum1".$i)->getValue();
                        if ($mfrNo instanceof \PHPExcel_RichText) { //富文本转换字符串
                            $mfrNo = trim($mfrNo->__toString());
                        }
                        $mtr1 = Materials::find()->where(['mfr_part_number'=>$mfrNo,'manufacturer'=>$arrMfr[$cell]])->one();
                        if(!empty($mtr1))
                            $errorMsg['行'.$i][]='系统存在厂家料号和厂家了';
                    }else if($field == 'vehicle_standard'){//物料级别
                        if(empty($cell))
                            $mdl->$field = '';
                        else{
                            $veh = array_search($cell,Materials::VEHICLE_STANDARD);
                            if($veh === false)
                                $errorMsg['行'.$i][]='列'.$colum.'车规级不是三种之一';
                            else
                                $mdl->$field = $veh;
                        }
                    }else if($field == 'manufacturer2_id'){//二供
                        $colum1 = \PHPExcel_Cell::stringFromColumnIndex($j+1);
                        $mfrNo = $sheet->getCell("$colum1".$i)->getValue();
                        if ($mfrNo instanceof \PHPExcel_RichText) { //富文本转换字符串
                            $mfrNo = trim($mfrNo->__toString());
                        }
                        if(!empty($mfrNo)&&!empty($cell)){
                            $mtr2 = Materials::find()->where(['mfr_part_number'=>$cell,'manufacturer'=>$arrMfr[$mfrNo]])->one();
                            if(empty($mtr2))
                                $errorMsg['行'.$i][]='列'.$colum.'系统里没有这个料';
                            else
                                $mdl->$field = $mtr2->material_id;
                        }
                    }else
                        $mdl->$field = $cell;
                }
            }
            if(empty($errorMsg)){
                $objJson = $this->actionGetSerialNum($strZc,$arrScd[1]);
                $serialNum = json_decode($objJson)->data;
                $strZc .= $this->padZore($serialNum,$arrScd[1]);
                $mdl->zc_part_number = $strZc;
                $mdl->description = $strDes;
                $mdl->part_type = $partType->id;
                $mdl->recommend_purchase = 0;
                $mdl->assy_level = 3;
                $mdl->save();
                $mdl->parent_id = $mdl->material_id;
                $mdl->save();
                if($mdl->is_first_mfr == 1){
                    $isSuc = $this->syncMaterial($ctl->tableName[strtolower($tblName)],$mdl,true,$partType->name,$arrMfr);
                    if(!$isSuc)
                        $errorMsg['其它'][]='保存出错';
                }
            }
        }
        return $errorMsg;

    }

    /** 同步数据到研发库里
     * @param $tableName 同步到哪个研发数据库
     * @param $model 物料数据
     * @param $isCreate  是否是新建
     * @param $partTypeName 分类的名字
     * @return bool 是否保存成功，到时事务要用到返回值
     */
    public function syncMaterial($tableName,$model,$isCreate,$partTypeName,$arrMfr)
    {
        $className = $tableName;//插入的是哪个表
        $classNcName = $className."Nc";
        //更新还是插入
        if($isCreate)//insert
        {
            $hardwareModel = new $className();
            $hardwareNcModel = new $classNcName();
        }
        else//update
        {
            $hardwareModel = $className::findOne(['Mfr_part_number'=>$model->mfr_part_number]);
            $hardwareNcModel = $classNcName::findOne(['Mfr_part_number'=>$model->mfr_part_number]);
        }
        //提取二三四供的信息
        $data234Mfr = [2=>['mfrPartNo'=>'','zcPartNo'=>'','mfr'=>'','des'=>''],3=>['mfrPartNo'=>'','zcPartNo'=>'','mfr'=>'','des'=>''],
            4=>['mfrPartNo'=>'','zcPartNo'=>'','mfr'=>'','des'=>'']];

        if(!empty($model->manufacturer2_id))
        {
            $dataTemp = Materials::findOne($model->manufacturer2_id);
            $data234Mfr[2]['mfrPartNo']=$dataTemp->mfr_part_number;
            $data234Mfr[2]['zcPartNo']=$dataTemp->zc_part_number;
            $data234Mfr[2]['des']=$dataTemp->description;
            $data234Mfr[2]['mfr']=$dataTemp->manufacturer1->name;
        }
        if(!empty($model->manufacturer3_id))
        {
            $dataTemp = Materials::findOne($model->manufacturer3_id);
            $data234Mfr[3]['mfrPartNo']=$dataTemp->mfr_part_number;
            $data234Mfr[3]['zcPartNo']=$dataTemp->zc_part_number;
            $data234Mfr[3]['des']=$dataTemp->description;
            $data234Mfr[3]['mfr']=$dataTemp->manufacturer1->name;
        }
        if(!empty($model->manufacturer4_id))
        {
            $dataTemp = Materials::findOne($model->manufacturer4_id);
            $data234Mfr[4]['mfrPartNo']=$dataTemp->mfr_part_number;
            $data234Mfr[4]['zcPartNo']=$dataTemp->zc_part_number;
            $data234Mfr[4]['des']=$dataTemp->description;
            $data234Mfr[4]['mfr']=$dataTemp->manufacturer1->name;
        }

        //同步不NC的数据
        $hardwareModel->setAttributes([
            "Assy_Level"=>$model->assy_level,
            "Purchase_Level"=>$model->purchase_level,
            "Mfr_part_number"=>$model->mfr_part_number,
            "Description"=>$model->description,
            "Allegro_PCB_Footprint"=>$model->pcb_footprint,
            "Manufacturer"=>empty($model->manufacturer)?" ":array_search($model->manufacturer,$arrMfr),
            "zc_part_number"=>$model->zc_part_number,
            "2Mfr_part_number"=>$data234Mfr[2]['mfrPartNo'],
            "2zc_part_number"=>$data234Mfr[2]['zcPartNo'],
            "2Manufacturer"=>$data234Mfr[2]['mfr'],
            "2Description"=>$data234Mfr[2]['des'],
            "3Mfr_part_number"=>$data234Mfr[3]['mfrPartNo'],
            "3zc_part_number"=>$data234Mfr[3]['zcPartNo'],
            "3Manufacturer"=>$data234Mfr[3]['mfr'],
            "3Description"=>$data234Mfr[3]['des'],
            "4Mfr_part_number"=>$data234Mfr[4]['mfrPartNo'],
            "4zc_part_number"=>$data234Mfr[4]['zcPartNo'],
            "4Manufacturer"=>$data234Mfr[4]['mfr'],
            "4Description"=>$data234Mfr[4]['des'],
            "Version"=>date("Y-m-d H:i:s",time()),
            "Automotive"=>$model::VEHICLE_STANDARD[$model->vehicle_standard],
            "Part_type"=>$partTypeName,
            "Value"=>$model->value,
            "Schematic_part"=>$model->schematic_part,
            "Datasheet"=>$model->datasheet,
            "Price"=>$model->price,
            "recommend_purchase"=>'可用',
            "minimum_packing_quantity"=>$model->minimum_packing_quantity,
            "lead_time"=>$model->lead_time,
        ]);
        if(!$hardwareModel->save())
        {
            var_dump($hardwareModel->getErrors());die;
            return false;
        }

        //同步NC数据
        $hardwareNcModel->setAttributes([
            "Assy_Level"=>$model->assy_level,
            "Purchase_Level"=>$model->purchase_level,
            "Mfr_part_number"=>$model->mfr_part_number,
            "Description"=>$model->description,
            "Allegro_PCB_Footprint"=>$model->pcb_footprint,
            "Manufacturer"=>empty($model->manufacturer)?" ":array_search($model->manufacturer,$arrMfr),
            "zc_part_number"=>$model->zc_part_number,
            "2Mfr_part_number"=>$data234Mfr[2]['mfrPartNo'],
            "2zc_part_number"=>$data234Mfr[2]['zcPartNo'],
            "2Manufacturer"=>$data234Mfr[2]['mfr'],
            "2Description"=>$data234Mfr[2]['des'],
            "3Mfr_part_number"=>$data234Mfr[3]['mfrPartNo'],
            "3zc_part_number"=>$data234Mfr[3]['zcPartNo'],
            "3Manufacturer"=>$data234Mfr[3]['mfr'],
            "3Description"=>$data234Mfr[3]['des'],
            "4Mfr_part_number"=>$data234Mfr[4]['mfrPartNo'],
            "4zc_part_number"=>$data234Mfr[4]['zcPartNo'],
            "4Manufacturer"=>$data234Mfr[4]['mfr'],
            "4Description"=>$data234Mfr[4]['des'],
            "Version"=>date("Y-m-d H:i:s",time()),
            "Automotive"=>$model::VEHICLE_STANDARD[$model->vehicle_standard],
            "Part_type"=>$partTypeName,
            "Value"=>$model->value,
            "Schematic_part"=>$model->schematic_part,
            "Datasheet"=>$model->datasheet,
            "Price"=>$model->price,
            "recommend_purchase"=>'可用',
            "minimum_packing_quantity"=>$model->minimum_packing_quantity,
            "lead_time"=>$model->lead_time,
        ]);
        if(!$hardwareNcModel->save())
        {
            var_dump($hardwareNcModel->getErrors());die;
            return false;
        }
        return true;
    }


    /**解析excel表
     * @param $model
     * @return mixed
     */
    public function analysisMtrImport($model)
    {
        $model->mtrFile = UploadedFile::getInstance($model, 'mtrFile');
//            if ($model->upload()) {
        require_once (Yii::getAlias("@common")."/components/phpexcel/PHPExcel/Reader/Excel2007.php");

        $reader =\PHPExcel_IOFactory::createReader('Excel5');
        //读excel文件
        $PHPExcel = $reader->load($model->mtrFile->tempName,'utf-8'); // 载入excel文件

        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = ord($sheet->getHighestColumn())-65; // 取得总列数,0->A
        //得到智车料号列
        $zcNum=$desNum=0;
        for ($i=0;$i<$highestColumm;$i++){
            $colum = \PHPExcel_Cell::stringFromColumnIndex($i);
            $cell = $sheet->getCell("$colum".'1')->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            if($cell == '描述'){
                $zcNum = $i;
            }
            if($cell == '其它'){
                $desNum = $i;
            }
        }
        $id2 = $sheet->getCell('B1')->getValue();//物料ID
        if ($id2 instanceof \PHPExcel_RichText) { //富文本转换字符串
            $id2 = $id2->__toString();
        }
        $id3 = $sheet->getCell('C1')->getValue();//描述ID
        if ($id3 instanceof \PHPExcel_RichText) { //富文本转换字符串
            $id3 = $id3->__toString();
        }
        //智车料号规则
        $mtrCode = MaterialEncodeRule::findOne($id2)->getSon();
        foreach ($mtrCode as $key=>$item){
            $children = $item->getSon();
            if(empty($children))
                $ruleZc[$key] = [];
            foreach ($children as $child){
                if($item->name == '厂家')
                    $ruleZc[$key][trim($child->remark)] = trim($child->id);
                else
                    $ruleZc[$key][trim($child->name)] = trim($child->remark);
            }
        }
        $arrMfr = end($ruleZc);//厂家
        $data['sheet'] = $sheet;
        $data['highestRow'] = $highestRow;
        $data['highestColumm'] = $highestColumm;
        $data['zcNum'] = $zcNum;
        $data['desNum'] = $desNum;
        $data['id2'] = $id2;
        $data['ruleZc'] = $ruleZc;
        $data['arrMfr'] = $arrMfr;
        $data['id3'] = $id3;
        return $data;
    }
    /**
     *  根据id返回第二到第5位的码
     */
    public function getScdCode($id)
    {
        switch ($id)
        {   //选择value而不选择内容的原因是他们要是改规则的名字不会有影响
            case '5'://RES
                return array('RES',3);
                break;
            case '6'://CAP
                return array('CAP',3);
                break;
            case '7'://IND
                return array('IND',5);
                break;
            case '65'://BEAD
                return array('EBA',5);
                break;
            case '45'://Diode
                return array('DIO',7);
                break;
            case '66'://Triode
                return array('TRI',7);
                break;
            case '189'://MOS
                return array('MOS',9);
                break;
            case '204'://Fuse
                return array('FUS',7);
                break;
            case '205'://CONN
                return array('CON',5);
                break;
            case '206'://Crystal/Oscillator
                return array('CRY',9);
                break;
            case '207'://Spring
                return array('SPR',8);
                break;
            // case '208'://Buzzer
            //     return array([1,3],'BUZ',10);
            //     break;
            case '8'://Analog IC
                return array('AIC',7);
                break;
            case '9'://Power IC
                return array('PIC',7);
                break;
            case '338'://PHY
                return array('ICH',7);
                break;
            case '396'://Memory
                return array('ICM',7);
                break;
            case '398'://AP
                return array('ICU',7);
                break;
            case '399'://Sensor
                return array('ICS',7);
                break;
            case '10'://Module/IC和ANT
            case '11':
                return array('ICR',7);
                break;
            case '397'://Video
                return array('ICV',6);
                break;
            case '551'://Battery
                return array('ICB',8);
                break;
            case '2000'://Buzzer
                return array('BUZ',7);
                break;
            case '1987'://RELAY
                return array('REL',7);
                break;
        }
        return false;

    }

    /**
     * 数字位不够，补零
     * @param num:被补数
     * @param n:补多少位
     * @returns {*}:补完的数
     */
    public function padZore($num, $n)
    {
        $num= $num.'';

        $len = strlen($num);
        for(;$len < $n;$len++)
            $num = "0" . $num;
        return $num;
    }
}