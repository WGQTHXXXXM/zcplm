<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\FileAttachments;

/**
 * FileAttachmentsSearch represents the model behind the search form about `frontend\models\FileAttachments`.
 */
class FileAttachmentsSearch extends FileAttachments
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_attachment_id', 'file_id', 'submitter_id', 'version', 'created_at', 'updated_at'], 'integer'],
            [['attachment_url'], 'safe'],
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
        $query = FileAttachments::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'file_attachment_id' => $this->file_attachment_id,
            'file_id' => $this->file_id,
            'submitter_id' => $this->submitter_id,
            'version' => $this->version,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'attachment_url', $this->attachment_url]);

        return $dataProvider;
    }
}
