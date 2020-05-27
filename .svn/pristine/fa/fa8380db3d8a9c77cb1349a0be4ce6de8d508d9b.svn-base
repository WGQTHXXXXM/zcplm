<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EcnSearch represents the model behind the search form about `frontend\models\Ecn`.
 */
class EcnSearch extends Ecn
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'change_now', 'affect_stock', 'ecr_id'], 'integer'],
            [['serial_number','remark','status','user', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['status','user']);
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
        $query = Ecn::find();
        $strEcr = Tasks::TASK_TYPE_ECN1.','.Tasks::TASK_TYPE_ECN2.','.Tasks::TASK_TYPE_ECN3.','.Tasks::TASK_TYPE_ECN4;

        $query->innerJoin('tasks','tasks.type in ('.$strEcr.')  and tasks.type_id=ecn.id')->select('ecn.*,tasks.status');
        $query->innerJoin('user','tasks.user_id = user.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->sort->attributes['status'] =
            [
                'asc'=>['tasks.status'=>SORT_ASC],
                'desc'=>['tasks.status'=>SORT_DESC],
            ];
        $dataProvider->sort->attributes['user'] =
            [
                'asc'=>['user.username'=>SORT_ASC],
                'desc'=>['user.username'=>SORT_DESC],
            ];


        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'serial_number', $this->serial_number])
            ->andFilterWhere(['like', 'user.username', $this->user])
            ->andFilterWhere(['like', 'tasks.status', $this->status]);
        //时间的搜索用，没有值时是空（就不会搜索这个字段），有值时才会搜索，并把日期转成时间截。
        $ctimeBg = '';
        $ctimeEn = '';
        $utimeBg = '';
        $utimeEn = '';
        if(!empty($this->created_at)){
            $ctimeBg = strtotime(substr(str_replace(['年','月','日'],'-',$this->created_at),0,-1));
            $ctimeEn = $ctimeBg+Yii::$app->params['timeStrapOneDay'];
        }
        if(!empty($this->updated_at)){
            $utimeBg = strtotime(substr(str_replace(['年','月','日'],'-',$this->updated_at),0,-1));
            $utimeEn = $utimeBg+Yii::$app->params['timeStrapOneDay'];
        }
        $query->andFilterWhere(['between','ecn.created_at',$ctimeBg,$ctimeEn])
            ->andFilterWhere(['between','ecn.updated_at',$utimeBg,$utimeEn]);

        return $dataProvider;
    }
}
