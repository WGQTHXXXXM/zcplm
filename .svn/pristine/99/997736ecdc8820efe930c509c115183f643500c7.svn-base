<?php

namespace frontend\models;

use Yii;
use yii\web\UploadedFile;

class Upload extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile|Null file attribute
     */
    public $upload_file;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['upload_file'], 'file', 'skipOnEmpty' => false, 'maxFiles' => 2],
            [['upload_file'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'upload_file' => Yii::t('common', 'Upload File'),
        ];
    }
}