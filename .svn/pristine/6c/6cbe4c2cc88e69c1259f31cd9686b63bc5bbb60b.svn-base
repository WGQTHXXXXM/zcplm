<?php

namespace frontend\models;



class Bomecn extends Boms
{
    /**
     * 得到使用者
     */
    public static function getParentByChild($idChild,$changeType)
    {
        if($changeType == 5)//替换二供的使用者，要有二三四供显示的;
        {
            $tbl = Boms::find()->alias('strbom')->leftJoin('materials as mtr_p','strbom.parent_id=mtr_p.material_id')
                ->leftJoin('materials as mtr1','strbom.child_id=mtr1.material_id')
                ->leftJoin('materials as mtr2','strbom.zc_part_number2_id=mtr2.material_id')
                ->leftJoin('materials as mtr3','strbom.zc_part_number3_id=mtr3.material_id')
                ->leftJoin('materials as mtr4','strbom.zc_part_number4_id=mtr4.material_id')
                ->select('strbom.id as idBom,mtr_p.zc_part_number as userZcPartNo,mtr_p.description as userDesc,
                        mtr1.zc_part_number as zcPartNo1,mtr2.zc_part_number as zcPartNo2,mtr3.zc_part_number as zcPartNo3,
                        mtr4.zc_part_number as zcPartNo4');
        }
        else
        {
            $tbl = Boms::find()->alias('strbom')->leftJoin('materials as mtr_p','strbom.parent_id=mtr_p.material_id')
                ->leftJoin('materials as mtr1','strbom.child_id=mtr1.material_id')
                ->select('strbom.id as idBom,mtr_p.zc_part_number as userZcPartNo,mtr_p.description as userDesc,
                        mtr1.zc_part_number as zcPartNo1');
        }
        //先做一个包含这个料的最新版本的表
        $tbla = Boms::find()->select('max(parent_version) as amax,parent_id as ap_id')->where(['child_id'=>$idChild])
            ->groupBy('parent_id');
        //把上表加上ID，并去掉删除的
        $tblb = Boms::find()->innerJoin(['tbla'=>$tbla],
            'boms.child_id='.$idChild.' AND tbla.ap_id=boms.parent_id AND tbla.amax=boms.parent_version')
            ->select('boms.id as idtblb')->where('boms.qty<>0');
        //变成智车料号的表
        return $tbl->innerJoin(['tblb'=>$tblb],'tblb.idtblb=strbom.id');
    }

    /**
     * bom关联物料表
     */
    public function getMtrParent()
    {
        return $this->hasOne(Materials::className(), ['material_id' => 'parent_id']);
    }
    public function getMtrChild()
    {
        return $this->hasOne(Materials::className(), ['material_id' => 'child_id']);
    }
    public function getZcPartNumber2()
    {
        return $this->hasOne(Materials::className(), ['material_id' => 'zc_part_number2_id']);
    }
    public function getZcPartNumber3()
    {
        return $this->hasOne(Materials::className(), ['material_id' => 'zc_part_number3_id']);
    }
    public function getZcPartNumber4()
    {
        return $this->hasOne(Materials::className(), ['material_id' => 'zc_part_number4_id']);
    }


}
