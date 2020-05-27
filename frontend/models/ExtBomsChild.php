<?php

namespace frontend\models;


class ExtBomsChild extends BomsChild
{
    public $zc_part_number;//记得当宝哥上传更新后要删掉，因为宝哥的bomschild类里没有这个变量，当我保存时会出错

////////////////关联表//////////////////////////
    public function getExtBomsParent()
    {
        return $this->hasOne(ExtBomsParent::className(),['id'=>'boms_parent_id']);
    }

    public function getMaterial()
    {
        return $this->hasOne(Materials::className(), ['material_id' => 'child_id']);
    }

    public function getZcPartNo2()
    {
        return $this->hasOne(Materials::className(),['material_id'=>'zc_part_number2_id']);
    }

    public function getZcPartNo3()
    {
        return $this->hasOne(Materials::className(),['material_id'=>'zc_part_number3_id']);
    }

    public function getZcPartNo4()
    {
        return $this->hasOne(Materials::className(),['material_id'=>'zc_part_number4_id']);
    }

///////////////////////////////////////////////////////


    public function generateSelfByEcn($arrBomsChild)
    {
        $time = time();
        $this->load(['ExtBomsChild'=>$arrBomsChild]);
        $this->updated_at = $this->created_at = $time;
        $this->bom_expire_date = BomsParent::EXPIRE_DATE_MAX;
        if(!$this->save())
            return false;
        $model = new EcnBomidTmp();
        $model->ecn_id = $arrBomsChild['ecn_id'];
        $model->bom_id = $this->id;
        if(!$model->save())
            return false;
        return true;
    }







}