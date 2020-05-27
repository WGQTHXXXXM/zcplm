<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class BuildTemplateForm extends Model
{
    public $material;
    public $bom;

    public function rules()
    {
        return [
            [['material', 'bom'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'material' => Yii::t('common', 'Material File Name'),
            'bom' => Yii::t('common', 'Bom File Name'),
        ];
    }
}
