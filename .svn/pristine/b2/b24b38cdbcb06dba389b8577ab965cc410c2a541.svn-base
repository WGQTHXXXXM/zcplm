<?php

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\UserTask;

/**
 * UserTaskSearch represents the model behind the search form about `frontend\models\UserTask`.
 */
class UserTaskSearch extends UserTask
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['user_id', 'userTaskUser','taskStatus','userTaskStatus'], 'safe'],
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
        $tbl = UserTask::find()->select('(max(updated_at)-min(created_at)) as sub,task_id')->groupBy('task_id');

        $query = UserTask::find()->leftJoin('tasks','user_task.task_id=tasks.id')
            ->leftJoin(['tbl'=>$tbl],'tbl.task_id=user_task.task_id')
            ->select('*,user_task.remark as userTaskRemark,user_task.status as userTaskStatus,tbl.sub as taskSub,
            user_task.id as id,tasks.status as taskStatus,tasks.date as dateApprove,tasks.user_id as taskUser,
            user_task.user_id as userTaskUser,(user_task.updated_at-user_task.created_at) as userTime');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => '10',
            ],
            'sort' => ['defaultOrder' => ['userTaskStatus' => SORT_ASC,'approve_able' => SORT_DESC,'dateApprove' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['user_id'] = [
            'asc' => ['user_task.user_id' => SORT_ASC],
            'desc' => ['user_task.user_id' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['taskUser'] = [
            'asc' => ['tasks.user_id' => SORT_ASC],
            'desc' => ['tasks.user_id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['userTaskStatus'] = [
            'asc' => ['user_task.status' => SORT_ASC],
            'desc' => ['user_task.status' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['taskStatus'] = [
            'asc' => ['tasks.status' => SORT_ASC],
            'desc' => ['tasks.status' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['dateApprove'] = [
            'asc' => ['tasks.date' => SORT_ASC],
            'desc' => ['tasks.date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['taskSub'] = [
            'asc' => ['tbl.sub' => SORT_ASC],
            'desc' => ['tbl.sub' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['userTime'] = [
            'asc' => ['userTime' => SORT_ASC],
            'desc' => ['userTime' => SORT_DESC],
        ];



        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            return $dataProvider;
        }

        //用户的搜索
        $user = '';
        if(!empty($this->userTaskUser))
        {
            $user = User::findOne(['username'=>$this->userTaskUser]);
            if(empty($user))
                $user = -1;
            else
                $user = $user->id;
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'user_task.' => $this->id,
            'user_task.task_id' => $this->task_id,
            'user_task.user_id' => $user,
            'user_task.updated_at' => $this->updated_at,
        ]);
        if(isset($params['UserTaskSearch']['userTaskStatus'])&&$params['UserTaskSearch']['userTaskStatus'] == 3){//如果是流程没到
            $query->andFilterWhere(['user_task.status' => UserTask::STATUS_UNAPPROVE]);
            $query->andFilterWhere(['user_task.approve_able'=>0]);
        }else if(isset($params['UserTaskSearch']['userTaskStatus'])&&$params['UserTaskSearch']['userTaskStatus'] == 0){//如果是待审批
            $query->andFilterWhere(['user_task.status' => UserTask::STATUS_UNAPPROVE]);
            $query->andFilterWhere(['user_task.approve_able'=>1]);
        }else
            $query->andFilterWhere(['user_task.status' => $this->userTaskStatus]);

        $query->andFilterWhere(['like', 'tasks.remark', $this->remark]);

        return $dataProvider;
    }
}
