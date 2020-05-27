<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "modules".
 *
 * @property integer $module_id
 * @property integer $project_id
 * @property string $name
 * @property string $category
 * @property string $milestone
 * @property double $produce_qty
 * @property string $date_entered
 *
 * @property Boms[] $boms
 * @property Projects $project
 */
class Modules extends \yii\db\ActiveRecord
{
    public $project_name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'modules';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'name', 'category', 'milestone'], 'required'],
            [['project_id'], 'integer'],
            [['produce_qty'], 'number'],
            [['date_entered'], 'safe'],
            [['name'], 'string', 'max' => 40],
            [['category', 'milestone'], 'string', 'max' => 10],
            [['project_id', 'name', 'milestone'], 'unique', 'targetAttribute' => ['project_id', 'name', 'milestone'], 'message' => 'The combination of Project ID, Name and Milestone has already been taken.'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::className(), 'targetAttribute' => ['project_id' => 'project_id']],
            [['project_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'module_id' => Yii::t('common', 'Module ID'),
            'project_id' => Yii::t('common', 'Project'),
            'name' => Yii::t('common', 'Modules'),
            'category' => Yii::t('common', 'Category'),
            'milestone' => Yii::t('common', 'Milestone'),
            'produce_qty' => Yii::t('common', 'Produce Qty'),
            'date_entered' => Yii::t('common', 'Date Entered'),
            'project_name' => Yii::t('common', 'Project'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoms()
    {
        return $this->hasMany(Boms::className(), ['module_id' => 'module_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['project_id' => 'project_id']);
    }
}
