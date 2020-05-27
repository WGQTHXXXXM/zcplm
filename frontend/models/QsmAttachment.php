<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "qsm_attachment".
 *
 * @property integer $id
 * @property integer $qsm_id
 * @property string $path
 * @property string $name
 * @property integer $updated_at
 * @property integer $version
 * @property integer $status
 * @property string $remark
 */
class QsmAttachment extends TaskAbstract
{
    const QSM_FILE_UPLOAD = '质量体系文件上传';//任务名称

    public $departLvl=[];//选择的部门审批人的数据

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qsm_attachment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['qsm_id', 'path', 'name', 'updated_at', 'version','status'], 'required'],
            [['qsm_id', 'updated_at', 'version','status'], 'integer'],
            [['remark'], 'string'],
            [['path', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'qsm_id' => Yii::t('material', 'qsm的id号'),
            'path' => Yii::t('material', '存储路径'),
            'name' => Yii::t('material', '文件名'),
            'updated_at' => Yii::t('material', '更新时间'),
            'version' => Yii::t('material', '版本'),
            'remark' => Yii::t('material', '上传文件的描述'),
        ];
    }

    public function getQsm()
    {
        return $this->hasOne(QualitySystemManage::className(),['id'=>'qsm_id']);
    }

    /**
     * 通过时，改下文件状态，让文件可见
     */
    public function doPass()
    {
        // TODO: Implement doPass() method.
        $this->status = 1;
        $mdlQsm = QualitySystemManage::findOne($this->qsm_id);
        $mdlQsm->status_submit = QualitySystemManage::FILE_STATUS_SURE;
        if($this->save()&&$mdlQsm->save())
            return true;
        return false;
    }

    public function getTaskMail()
    {
        // TODO: Implement getTaskMail() method.
        $this->name;
    }

    public function initMdl()
    {
        // TODO: Implement initMdl() method.
        $this->nameTask = self::QSM_FILE_UPLOAD;
        $this->typeTask = Tasks::TASK_TYPE_QSM;
    }


}
