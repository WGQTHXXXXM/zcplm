<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImportBomForm extends Model
{
    public $bomFile,$zcPartNo,$merId;


    public function rules()
    {
        return [
            [['zcPartNo','merId'], 'required'],

            [['bomFile'], 'file', 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'bomFile' => 'BOM文件',
            'zcPartNo' => "智车料号",
        ];
    }

}