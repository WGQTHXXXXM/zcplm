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
class ProjectProcessTemplate extends \kartik\tree\models\Tree
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_process_template';
    }

    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    public function attributes()
    {
        //return parent::attributes(); // TODO: Change the autogenerated stub

        $arr = parent::attributes();
        return array_merge(['department_id'=>'所属部门'],$arr);

    }
}
