<?php

namespace frontend\controllers;

use backend\models\VersionSearch;
use yii\web\Controller;
use backend\models\Version;
use Yii;

class VersionController extends Controller
{

    function actionIndex()
    {
        $searchModel = new VersionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 查看最新版本
     */
    function actionView()
    {
        $model = Version::find()->orderBy('id')->one();
        return $this->render('view',['model'=>$model]);
    }

}
