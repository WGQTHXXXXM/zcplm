<?php

namespace frontend\models;

use Yii;

/**
 * 功能：一个“零件使用都”有可能对应一个“组变更”ecn_alt_group。(改二三四供时的信息)
 *
 * This is the model class for table "ecn_altgroup".
 *
 * @property integer $id
 * @property integer $part_user_id
 * @property integer $mfr_no
 * @property integer $mdf_type
 * @property integer $mdf_part_id
 */
class EcnAltgroup extends \yii\db\ActiveRecord
{
    //更改二供时的每个使用者的变量类型
    const GROUP_CHANGE_DETAIL=[0=>'增加',1=>'减少',2=>'替换'];
    const GROUP_CHANGE_ADD = 0;
    const GROUP_CHANGE_SUB = 1;
    const GROUP_CHANGE_REPLACE = 2;



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecn_altgroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['part_user_id', 'mfr_no', 'mdf_type'], 'required'],
            [['part_user_id', 'mfr_no', 'mdf_type', 'mdf_part_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('material', 'ID'),
            'part_user_id' => Yii::t('material', '使用者零件id'),
            'mfr_no' => Yii::t('material', '选中的第几供应商'),
            'mdf_type' => Yii::t('material', '修改类型'),
            'mdf_part_id' => Yii::t('material', '修改后的零件id'),
        ];
    }

    /**
     * @param $k:使用者的parent_id号
     * @param $key：第几组的变更集合号
     * @param $idPartUser：使用者的id号
     */
    public static function saveAltgroup($k,$key,$idPartUser)
    {
        $mdlAltGroup = new self();
        $mdlAltGroup->part_user_id = $idPartUser;
        $mdlAltGroup->mfr_no = $_POST['Ecn']['mfr'][$key][$k];
        $mdlAltGroup->mdf_type = $_POST['Ecn']['mdf_mfr_type'][$key][$k];
        $mdlAltGroup->mdf_part_id = $_POST['Ecn']['mdf_part_no'][$key][$k];
        if($mdlAltGroup->save())
           return true;
        return false;
    }

    /**
     * 关联表
     */
    public function getMdfPart()
    {
        return $this->hasOne(Materials::className(),['material_id'=>'mdf_part_id']);
    }
}