<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Modules;

/**
 * ModulesSearch represents the model behind the search form about `frontend\models\Modules`.
 */
class ModulesSearch extends Modules
{
  //  public $project_name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id', 'project_id'], 'integer'],
            [['name', 'category', 'milestone', 'date_entered'], 'safe'],
            [['produce_qty'], 'number'],
            [['project_name'], 'safe'],
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
        $query = Modules::find();
        $query->joinWith(['project']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['project_name'] = [
            'asc' => ["projects.name" => SORT_ASC],
            'desc' => ["projects.name" => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'module_id' => $this->module_id,
            'project_id' => $this->project_id,
            'produce_qty' => $this->produce_qty,
            'date_entered' => $this->date_entered,
        ]);

        $query->andFilterWhere(['like', 'projects.name', $this->project_name])
            ->andFilterWhere(['like', 'modules.name', $this->name])
            ->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'milestone', $this->milestone]);

        return $dataProvider;
    }
}
