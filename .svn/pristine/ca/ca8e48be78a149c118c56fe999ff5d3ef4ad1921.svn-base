<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class TypeServiceForm extends Model
{
    public $class1;
    public $class2;
    public $class3;
    public $codenumber;
    public $brand;

    public function rules()
    {
        return [
            [['class1', 'class2','class3','codenumber','brand'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'class1' => 'Class1',
            'class2' => 'Class2',
            'class3' => 'Class3',
            'codenumber' => 'Code Number',
            'brand' => 'Brand',
        ];
    }
}
