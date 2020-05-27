<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Modules;
use frontend\models\Projects;
use yii\web\NotFoundHttpException;
use yii\helpers\Json;
use common\components\CommonFunc;
use yii\data\SqlDataProvider;


class PurchaseBomController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $modules = new Modules();
        $projects = Projects::find()->select(['project_id', 'name'])->indexBy('project_id')->asArray()->all();

        return $this->render('index', [
            'modules' => $modules,
            'projects' => $projects,
        ]);
    }

    public function actionView()
    {
        $modules = new Modules();

        if ($modules->load(Yii::$app->request->post())) {
            $project = Projects::findOne($modules->project_id);
            $modules->project_name = $project->name;

            // Retrieve the module milestone information:
            $sql = "SELECT name FROM modules WHERE project_id='$modules->project_id' && milestone='$modules->milestone' && category='$modules->category' ORDER BY name ASC";
            $rows = Modules::findBySql($sql)->asArray()->all();

            // Count the number of returned rows:
            $num = Modules::findBySql($sql)->count();
            $sum_qty_as_module="";
            $sum_produce_qty_as_module="";
            if ($num > 0) // If it ran OK, display the records.
            {
                // Fetch and print all the records:
                foreach ($rows as $key => $row) {
                    $sum_qty_as_module = $sum_qty_as_module . "SUM(CASE name WHEN '" . $row['name'] . "' THEN qty END) AS '" . $row['name'] . "', ";
                    $sum_produce_qty_as_module = $sum_produce_qty_as_module . "SUM(CASE name WHEN '" . $row['name'] . "' THEN qty*produce_qty END) AS '" . $row['name'] . " (生产数量)', ";
                }
            }

            // Define the query:
            $sql = "SELECT zc_part_no, part_no, part_name, description, supplier_name, 
             second_source_part_no, second_source_supplier_name, "
                . $sum_qty_as_module .
                "SUM(qty) AS BOM总用量, "
                . $sum_produce_qty_as_module .
                "SUM(qty*produce_qty) AS 生产总需求 
                    FROM 
             (SELECT m1.zc_part_no AS zc_part_no, m1.part_no AS part_no, m1.part_name AS part_name, m1.description AS description, bl1.brand AS supplier_name, 
                     m2.part_no AS second_source_part_no, bl2.brand AS second_source_supplier_name, 
                     mo.name AS name, b.qty AS qty, mo.produce_qty AS produce_qty 
              FROM modules AS mo INNER JOIN boms AS b USING (module_id) 
              INNER JOIN boms_materials AS b_m1 ON b.bom_id=b_m1.bom_id && b_m1.supplier_priority=1 INNER JOIN materials AS m1 USING (material_id) LEFT JOIN brand_list AS bl1 ON (m1.supplier_name_id=bl1.id) 
              LEFT JOIN boms_materials AS b_m2 ON b_m1.bom_id=b_m2.bom_id && b_m2.supplier_priority=2 LEFT JOIN materials AS m2 ON b_m2.material_id=m2.material_id LEFT JOIN brand_list AS bl2 ON (m2.supplier_name_id=bl2.id) 
              WHERE mo.category='$modules->category' && mo.milestone='$modules->milestone' && mo.project_id='$modules->project_id' 
              GROUP BY m1.material_id, mo.name 
              ORDER BY part_no, name) AS subsql 
              GROUP BY part_no 
              ORDER BY part_no ASC";
            $count = Modules::findBySql($sql)->count();
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'totalCount' => $count,
                'pagination' => [
                    'pageSize' => $count,
                ],
            ]);

            $heading = '类别：'. $modules->category .'&nbsp;&nbsp;&nbsp;('. $modules->milestone .' BOM for '. $modules->project_name .')';

            $gridColumns = [
                [
                    'class' => 'kartik\grid\SerialColumn',
                    'pageSummary'=>'合计',
                    'pageSummaryOptions'=>['class'=>'text-center text-warning'],
                ],
                [
                    'attribute' => 'zc_part_no',
                    'label' => Yii::t('common', 'Zc Part No'),
                ],
                [
                    'attribute' => 'part_no',
                    'label' => Yii::t('common', 'Part No.'),
                    'visible' => $_POST['part_no_toggle'] == 0,
                ],
                [
                    'attribute' => 'part_name',
                    'label' => Yii::t('common', 'Part Name'),
                    'visible' => $_POST['part_name_toggle'] == 0,
                ],
                [
                    'attribute' => 'description',
                    'label' => Yii::t('common', 'Description'),
                    'visible' => $_POST['description_toggle'] == 0,
                ],
                [
                    'attribute' => 'supplier_name',
                    'label' => Yii::t('common', 'Supplier Name'),
                    'visible' => $_POST['supplier_name_toggle'] == 0,
                ],
                [
                    'attribute' => 'second_source_part_no',
                    'label' => Yii::t('common', '2nd Source P/N'),
                    'visible' => $_POST['second_source_part_no_toggle'] == 0,
                ],
                [
                    'attribute' => 'second_source_supplier_name',
                    'label' => Yii::t('common', '2nd Source Supplier'),
                    'visible' => $_POST['second_source_supplier_name_toggle'] == 0,
                ],
            ];

            if ($num > 0) // 增加Module模块列及数量统计列.
            {
                foreach ($rows as $key => $row) {
                    $gridColumn = [
                        'attribute' => $row['name'],
                        'label' => Yii::t('common', $row['name']),
                        'hAlign' => 'center',
                        //  'format'=>['decimal', 2],
                        'pageSummary'=>true
                    ];
                    array_push($gridColumns, $gridColumn);
                }

                $gridColumn = [
                    'attribute' => 'BOM总用量',
                    'label' => Yii::t('common', 'BOM总用量'),
                    'hAlign' => 'center',
                    //  'format'=>['decimal', 2],
                    'pageSummary'=>true
                ];
                array_push($gridColumns, $gridColumn);

                foreach ($rows as $key => $row) {
                    $gridColumn = [
                        'attribute' => $row['name'].' (生产数量)',
                        'label' => Yii::t('common', $row['name'].' (生产数量)'),
                        'hAlign' => 'center',
                        //  'format'=>['decimal', 2],
                        'pageSummary'=>true
                    ];
                    array_push($gridColumns, $gridColumn);
                }

                $gridColumn = [
                    'attribute' => '生产总需求',
                    'label' => Yii::t('common', '生产总需求'),
                    'hAlign' => 'center',
                    //  'format'=>['decimal', 2],
                    'pageSummary'=>true
                ];
                array_push($gridColumns, $gridColumn);
            }

            return $this->render('view', [
                'modules' => $modules,
                'dataProvider' => $dataProvider,
                'gridColumns' => $gridColumns,
                'heading' => $heading,
            ]);
        }
    }

    /**
     * This function is used to response jQuery Ajax request.
     * @return Json::encode()
     * This function is created by syb.
     */
    public function actionAjax()
    {
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $action = $request->get('action');

            $data = NULL;
            if ($action == "getmilestoneOption"){
                $project_id = $request->get('project_id');

                if ($project_id != "") {
                    $data = Modules::find()->select(['milestone'])->where(['project_id' => $project_id])->indexBy('milestone')->orderBy('milestone')->asArray()->all();
                }

                //  \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return Json::encode($data);
            }
        }
    }

    /**
     * @DESC 数据导出
     */
    public function actionExportData()
    {
        $modules = new Modules();

        if ($modules->load(Yii::$app->request->post())) {
            $project = Projects::findOne($modules->project_id);
            $modules->project_name = $project->name;
            if ($modules->category == "电子") { // 电子BOM无Part Name项，设置为不显示
                $_POST["part_name_toggle"] = 1;
            }

            $header = [
                ['header'=>'Item', 'width'=>7],
                ['header'=>Yii::t('common', 'Zc Part No'), 'width'=>10],
                ['header'=>$_POST["part_no_toggle"] ? "" : Yii::t('common', 'Part No.'), 'width'=>20],
                ['header'=>$_POST["part_name_toggle"] ? "" : Yii::t('common', 'Part Name'), 'width'=>25],
                ['header'=>$_POST["description_toggle"] ? "" : Yii::t('common', 'Description'), 'width'=>50],
                ['header'=>$_POST["supplier_name_toggle"] ? "" : Yii::t('common', 'Supplier Name'), 'width'=>15],
                ['header'=>$_POST["second_source_part_no_toggle"] ? "" : Yii::t('common', '2nd Source P/N'), 'width'=>20],
                ['header'=>$_POST["second_source_supplier_name_toggle"] ? "" : Yii::t('common', '2nd Source Supplier'), 'width'=>15],
            ]; //导出excel的表头

            // Retrieve the module milestone information:
            $sql = "SELECT name FROM modules WHERE project_id='$modules->project_id' && milestone='$modules->milestone' && category='$modules->category' ORDER BY name ASC";
            $rows = Modules::findBySql($sql)->asArray()->all();

            // Count the number of returned rows:
            $num = Modules::findBySql($sql)->count();
            $sum_qty_as_module="";
            $sum_produce_qty_as_module="";
            if ($num > 0) // If it ran OK, display the records.
            {
                // Fetch and print all the records:
                foreach ($rows as $key => $row) {
                    $sum_qty_as_module = $sum_qty_as_module . "SUM(CASE name WHEN '" . $row['name'] . "' THEN qty END) AS module_" . $key . ", ";
                    array_push($header, ['header'=>$row['name'], 'width'=>7]);
                    $sum_produce_qty_as_module = $sum_produce_qty_as_module . "SUM(CASE name WHEN '" . $row['name'] . "' THEN qty*produce_qty END) AS produce_module_" . $key . ", ";
                }
                array_push($header, ['header'=>"BOM总用量", 'width'=>7]);
                foreach ($rows as $row) {
                    array_push($header, ['header'=>$row['name'].' (生产数量)', 'width'=>7]);
                }
                array_push($header, ['header'=>"生产总需求", 'width'=>7]);
            }

            $select = "zc_part_no,";

            $part_no = $_POST["part_no_toggle"] ? "" : "part_no,";
            $part_name = $_POST["part_name_toggle"] ? "" : "part_name,";
            $description = $_POST["description_toggle"] ? "" : "description,";
            $supplier_name = $_POST["supplier_name_toggle"] ? "" : "supplier_name,";
            $second_source_part_no = $_POST["second_source_part_no_toggle"] ? "" : "second_source_part_no,";
            $second_source_supplier_name = $_POST["second_source_supplier_name_toggle"] ? "" : "second_source_supplier_name,";
            $select .= $part_no . $part_name . $description . $supplier_name . $second_source_part_no . $second_source_supplier_name;

            //查询数据
            $sql = "SELECT " . $select
                . $sum_qty_as_module .
                "SUM(qty) AS bom_total, "
                . $sum_produce_qty_as_module .
                "SUM(qty*produce_qty) AS produce_total 
                 FROM 
             (SELECT m1.zc_part_no AS zc_part_no, m1.part_no AS part_no, m1.part_name AS part_name, m1.description AS description, bl1.brand AS supplier_name, 
                     m2.part_no AS second_source_part_no, bl2.brand AS second_source_supplier_name, 
                     mo.name AS name, b.qty AS qty, mo.produce_qty AS produce_qty 
              FROM modules AS mo INNER JOIN boms AS b USING (module_id) 
              INNER JOIN boms_materials AS b_m1 ON b.bom_id=b_m1.bom_id && b_m1.supplier_priority=1 INNER JOIN materials AS m1 USING (material_id) LEFT JOIN brand_list AS bl1 ON (m1.supplier_name_id=bl1.id) 
              LEFT JOIN boms_materials AS b_m2 ON b_m1.bom_id=b_m2.bom_id && b_m2.supplier_priority=2 LEFT JOIN materials AS m2 ON b_m2.material_id=m2.material_id LEFT JOIN brand_list AS bl2 ON (m2.supplier_name_id=bl2.id) 
              WHERE mo.category='$modules->category' && mo.milestone='$modules->milestone' && mo.project_id='$modules->project_id' 
              GROUP BY m1.material_id, mo.name 
              ORDER BY part_no, name) AS subsql 
              GROUP BY part_no 
              ORDER BY part_no ASC
              ";
            $data = Modules::findBySql($sql)->asArray()->all();

            if ($data) { // 数据处理
                // 给每一行记录添加Item序号
                for ($item=0; $item<count($data); $item++) {
                    array_unshift($data[$item], $item+1);
                }

                // 表的最后一行统计各列合计数量：
                $sum_temp="";
                for ($i=0; $i<$num; $i++) {
                    $th_module = 'module_' . $i;
                    $sum_temp = $sum_temp . "SUM($th_module) AS $th_module, ";
                }
                $sum_temp = $sum_temp . "SUM(bom_total) AS bom_total, ";
                for ($i=0; $i<$num; $i++) {
                    $th_module = 'produce_module_' . $i;
                    $sum_temp = $sum_temp . "SUM($th_module) AS $th_module, ";
                }
                $sum_temp = $sum_temp . "SUM(produce_total) AS produce_total ";

                $q_sum_module = "SELECT " . $sum_temp . " FROM (" . $sql .") AS lastsql";
                $row = Modules::findBySql($q_sum_module)->asArray()->one();
                $times = count($data[0])-count($row);
                for ($i=1; $i<$times; $i++) { // 前面补空
                    array_unshift($row, "");
                }
                array_unshift($row, "合计");
                array_push($data, $row);
            }

            $filename = $modules->category . '(' . $modules->milestone . ' BOM for ' . $modules->project_name . ')';
            $worksheet = ($modules->category == '电子') ? "前线BOM发料" : "后线结构物料";
            if (!empty($_POST["filename"])) $filename = $_POST["filename"];
            if (!empty($_POST["worksheet"])) $worksheet = $_POST["worksheet"];

            CommonFunc::exportData($data, $header, $worksheet, $filename);
        } else {
            $projects = Projects::find()->select(['project_id', 'name'])->indexBy('project_id')->asArray()->all();
            return $this->render('index', [
                'modules' => $modules,
                'projects' => $projects,
            ]);
        }
    }
}
