<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table 'materials'.
 *
 * @property string $material_id
 * @property string $assy_level
 * @property string $purchase_level
 * @property string $mfr_part_number
 * @property string $part_name
 * @property string $description
 * @property string $unit
 * @property string $pcb_footprint
 * @property integer $manufacturer
 * @property string $zc_part_number
 * @property string $date_entered
 * @property integer $vehicle_standard
 * @property integer $part_type
 * @property string $value
 * @property string $schematic_part
 * @property string $datasheet
 * @property string $price
 * @property string $active
 * @property integer $manufacturer2_id
 * @property integer $manufacturer3_id
 * @property integer $manufacturer4_id
 * @property integer $recommend_purchase
 * @property integer $minimum_packing_quantity
 * @property integer $lead_time
 */
class Materials extends \yii\db\ActiveRecord
{
    //一，二级分类
    public $class1;
    public $class2;
    //形成智车料号的六个分类
    public $mer1;
    public $mer2;
    public $mer3;
    public $mer4;
    public $mer5;
    public $mer6;
    public $mer7;
    public $mer8;
    public $mer9;
    //二三四供的智车料号
    public $mfrPartNo2;
    public $mfrPartNo3;
    public $mfrPartNo4;



    //采购推荐级别的宏定义
    const RECOMMEND_PURCHASE = [-1=>'不可用',0=>'可用',1=>'推荐',];
    //物料级别
    const VEHICLE_STANDARD = [0=>'商业级',1=>'工业级',2=>'汽车级',3=>''];



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'materials';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['manufacturer', 'part_type', 'manufacturer2_id', 'manufacturer3_id', 'manufacturer4_id','parent_id',
                 'material_id','minimum_packing_quantity','is_first_mfr', 'lead_time','assy_level'], 'integer'],
            [['date_entered', 'mfr_part_number','recommend_purchase','vehicle_standard'], 'safe'],
            [['purchase_level', 'unit'], 'string', 'max' => 10],
            [[ 'description', 'pcb_footprint', 'value', 'schematic_part', 'datasheet','remark'], 'string', 'max' => 255],
            [['zc_part_number'], 'string', 'max' => 40],
            [['price'], 'string', 'max' => 20],
            [['part_name','car_number'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'assy_level' => Yii::t('material', 'Assy Level'),
            'is_first_mfr'=> Yii::t('material', 'Is First Manufacturer'),
            'purchase_level' => Yii::t('material', 'Purchase Level'),
            'mfr_part_number' => Yii::t('material', 'Manufacturer Part Number'),
            'part_name' => Yii::t('material', 'Part Name'),
            'description' => Yii::t('material', 'Description'),
            'unit' => Yii::t('material', 'Unit'),
            'pcb_footprint' => Yii::t('material', 'Pcb Footprint'),
            'manufacturer' => Yii::t('material', 'Manufacturer'),
            'zc_part_number' => Yii::t('material', 'Zhiche Part Number'),
            'date_entered' => Yii::t('material', 'Date Entered'),
            'vehicle_standard' => Yii::t('material', 'Vehicle Standard'),
            'part_type' => Yii::t('material', 'Part Type'),
            'value' => Yii::t('material', 'Value'),
            'schematic_part' => Yii::t('material', 'Schematic Part'),
            'datasheet' => Yii::t('material', 'Datasheet'),
            'price' => Yii::t('material', 'Price'),
            'recommend_purchase' => Yii::t('material', 'Recommend Purchase'),
            'minimum_packing_quantity' => Yii::t('material', 'Minimum Packing Quantity'),
            'lead_time' => Yii::t('material', 'Lead Time'),
            'manufacturer2_id' => Yii::t('material', 'Second Manufacturer Part Number'),
            'manufacturer3_id' => Yii::t('material', 'third Manufacturer Part Number'),
            'manufacturer4_id' => Yii::t('material', 'fourth Manufacturer Part Number'),
            'mfrPartNo2'=>Yii::t('material','Second Zhiche Part Number'),
            'mfrPartNo3'=>Yii::t('material','third Zhiche Part Number'),
            'mfrPartNo4'=>Yii::t('material','fourth Zhiche Part Number'),
            'approver1'=>'部门内一级审批人',
            'approver2'=>'部门内二级审批人',
            'approver3dcc'=>'dcc审批人',
            'approver3purchase'=>'采购审批人',
            'remark'=>'备注',
            'car_number'=>'整车料号',

            'class1' => Yii::t('material', 'Class1'),
            'class2' => Yii::t('material', 'Class2'),

            'assy_level_toggle'=> Yii::t('material', 'Assy Level Toggle'),
            'purchase_level_toggle'=> Yii::t('material', 'Purchase Level Toggle'),
            'mfr_part_number_toggle'=> Yii::t('material', 'Manufacturer Part Number Toggle'),
            'part_name_toggle'=> Yii::t('material', 'Part Name'),
            'description_toggle'=> Yii::t('material', 'Description Toggle'),
            'unit_toggle'=> Yii::t('material', 'Unit'),
            'pcb_footprint_toggle'=> Yii::t('material', 'Pcb Footprint Toggle'),
            'manufacturer_toggle'=> Yii::t('material', 'Manufacturer Toggle'),
            'zc_part_number_toggle'=> Yii::t('material', 'Zhiche Part Number Toggle'),
            'date_entered_toggle'=> Yii::t('material', 'Date Entered Toggle'),
            'vehicle_standard_toggle'=> Yii::t('material', 'Vehicle Standard Toggle'),
            'part_type_toggle'=> Yii::t('material', 'Part Type Toggle'),
            'value_toggle'=> Yii::t('material', 'Value Toggle'),
            'schematic_part_toggle'=> Yii::t('material', 'Schematic Part Toggle'),
            'datasheet_toggle'=> Yii::t('material', 'Datasheet Toggle'),
            'price_toggle'=> Yii::t('material', 'Price Toggle'),
            'manufacturer2_id_toggle'=> Yii::t('material', 'Manufacturer2 Part Number Toggle'),
            'manufacturer3_id_toggle'=> Yii::t('material', 'Manufacturer3 Part Number Toggle'),
            'manufacturer4_id_toggle'=> Yii::t('material', 'Manufacturer4 Part Number Toggle'),
            'recommend_purchase_toggle'=> Yii::t('material', 'Recommend Purchase Toggle'),
            'lead_time_toggle'=> Yii::t('material', 'Lead Time Toggle'),
            'minimum_packing_quantity_toggle'=> Yii::t('material', 'Minimum Packing Quantity Toggle'),
            'car_number_toggle'=> '整车料号',
            'remark_toggle'=> '备注',
        ];

    }




    //上传的规格书datasheet
    public function getDatasheetAttachments()
    {
        return $this->hasMany(MaterialAttachment::className(),['material_id' => 'material_id'])
            ->andWhere('material_attachment.modify_material_id<>-1');
    }

    //元件类型
    public function getPartType()
    {
        return $this->hasOne(MaterialEncodeRule::className(), ['id' => 'part_type']);
    }

    //一供厂商家
    public function getManufacturer1()
    {
        return $this->hasOne(MaterialEncodeRule::className(), ['id' => 'manufacturer']);
    }


    //二供厂商家
    public function getManufacturer2()
    {
        return $this->hasOne(Materials::className(), ['Material_id' => 'manufacturer2_id']);
    }

    //三供厂商家
    public function getManufacturer3()
    {
        return $this->hasOne(Materials::className(), ['Material_id' => 'manufacturer3_id']);
    }

    //四供厂商家
    public function getManufacturer4()
    {
        return $this->hasOne(Materials::className(), ['Material_id' => 'manufacturer4_id']);
    }

    //BOM
    public function getBomParent()
    {
        return $this->hasMany(BomsParent::className(),['parent_id'=>'material_id']);
    }

    //获得物料最大版本
    public function getMaxVersion()
    {
        $maxVersion = self::find()->select('max(material_id) as material_id')->groupBy('parent_id')
            ->where(['parent_id'=>$this->parent_id])->one()->material_id;
        return $maxVersion;
    }

    //系列料号
    public function getParentId()
    {
        return $this->hasOne(Materials::className(),['material_id'=>'parent_id']);
    }

}
