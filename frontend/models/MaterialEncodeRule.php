<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "tree_tpls".
 *
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $lvl
 * @property string $name
 * @property string $icon
 * @property integer $icon_type
 * @property integer $active
 * @property integer $selected
 * @property integer $disabled
 * @property integer $readonly
 * @property integer $visible
 * @property integer $collapsed
 * @property integer $movable_u
 * @property integer $movable_d
 * @property integer $movable_l
 * @property integer $movable_r
 * @property integer $removable
 * @property integer $removable_all
 */
class MaterialEncodeRule extends \kartik\tree\models\Tree
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material_encode_rule';
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['remark','num'], 'safe'];
        return $rules;
    }

    public function attributeLabels()
    {
        $atb = parent::attributeLabels();
        $atb['num'] = '位数';
        $atb['remark'] = '编码号';
        return $atb;
    }

    public function getFather($arr = ["lvl","root","lft","rgt","id","remark","name"])
    {
        return MaterialEncodeRule::find()->where(['lvl'=>$this->lvl-1,'root'=>$this->root])->andWhere(['<','lft',$this->lft])
            ->andWhere(['>','rgt',$this->rgt])->select($arr)->all()[0];
    }

    public function getSon($arr = ["lvl","root","lft","rgt","id","remark","name"])
    {
        return MaterialEncodeRule::find()->where(['lvl'=>$this->lvl+1,'root'=>$this->root])->andWhere(['>','lft',$this->lft])
            ->andWhere(['<','rgt',$this->rgt])->OrderBy('lft')->select($arr)->all();
    }


}