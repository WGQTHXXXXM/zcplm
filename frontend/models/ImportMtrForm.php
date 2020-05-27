<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImportMtrForm extends Model
{

    public $mtrFile;

    public function rules()
    {
        return [
            [['mtrFile'], 'file', 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'mtrFile' => '上伟的文件',
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->mtrFile->saveAs('../uploads/importmtr/' . $this->mtrFile->baseName . '.' . $this->mtrFile->extension);
            return true;
        } else {
            return false;
        }
    }

}
