<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SearchForm;


class SearchController extends \yii\web\Controller
{

    /**
     * @return string
     */
    public function actionBomView()
    {
        $searchModel = new SearchForm();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('bom-view', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
