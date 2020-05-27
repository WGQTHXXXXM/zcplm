<?php
namespace api\controllers;

use api\models\Materials;
use yii\data\ActiveDataProvider;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class MaterialsController extends ActiveController
{
    public $modelClass = 'api\models\materials';

    public  function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function behaviors()
    {
        return ArrayHelper::merge([
            [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['*'],
                    'Access-Control-Request-Headers'=>['*']
                ],

            ],
        ], parent::behaviors());
    }

    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        return new ActiveDataProvider(
            [
                'query'=>$modelClass::find()->asArray(),
                'pagination'=>['pageSize'=>5],
            ]
        );
    }


    public function actionSearch()
    {
        $data = Materials::find();

        if(!empty($_GET['zc_part_number']))
            $data->andFilterWhere(['like','zc_part_number',$_GET['zc_part_number']]);

        if(!empty($_GET['part_name']))
            $data->andFilterWhere(['like','part_name',$_GET['part_name']]);

        if(!empty($_GET['description']))
            $data->andFilterWhere(['like','description',$_GET['description']]);

        return ['data'=>$data->all()];

    }

    public function actionRefMtr()
    {
        $strIn = \Yii::$app->request->get('ids');
        $arrIn = explode(',',$strIn);

        $pre_page = empty($_GET['pre_page'])?15:$_GET['pre_page'];
        $page = empty($_GET['page'])?0:$_GET['page']-1;
        $page = $page*$pre_page;

        return [
            'data'=>Materials::find()->andFilterWhere(['in','material_id',$arrIn])->limit($pre_page)->offset($page)->all(),
            'meta'=>[
                'pagination'=>[
                    'total'=>intval(Materials::find()->andFilterWhere(['in','material_id',$arrIn])->count()),
                    'count'=>$page,
                    'per_page'=>$pre_page,
                    'current_page'=>'',
                    'total_pages'=>'',
                    'links'=>'',
                ],

            ]
        ];

    }


}