<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImportMaterialForm extends Model
{
    public $materialFile;

    public function rules()
    {
        return [
            [['materialFile'], 'file', 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'materialFile' => Yii::t('common', 'Material Csv File'),
        ];
    }
}
