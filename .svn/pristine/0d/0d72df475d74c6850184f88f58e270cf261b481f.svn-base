<?php

namespace frontend\models;

use Yii;

/**
 * 功能：更改bom时增加的料存到ecn_bomid_temp，以备删除时用可以找到
 *
 *
 * This is the model class for table "ecn_bomid_tmp".
 *
 * @property integer $ecn_id
 * @property integer $bom_id
 */
class EcnBomidTmp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecn_bomid_tmp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ecn_id', 'bom_id'], 'required'],
            [['ecn_id', 'bom_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ecn_id' => Yii::t('material', 'ECN的编号'),
            'bom_id' => Yii::t('material', 'BOM的ID号'),
        ];
    }
}
