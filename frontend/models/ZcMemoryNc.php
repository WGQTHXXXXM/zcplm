<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "zc_memory_nc".
 *
 * @property integer $Item
 * @property integer $Assy_Level
 * @property string $Purchase_Level
 * @property string $Mfr_part_number
 * @property string $Description
 * @property string $Allegro_PCB_Footprint
 * @property string $Manufacturer
 * @property string $zc_part_number
 * @property string $2Mfr_part_number
 * @property string $2zc_part_number
 * @property string $2Manufacturer
 * @property string $2Description
 * @property string $3Mfr_part_number
 * @property string $3zc_part_number
 * @property string $3Manufacturer
 * @property string $3Description
 * @property string $4Mfr_part_number
 * @property string $4zc_part_number
 * @property string $4Manufacturer
 * @property string $4Description
 * @property string $Version
 * @property string $Automotive
 * @property string $Part_type
 * @property string $Value
 * @property string $Schematic_part
 * @property string $Datasheet
 * @property string $Price
 * @property string $recommend_purchase
 * @property integer $minimum_packing_quantity
 * @property integer $lead_time
 */
class ZcMemoryNc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zc_memory_nc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Assy_Level', 'recommend_purchase'], 'required'],
            [['Assy_Level', 'minimum_packing_quantity', 'lead_time'], 'integer'],
            [['Purchase_Level'], 'string', 'max' => 10],
            [['Mfr_part_number', 'Description', 'Allegro_PCB_Footprint', 'Manufacturer', 'zc_part_number', '2Mfr_part_number', '2zc_part_number', '2Manufacturer', '2Description', '3Mfr_part_number', '3zc_part_number', '3Manufacturer', '3Description', '4Mfr_part_number', '4zc_part_number', '4Manufacturer', '4Description', 'Part_type', 'Value', 'Schematic_part', 'Datasheet'], 'string', 'max' => 255],
            [['Version'], 'string', 'max' => 30],
            [['Automotive', 'Price', 'recommend_purchase'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Item' => Yii::t('material', '主索引'),
            'Assy_Level' => Yii::t('material', '装配等级'),
            'Purchase_Level' => Yii::t('material', '采购等级'),
            'Mfr_part_number' => Yii::t('material', '厂商零件编号'),
            'Description' => Yii::t('material', '描述'),
            'Allegro_PCB_Footprint' => Yii::t('material', '封装'),
            'Manufacturer' => Yii::t('material', '供应商'),
            'zc_part_number' => Yii::t('material', '智车料号'),
            '2Mfr_part_number' => Yii::t('material', '厂商零件编号（2）'),
            '2zc_part_number' => Yii::t('material', '智车料号（2）'),
            '2Manufacturer' => Yii::t('material', '供应商（2）'),
            '2Description' => Yii::t('material', '二供描述'),
            '3Mfr_part_number' => Yii::t('material', '厂商零件编号（3）'),
            '3zc_part_number' => Yii::t('material', '智车料号（3）'),
            '3Manufacturer' => Yii::t('material', '供应商（3）'),
            '3Description' => Yii::t('material', '三供描述'),
            '4Mfr_part_number' => Yii::t('material', '厂商零件编号（4）'),
            '4zc_part_number' => Yii::t('material', '智车料号（4）'),
            '4Manufacturer' => Yii::t('material', '供应商（4）'),
            '4Description' => Yii::t('material', '四供描述'),
            'Version' => Yii::t('material', '版本'),
            'Automotive' => Yii::t('material', '是不是车规料'),
            'Part_type' => Yii::t('material', '零件类型'),
            'Value' => Yii::t('material', '零件大小值'),
            'Schematic_part' => Yii::t('material', '零件原理图'),
            'Datasheet' => Yii::t('material', '规格书'),
            'Price' => Yii::t('material', '单价'),
            'recommend_purchase' => Yii::t('material', '采购推荐等级'),
            'minimum_packing_quantity' => Yii::t('material', '最小包装量'),
            'lead_time' => Yii::t('material', '交货周期'),
        ];
    }
}
