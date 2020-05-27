<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Materials;


class MaterialsSearch extends Materials
{
    //   public $supplier_name;
    //   public $part;

    public $zc_part_number_toggle=1;
    public $mfr_part_number_toggle=1;
    public $part_name_toggle;
    public $car_number_toggle;
    public $description_toggle=1;
    public $manufacturer_toggle=1;
    public $manufacturer2_id_toggle;
    public $pcb_footprint_toggle=1;
    public $date_entered_toggle;
    public $vehicle_standard_toggle=1;
    public $recommend_purchase_toggle=1;
    public $part_type_toggle=1;
    public $value_toggle=1;
    public $remark_toggle;

    public $unit_toggle;
    public $schematic_part_toggle;
    public $datasheet_toggle;
    public $purchase_level_toggle;
    public $manufacturer3_id_toggle;
    public $manufacturer4_id_toggle;

    public function attributes()
    {
        return array_merge(parent::attributes(),['mfr1','partType1','mfrPartNo2','mfrPartNo3','mfrPartNo4',]);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_id', 'manufacturer', 'vehicle_standard', 'part_type', 'recommend_purchase', 'minimum_packing_quantity',
                'lead_time', 'manufacturer2_id', 'manufacturer3_id', 'manufacturer4_id'], 'integer'],
            [[ 'purchase_level', 'mfr_part_number', 'part_name', 'description', 'unit', 'pcb_footprint', 'zc_part_number','remark',
                'date_entered', 'value', 'schematic_part', 'datasheet', 'price','mfr1','partType1','mfrPartNo2','mfrPartNo3','mfrPartNo4','car_number'], 'safe'],

            [['purchase_level_toggle','mfr_part_number_toggle','part_name_toggle','description_toggle','unit_toggle','pcb_footprint_toggle'
                ,'manufacturer_toggle','zc_part_number_toggle','date_entered_toggle','vehicle_standard_toggle','part_type_toggle'
                ,'value_toggle','schematic_part_toggle','datasheet_toggle','recommend_purchase_toggle'
                ,'manufacturer2_id_toggle','manufacturer3_id_toggle'
                ,'manufacturer4_id_toggle','car_number_toggle'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Materials::find();
        // add conditions that should always apply here


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            //var_dump($this->getErrors());
            return $dataProvider;
        }

        ////////////////////排序///////////////////////////////////
        $dataProvider->sort->attributes['mfrPartNo2'] =//二供厂家料号
            [
                'asc'=>['mfr2.mfr_part_number'=>SORT_ASC],
                'desc'=>['mfr2.mfr_part_number'=>SORT_DESC],
            ];
        $dataProvider->sort->attributes['mfrPartNo3'] =//三供二供厂家料号
            [
                'asc'=>['mfr3.mfr_part_number'=>SORT_ASC],
                'desc'=>['mfr3.mfr_part_number'=>SORT_DESC],
            ];
        $dataProvider->sort->attributes['mfrPartNo4'] =//四供二供厂家料号
            [
                'asc'=>['mfr4.mfr_part_number'=>SORT_ASC],
                'desc'=>['mfr4.mfr_part_number'=>SORT_DESC],
            ];
        $dataProvider->sort->attributes['mfr1'] =//一供厂家
            [
                'asc'=>['material_encode_rule.name'=>SORT_ASC],
                'desc'=>['material_encode_rule.name'=>SORT_DESC],
            ];
        $dataProvider->sort->attributes['partType1'] =//物料partType
            [
                'asc'=>['material_encode_rule.name'=>SORT_ASC],
                'desc'=>['material_encode_rule.name'=>SORT_DESC],
            ];

        // grid filtering conditions
        $query->andFilterWhere([
            'materials.purchase_level' => $this->purchase_level,
            'materials.price' => $this->price,
            'materials.material_id' => $this->material_id,
        ]);

        $query->andFilterWhere(['like', 'materials.mfr_part_number', $this->mfr_part_number])
            ->andFilterWhere(['like', 'materials.part_name', $this->part_name])
            ->andFilterWhere(['like', 'materials.description', $this->description])
            ->andFilterWhere(['like', 'materials.unit', $this->unit])
            ->andFilterWhere(['like', 'materials.pcb_footprint', $this->pcb_footprint])
            ->andFilterWhere(['like', 'materials.manufacturer', $this->manufacturer])
            ->andFilterWhere(['like', 'materials.zc_part_number', $this->zc_part_number])
            ->andFilterWhere(['like', 'materials.date_entered', $this->date_entered])
            ->andFilterWhere(['like', 'materials.vehicle_standard', $this->vehicle_standard])
            ->andFilterWhere(['like', 'materials.part_type', $this->part_type])
            ->andFilterWhere(['like', 'materials.schematic_part', $this->schematic_part])
            ->andFilterWhere(['like', 'materials.datasheet', $this->datasheet])
            ->andFilterWhere(['like', 'materials.remark', $this->remark])
            ->andFilterWhere(['like', 'materials.recommend_purchase', $this->recommend_purchase])
            ->andFilterWhere(['like', 'materials.price', $this->price])
            ->andFilterWhere(['like', 'materials.car_number', $this->car_number]);
        if(empty($params))//如果没有搜索和排序，直接返回
            return $dataProvider;
        /////////////////二三四供料号的排序和搜索////////////////////////////////////////////
        if((!empty($params['sort'])&&($params['sort'] == 'mfrPartNo2'||$params['sort'] == '-mfrPartNo2'))
            ||!empty($params['MaterialsSearch']['mfrPartNo2']))
        {
            $query->innerJoin('materials as mfr2','mfr2.Material_id=materials.manufacturer2_id')->
            select('materials.*,mfr2.mfr_part_number as mfrPartNo2');
            $query->andFilterWhere(['like', 'mfr2.mfr_part_number', $this->mfrPartNo2]);
        }
        if((!empty($params['sort'])&&($params['sort'] == 'mfrPartNo3'||$params['sort'] == '-mfrPartNo3'))
            ||!empty($params['MaterialsSearch']['mfrPartNo3']))
        {
            $query->innerJoin('materials as mfr3','mfr3.Material_id=materials.manufacturer3_id')->
            select('materials.*,mfr3.mfr_part_number  as mfrPartNo3');
            $query->andFilterWhere(['like', 'mfr3.mfr_part_number', $this->mfrPartNo3]);
        }
        if((!empty($params['sort'])&&($params['sort'] == 'mfrPartNo4'||$params['sort'] == '-mfrPartNo4'))
            ||!empty($params['MaterialsSearch']['mfrPartNo4']))
        {
            $query->innerJoin('materials as mfr4','mfr4.Material_id=materials.manufacturer4_id')->
            select('materials.*,mfr4.mfr_part_number  as mfrPartNo4');
            $query->andFilterWhere(['like', 'mfr4.mfr_part_number', $this->mfrPartNo4]);
        }

        /////////////以下为搜索 厂家和物料类型代码////////////////////////////

        //关联一个表来表达厂家
        if((!empty($params['sort'])&&($params['sort'] == 'mfr1'||$params['sort'] == '-mfr1'))||
            !empty($params['MaterialsSearch']['mfr1'])&&empty($params['MaterialsSearch']['partType1']))
        {
            $query->join('join','material_encode_rule','materials.manufacturer = material_encode_rule.id');
            $query->andFilterWhere(['like','material_encode_rule.name',$this->mfr1]);
        }//关联一个表来表达part_type
        else if((!empty($params['sort']))&&($params['sort'] == 'partType1'||$params['sort'] == '-partType1')||
            (!empty($params['MaterialsSearch']['partType1'])&&empty($params['MaterialsSearch']['mfr1'])))
        {
            $query->join('join','material_encode_rule','materials.part_type = material_encode_rule.id');
            $query->andFilterWhere(['like','material_encode_rule.name',$this->partType1]);

        }//三表联合，物料表和只要厂家，只要part_type
        else if(!empty($params['MaterialsSearch']['partType1'])&&!empty($params['MaterialsSearch']['mfr1']))
        {
            $query->innerJoin('material_encode_rule as table_pt','materials.part_type=table_pt.id')
                ->innerJoin('material_encode_rule as table_mfr','materials.manufacturer=table_mfr.id')
            ->select('materials.*,table_pt.name,table_mfr.name');
            $query->andFilterWhere(['like','table_mfr.name',$this->mfr1])
                ->andFilterWhere(['like','table_pt.name',$this->partType1]);
            $dataProvider->sort->attributes['mfr1'] =
                [
                    'asc'=>['table_mfr.name'=>SORT_ASC],
                    'desc'=>['table_mfr.name'=>SORT_DESC],
                ];
            $dataProvider->sort->attributes['partType1'] =
                [
                    'asc'=>['table_pt.name'=>SORT_ASC],
                    'desc'=>['table_pt.name'=>SORT_DESC],
                ];
        }



//        $dataProvider->sort->defaultOrder =
//            [
//                'date_entered'=>SORT_DESC,
//            ];
        return $dataProvider;
    }


    public function searchAttach($params)
    {
        if(empty($params['attach'])){
            $query = Materials::find();
        }else
            $query = Materials::find()->leftJoin('material_attachment','material_attachment.material_id=materials.material_id')
                ->where('material_attachment.material_id is null and material_attachment.modify_material_id is null');
        // add conditions that should always apply here


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            //var_dump($this->getErrors());
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'materials.mfr_part_number', $this->mfr_part_number.'%',false])
            ->andFilterWhere(['like', 'materials.zc_part_number', $this->zc_part_number.'%',false]);

        return $dataProvider;
    }

}
