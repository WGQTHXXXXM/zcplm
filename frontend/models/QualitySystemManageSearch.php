<?php

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\QualitySystemManage;

/**
 * QualitySystemManageSearch represents the model behind the search form about `frontend\models\QualitySystemManage`.
 */
class QualitySystemManageSearch extends QualitySystemManage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'department_belong_id', 'file_class', 'status_submit', 'visible'], 'integer'],
            [['parent_name', 'son_name', 'file_code','name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {


        if(array_search('DCC',User::getUserRole(Yii::$app->user->id)) === false)//只有DCC显示所有的
            $query = QualitySystemManage::find()->where(['visible'=>1]);
        else
            $query = QualitySystemManage::find();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => '15',
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'department_belong_id' => $this->department_belong_id,
            'file_class' => $this->file_class,
            'status_submit' => $this->status_submit,
            'visible' => $this->visible,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'parent_name', $this->parent_name])
            ->andFilterWhere(['like', 'son_name', $this->son_name])
            ->andFilterWhere(['like', 'file_code', $this->file_code]);

        return $dataProvider;
    }
}
