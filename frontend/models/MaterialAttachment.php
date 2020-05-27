<?php

namespace frontend\models;

use backend\models\Department;
use backend\models\DepartmentUser;
use common\components\CommonFunc;
use Yii;


/**
 * This is the model class for table "material_attachment".
 *
 * @property integer $id
 * @property string $path
 * @property string $name
 * @property integer $version
 * @property string $remark
 * @property integer $updated_at
 * @property integer $modify_material_id;//为0时，是不经过物料更新的，项目文件上传的
 * @property integer $material_id
 * @property integer $file_class_name
 */
class MaterialAttachment extends TaskAbstract
{
    const PROJECT_FILE_UPLOAD = '物料文件上传';//任务名称

    public $desc,$car_number;//物料的描述
    //审批部门
    public $departLvl=[];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material_attachment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['path', 'name', 'version', 'updated_at', 'modify_material_id','file_class_name'], 'required'],
            [['version', 'updated_at', 'modify_material_id', 'material_id'], 'integer'],
            [['path', 'name', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     *
     *
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('material', 'ID'),
            'path' => Yii::t('material', '路径'),
            'name' => Yii::t('material', '文件名'),
            'version' => Yii::t('material', '最新版本'),
            'remark' => Yii::t('material', '备注'),
            'updated_at' => Yii::t('material', '更新时间'),
            //为0时，是管理员上传。为-1时项目文件管理那块，还没有上传。
            'modify_material_id' => Yii::t('material', '关联的中间物料表的ID'),
            'material_id' => Yii::t('material', '关联物料表的ID'),
            'file_class_name'=>'文件类名'
        ];
    }

    public function getDepartmentApprove()
    {
        $id = ProjectProcessTemplate::findOne(['name'=>$this->file_class_name])->id;
        $pjtPcsTempApprover = PjtPcsTempApprover::find()->where(['ppt_id'=>$id])->all();
        $department = [];
        foreach ($pjtPcsTempApprover as $model){
            $department[$model->lvl][Department::findOne($model->department_id)->name] =DepartmentUser::find()->leftJoin('user','user.id=department_user.user_id')
                ->where(['department_user.department_id'=>$model->department_id])
                ->select('user.username,user.id')->indexBy('id')->column();
        }
        return $department;
    }

    /////////////////关联表////////////////////
    public function getMaterials()
    {
        return $this->hasOne(Materials::className(),['material_id'=>'material_id']);
    }

    public function doPass()
    {
        // TODO: Implement doPass() method.
        $this->modify_material_id = 0;
        if(!$this->save())
            return false;
        return true;

    }

    public function getTaskMail()
    {
        // TODO: Implement getTaskMail() method.
        return $this->name;
    }

    public function initMdl()
    {
        // TODO: Implement initMdl() method.
        $this->nameTask = self::PROJECT_FILE_UPLOAD;
        $this->typeTask = Tasks::TASK_TYPE_MTR_FILE_UPLOAD;
    }

}
