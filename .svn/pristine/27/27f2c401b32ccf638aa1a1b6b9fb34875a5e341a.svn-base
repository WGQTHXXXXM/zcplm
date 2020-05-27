<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImportClassAndBrandForm extends Model
{
    public $classFile;
    public $brandFile;

    public function rules()
    {
        return [
            [['classFile'], 'file', 'skipOnEmpty' => false],
            [['brandFile'], 'file', 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'classFile' => Yii::t('common', 'Class Csv File'),
            'brandFile' => Yii::t('common', 'Brand Csv File'),
        ];
    }
}
