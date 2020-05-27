<?php

namespace frontend\models;

use common\components\CommonFunc;
use Yii;

/**
 * This is the model class for table "ecn_attachment".
 *
 * @property integer $id
 * @property string $path
 * @property string $name
 * @property integer $ecn_id
 */
class EcnAttachment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecn_attachment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['path', 'name', 'ecn_id'], 'required'],
            [['ecn_id'], 'integer'],
            [['path', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('material', 'ID'),
            'path' => Yii::t('material', '保存的路径'),
            'name' => Yii::t('material', '文件名'),
            'ecn_id' => Yii::t('material', '对应的ecn表的id'),
        ];
    }

    /**
     * 保存ECN附件
     */
    public function saveAttachment($id)
    {
        if(!isset($_FILES['attachment']))//当控件为空时ie浏览器检测不到这个
            return true;
        $attachments = $_FILES['attachment'];

        $fileName = $attachments['name'];
        //生成文件名和保存的路径
        $path = '../uploads/ecn/';
        if (!is_dir($path))
            mkdir($path);
        //保存时的随机名
        $nameRandom = CommonFunc::genRandomString(9).'.'.pathinfo(basename($fileName))['extension'];
        while(file_exists('../uploads/ecn/'.$nameRandom))//看文件是否存在
            $nameRandom = CommonFunc::genRandomString(9).'.'.pathinfo(basename($fileName))['extension'];
        $path = $path . $nameRandom;

        //保存附件数据库
        $this->ecn_id = $id;
        $this->name = $fileName;
        $this->path = $path;
        if(!$this->save())
        {
            var_dump($this->getErrors());die;
        }

        //上传文件
        if(!move_uploaded_file($attachments['tmp_name'],$path))
            return false;

        return true;
    }




}
