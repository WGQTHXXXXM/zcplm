<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Ecr;

/**
 * EcrSearch represents the model behind the search form about `frontend\models\Ecr`.
 */
class EcrSearch extends Ecr
{


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['serial_number', 'created_at', 'updated_at', 'reason', 'detail', 'bom_id','project_process_id',
                'status','user','project_id','projectName','projectProcessName','bom'], 'safe'],
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
        $query = Ecr::find();

        $strEcr = Tasks::TASK_TYPE_ECR1.','.Tasks::TASK_TYPE_ECR2.','.Tasks::TASK_TYPE_ECR3.','.Tasks::TASK_TYPE_ECR4;
        $query->innerJoin('tasks','tasks.type in ('.$strEcr.') and tasks.type_id=ecr.id')
            ->select('ecr.*,tasks.status,projects.name as projectName,materials.zc_part_number as bom,project_process.name as projectProcessName');
        $query->innerJoin('user','tasks.user_id = user.id');
        $query->leftJoin('projects','projects.id=ecr.project_id');
        $query->leftJoin('project_process','project_process.id=ecr.project_process_id');
        $query->leftJoin('materials','materials.material_id=ecr.bom_id');
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
        $dataProvider->sort->attributes['bom'] =
            [
                'asc'=>['materials.zc_part_number'=>SORT_ASC],
                'desc'=>['materials.zc_part_number'=>SORT_DESC],
            ];
        $dataProvider->sort->attributes['projectProcessName'] =
            [
                'asc'=>['project_process.name'=>SORT_ASC],
                'desc'=>['project_process.name'=>SORT_DESC],
            ];
        $dataProvider->sort->attributes['projectName'] =
            [
                'asc'=>['projects.name'=>SORT_ASC],
                'desc'=>['projects.name'=>SORT_DESC],
            ];

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'serial_number', $this->serial_number])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->andFilterWhere(['like', 'materials.zc_part_number', $this->bom])
            ->andFilterWhere(['like', 'project_process.name', $this->projectProcessName])
            ->andFilterWhere(['like', 'projects.name', $this->projectName])
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
        $query->andFilterWhere(['between','ecr.created_at',$ctimeBg,$ctimeEn])
            ->andFilterWhere(['between','ecr.updated_at',$utimeBg,$utimeEn]);



        return $dataProvider;
    }

}
