<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class BuildClassAndBrandTemplateForm extends Model
{
    public $class;
    public $brand;

    public function rules()
    {
        return [
            [['class', 'brand'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'class' => Yii::t('common', 'Class File Name'),
            'brand' => Yii::t('common', 'Brand File Name'),
        ];
    }
}
