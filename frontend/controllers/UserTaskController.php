<?php

namespace frontend\controllers;

use common\components\CommonFunc;
use common\models\User;
use frontend\models\BomsParent;
use frontend\models\ExtBomsParent;
use frontend\models\MaterialApprover;
use frontend\models\MaterialAttachment;
use frontend\models\ProjectProcess;
use frontend\models\Tasks;
use Yii;
use frontend\models\UserTask;
use frontend\models\UserTaskSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use frontend\models\MaterialEncodeRule;
use frontend\models\ModifyMaterial;
use frontend\models\Materials;
use yii\filters\AccessControl;
use frontend\models\Ecr;
use frontend\models\Ecn;
use frontend\models\Approver;

/**
 * UserTaskController implements the CRUD actions for UserTask model.
 */
class UserTaskController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //只找自己需要审批的并任务是已提交的并且是自己没有审批的
//        $dataProvider->query->where(['user_task.status'=>UserTask::STATUS_UNAPPROVE,
//            "user_task.user_id"=>Yii::$app->user->id,
//            'tasks.status'=>Tasks::STATUS_COMMITED]);
        $dataProvider->query->where(["user_task.user_id"=>Yii::$app->user->id]);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 审批人的指派
     */
    public function actionChangeUser()
    {
        //开启事务
        $transaction  = Yii::$app->db->beginTransaction();
        if (isset($_POST['hasEditable']))
        {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;//这个表格编辑控件需要的
            $mdlUserTask = UserTask::findOne($_POST['editableKey']);

            $data = current($_POST['UserTask']);//表单数据

            //如果是物料的话，判断审批人，不可以让审批人相同;
            if($mdlUserTask->tasks->type == Tasks::TASK_TYPE_MATERIAL||
                $mdlUserTask->tasks->type == Tasks::TASK_TYPE_MTR_APPROVER1||
                $mdlUserTask->tasks->type == Tasks::TASK_TYPE_MTR_APPROVER2)
            {
                //审批人模型
                $mdlOldMaterialApprover = $mdlUserTask->tasks->modifyMaterial->materialApprover;
                if($data['userTaskUser'] == $mdlOldMaterialApprover->approver1||
                    $data['userTaskUser'] == $mdlOldMaterialApprover->approver2||
                    $data['userTaskUser'] == $mdlOldMaterialApprover->approver3dcc||
                    $data['userTaskUser'] == $mdlOldMaterialApprover->approver3purchase)
                {
                    return ['output'=>'','message'=>'审批人不可重复审批'];
                }
                //看是改的哪级审批，然后改material_approver表
                if($mdlOldMaterialApprover->approver1 == $mdlUserTask->user_id)
                    $mdlOldMaterialApprover->approver1 = $data['userTaskUser'];
                else if($mdlOldMaterialApprover->approver2 == $mdlUserTask->user_id)
                    $mdlOldMaterialApprover->approver2 = $data['userTaskUser'];
                else if($mdlOldMaterialApprover->approver3dcc == $mdlUserTask->user_id)
                    $mdlOldMaterialApprover->approver3dcc = $data['userTaskUser'];
                else if($mdlOldMaterialApprover->approver3purchase == $mdlUserTask->user_id)
                    $mdlOldMaterialApprover->approver3purchase =$data['userTaskUser'];

                if($mdlOldMaterialApprover->save())//更改采购审批人
                {
                    $mdlUserTask->user_id = $data['userTaskUser'];
                    if($mdlUserTask->save())
                    {
                        $user = User::findOne($data['userTaskUser']);
                        $mdlTask = Tasks::findOne($mdlUserTask->task_id);
                        CommonFunc::sendMail(CommonFunc::APPROVE_NOTICE,$user->email,$mdlTask->name,'','tasks/index');
                        $transaction->commit();
                        return ['output'=>'','message'=>''];
                    }
                    else
                    {
                        $transaction->rollBack();
                        return ['output'=>'','message'=>'更新失败'];
                    }
                }
                else
                {
                    $transaction->rollBack();
                    return ['output'=>'','message'=>'更新失败'];
                }
            }
            else
            {
                $mdlUserTask->user_id = $data['userTaskUser'];
                if($mdlUserTask->save())
                {
                    $user = User::findOne($data['userTaskUser']);
                    $mdlTask = Tasks::findOne($mdlUserTask->task_id);
                    CommonFunc::sendMail(CommonFunc::APPROVE_NOTICE,$user->email,$mdlTask->name,'','tasks/index');
                    $transaction->commit();
                    return ['output'=>'','message'=>''];
                }
            }
        }
        $transaction->rollBack();
        return ['output'=>'', 'message'=>'更新失败'];

    }

    /**
     *
     * 审批管理
     *
     */
    public function actionAdminIndex()
    {
        $searchModel = new UserTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataUser = User::find()->select('username,id')->indexBy('id')->column();

        return $this->render('admin-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataUser'=>$dataUser,
        ]);
    }

    /**
     * Displays a single UserTask model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $query = UserTask::find()->where(['task_id'=>$id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->renderAjax('view', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new UserTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new UserTask();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserTask model.
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
     * Finds the UserTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserTask::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     *
     * 保存审批的结果
     */
    public function actionDoApprove()
    {
        $message = 'save data error.';//返回的消息
        if (isset($_POST['hasEditable']))
        {
            $curTime = time();//当前时间
            $transaction = Yii::$app->getDb()->beginTransaction();//开启事务
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;//这个表格编辑控件需要的，响应格式要是JSON的
            $mdlUserTask = UserTask::findOne($_POST['editableKey']);
            $mdlUserTask->updated_at = $curTime;
            $data = current($_POST['UserTask']);//表单数据
            $mdlUserTask->remark = $data['userTaskRemark'];
            //审批处理
            if($data['userTaskStatus']==UserTask::STATUS_REJECTED)//如果审批拒绝
                $result = $mdlUserTask->rejectedtask();
            else
                $result = $mdlUserTask->passTask();

            if($result['status'] == true){//提交成功
                $transaction->commit();
                Yii::$app->getSession()->setFlash('success', $result['msg']);
                return ['output'=>'', 'message'=>''];
            }
            $transaction->rollBack();//如果 没成功回滚
            Yii::$app->getSession()->setFlash('error', "审批失败");
            return ['output'=>'', 'message'=>$result['msg']];
        }
    }

}
