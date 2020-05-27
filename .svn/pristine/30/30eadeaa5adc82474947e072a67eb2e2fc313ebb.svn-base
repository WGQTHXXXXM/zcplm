<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "ecn_pbom_attachment".
 *
 * @property integer $id
 * @property integer $ecn_id
 * @property integer $pbom_id
 */
class EcnPbomAttachment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecn_pbom_attachment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ecn_id', 'pbom_id'], 'required'],
            [['ecn_id', 'pbom_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('material', 'ID'),
            'ecn_id' => Yii::t('material', 'ecn的id'),
            'pbom_id' => Yii::t('material', 'boms_parent的id'),
        ];
    }
}
