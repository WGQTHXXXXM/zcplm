<?php

namespace api\models;

use app\models\MaterialEncodeRule;
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
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Adminuser::className(), 'targetAttribute' => ['created_by' => 'id']],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'assy_level' =>  'Assy Level',
            'is_first_mfr'=>  'Is First Manufacturer',
            'purchase_level' =>  'Purchase Level',
            'mfr_part_number' =>  'Manufacturer Part Number',
            'part_name' =>  'Part Name',
            'description' =>  'Description',
            'unit' =>  'Unit',
            'pcb_footprint' =>  'Pcb Footprint',
            'manufacturer' =>  'Manufacturer',
            'zc_part_number' =>  'Zhiche Part Number',
            'date_entered' =>  'Date Entered',
            'vehicle_standard' =>  'Vehicle Standard',
            'part_type' =>  'Part Type',
            'value' =>  'Value',
            'schematic_part' =>  'Schematic Part',
            'datasheet' =>  'Datasheet',
            'price' =>  'Price',
            'recommend_purchase' =>  'Recommend Purchase',
            'minimum_packing_quantity' =>  'Minimum Packing Quantity',
            'lead_time' =>  'Lead Time',
            'manufacturer2_id' =>  'Second Manufacturer Part Number',
            'manufacturer3_id' =>  'third Manufacturer Part Number',
            'manufacturer4_id' =>  'fourth Manufacturer Part Number',
            'mfrPartNo2'=>'Second Zhiche Part Number',
            'mfrPartNo3'=>'third Zhiche Part Number',
            'mfrPartNo4'=>'fourth Zhiche Part Number',
            'approver1'=>'部门内一级审批人',
            'approver2'=>'部门内二级审批人',
            'approver3dcc'=>'dcc审批人',
            'approver3purchase'=>'采购审批人',
            'remark'=>'备注',
            'car_number'=>'整车料号',

            'class1' =>  'Class1',
            'class2' =>  'Class2',

        ];

    }

    public function getPartType()
    {
        return $this->hasOne(MaterialEncodeRule::className(), ['id' => 'part_type']);
    }

    public function fields()
    {


        return [
            'material_id'=>'material_id',
            'zc_part_number'=>'zc_part_number',
            'part_name'=>'part_name',
            'description'=>'description',
//            '是否是一供'=>function ($model) {
//                return $model->is_first_mfr==1?'是':'否';
//            },
//            '物料类型'=>function ($model) {
//                return $model->partType->name;
//            },

            //             'createdBy'=>function ($model) {
            //             return $model->createdBy->realname;
            //             },
        ];
    }

    public function extraFields()
    {
        return ['partType'];
    }


    /*$fields = parent::fields();

    unset($fields['assy_level']);
    //unset($fields['material_id']);
    unset($fields['parent_id']);
    //unset($fields['zc_part_number']);
    unset($fields['is_first_mfr']);
    unset($fields['assy_level']);
    unset($fields['purchase_level']);
    unset($fields['mfr_part_number']);
    //unset($fields['part_name']);
    //unset($fields['description']);
    unset($fields['unit']);
    unset($fields['pcb_footprint']);
    unset($fields['manufacturer']);
    unset($fields['date_entered']);
    unset($fields['vehicle_standard']);
    unset($fields['part_type']);
    unset($fields['value']);
    unset($fields['schematic_part']);
    unset($fields['datasheet']);
    unset($fields['price']);
    unset($fields['recommend_purchase']);
    unset($fields['minimum_packing_quantity']);
    unset($fields['lead_time']);
    unset($fields['manufacturer2_id']);
    unset($fields['manufacturer3_id']);
    unset($fields['manufacturer4_id']);
    unset($fields['remark']);
    unset($fields['car_number']);

    return $fields;*/



}
