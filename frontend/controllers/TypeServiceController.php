<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\models\TypeServiceForm;

require_once('connectvars.php');

class TypeServiceController extends Controller
{
    public function actionIndex()
    {
      $class1 = array();
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      if ($dbc) {
        mysqli_set_charset($dbc, 'utf8');
        $query = "SELECT id, type FROM type_list";
        $data = mysqli_query($dbc, $query);
        if (mysqli_num_rows($data) != 0) {
          while ($row = mysqli_fetch_array($data)) {
            $id = $row['id'];
            $type = $row['type'];
            $class1[$id] = $type;
          }
        }
        mysqli_close($dbc);
      }

      $model = new TypeServiceForm();
      return $this->render('index', ['model' => $model, 'class1' => $class1]);
    }

    public function actionGetClass2ByClass1id($filter_class1)
    {
      $output = '';
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      if ($dbc) {
        mysqli_set_charset($dbc, 'utf8');
        $id = $filter_class1;
        $query = "SELECT id, class FROM class_list WHERE type_id = '$id'";
        $data = mysqli_query($dbc, $query);

        $class2s = array();
        while ($row = mysqli_fetch_array($data, MYSQL_ASSOC)) {
          array_push($class2s, array('id' => $row['id'], 'class2' => $row['class']));
        }
        $output = json_encode(array("class2s" => $class2s));
        mysqli_close($dbc);
      }
      return $output;
    }

    public function actionGetClass3ByClass2id($filter_class2)
    {
      $output = '';
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      if ($dbc) {
        mysqli_set_charset($dbc, 'utf8');
        $class = $filter_class2;
        $query = "SELECT id, name, description FROM detail_list WHERE class_id = '$class'";
        $data = mysqli_query($dbc, $query);

        $class3s = array();
        while ($row = mysqli_fetch_array($data, MYSQL_ASSOC)) {
          array_push($class3s, array('id' => $row['id'], 'name' => $row['name'], 'description' => $row['description']));
        }
        $output = json_encode(array("class3s" => $class3s));
        mysqli_close($dbc);
      }
      return $output;
    }

    public function actionGetBrandByClass2id($filter_class2)
    {
      $output = '';
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      if ($dbc) {
        mysqli_set_charset($dbc, 'utf8');
        $class = $filter_class2;
        $query = "SELECT cbl.brand_id AS id, bl.brand AS brand " .
          "FROM class_brand_list AS cbl INNER JOIN brand_list AS bl " .
          "ON (cbl.brand_id = bl.id) " .
          "where cbl.class_id = '$class'";
        $data = mysqli_query($dbc, $query);

        $brands = array();
        while ($row = mysqli_fetch_array($data, MYSQL_ASSOC)) {
          array_push($brands, array('id' => $row['id'], 'brand' => $row['brand']));
        }
        $output = json_encode(array("brands" => $brands));
        mysqli_close($dbc);
      }
      return $output;
    }

    public function actionGetClass3nameByClass3id($filter_class3)
    {
      $output = '';
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      if ($dbc) {
        mysqli_set_charset($dbc, 'utf8');
        $id = $filter_class3;
        $query = "SELECT name FROM detail_list where id = '$id'";
        $data = mysqli_query($dbc, $query);

        if (mysqli_num_rows($data) == 1) {
          $row = mysqli_fetch_array($data, MYSQL_ASSOC);
          $name = $row['name'];
        } else {
          $name = "failed query";
        }
        $output = json_encode(array("name" => $name));
        mysqli_close($dbc);
      }
      return $output;
    }
}
