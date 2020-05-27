<?php

namespace frontend\models;

use Yii;

/**
 * 一个变更集合有可能对应多个“零件使用者”ecn_part_user。
 *
 *
 * This is the model class for table "ecn_part_user".
 *
 * @property integer $id
 * @property integer $ecn_change_id
 * @property integer $bom_id
 */
class EcnPartUser extends \yii\db\ActiveRecord
{
//    ///////关联ecn_altgroup表的字段/////////////
//    public $mfr_no,$mdf_type,$mdf_part_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecn_part_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ecn_change_id', 'bom_id'], 'required'],
            [['ecn_change_id', 'bom_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('material', 'ID'),
            'ecn_change_id' => Yii::t('material', '变更集合的ID'),
            'bom_id' => Yii::t('material', '使用者bom的id'),
        ];
    }

    /**
     * 关联的bom表
     */
    public function getExtBomsChild()
    {
        return $this->hasOne(ExtBomsChild::className(),['id'=>'bom_id']);
    }

    public function getAltGroup()
    {
        return $this->hasOne(EcnAltgroup::className(),['part_user_id'=>'id']);
    }


    /**
     * @param $key:保存当前使用者数组的索引
     * @param $idChangeSet：当前变更集合的id号
     * @return true
     */
    public static function savePartUser($key,$mdlChangeSet)
    {
        $data = $_POST['Ecn']['userParent'];
        //根据不同的变更类型来创建bom表
        $changeType = $_POST['Ecn']['change_type'][$key];
        //保存当前的使用者
        foreach ($data[$key] as $k=>$v)
        {
            $mdlPartUser = new self();
            $mdlPartUser->ecn_change_id = $mdlChangeSet->id;
            $mdlPartUser->bom_id = $k;
            if(!$mdlPartUser->save())
                return false;
            switch ($changeType)
            {
                case EcnChangeSet::CT_REPLACE:
                {
                    if(!ExtBomsParent::saveEcnChangeReplace($k,$mdlChangeSet))
                        return false;
                    break;
                }
                case EcnChangeSet::CT_ADJQTY:
                {
                    if(!ExtBomsParent::saveEcnChangeAdjqty($k,$mdlChangeSet))
                        return false;

                    break;
                }
                case EcnChangeSet::CT_ADD:
                {
                    if(!ExtBomsParent::saveEcnChangeAdd($k,$mdlChangeSet))
                        return false;

                    break;
                }
                case EcnChangeSet::CT_REMOVE:
                {
                    if(!ExtBomsParent::saveEcnChangeRemove($k,$mdlChangeSet))
                        return false;
                    break;
                }
                case EcnChangeSet::CT_ALTGROUP:
                {
                    if(!EcnAltgroup::saveAltgroup($k,$key,$mdlPartUser->id))
                        return false;
                    if(!ExtBomsParent::saveEcnChangeAltgroup($k,$mdlChangeSet,$key))
                        return false;

                    break;
                }
            }
        }
        return true;
    }
}
