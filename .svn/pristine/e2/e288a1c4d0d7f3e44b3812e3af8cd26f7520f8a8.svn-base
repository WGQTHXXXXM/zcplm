<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\models\BuildClassAndBrandTemplateForm;
use frontend\models\ImportClassAndBrandForm;
use yii\web\UploadedFile;

require_once('connectvars.php');
class ImportClassAndBrandController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionBuildTemplate()
    {
      $model = new BuildClassAndBrandTemplateForm();

      if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        $this->createClassTemplate(Yii::getAlias('@uploads/') . $model->class);
        $this->createBrandTemplate(Yii::getAlias('@uploads/') . $model->brand);
        return $this->render('download-template', ['model' => $model]);
      } else {
        return $this->render('build-template', ['model' => $model]);
      }
    }
    
    public function actionGetTemplate($file)
    {
      return \Yii::$app->response->sendFile(Yii::getAlias('@uploads/') . $file);
    }

    public function actionImportClassAndBrand()
    {
      $model = new ImportClassAndBrandForm();

      if (Yii::$app->request->isPost) {
        $model->classFile = UploadedFile::getInstance($model, 'classFile');
        $model->brandFile = UploadedFile::getInstance($model, 'brandFile');
        if ($model->validate()) {
          $classfile = $model->classFile->tempName;
          $brandfile = $model->brandFile->tempName;
          $result = $this->importClassAndBrands($classfile, $brandfile);

          return $this->render('import-class-and-brand-result', ['result' => $result]);
        }
      }
      return $this->render('import-class-and-brand-file', ['model' => $model]);
    }

    protected function createClassTemplate($file)
    {
      $class_fields = array("class1","class2","class3","description");
      $classfile = $file;

      $handle = fopen($classfile, "w");
      if ($handle == false) {
        return false;
      } 

      $num_column = count($class_fields);

      $csv_header = '';
      for ($i = 0; $i < $num_column; $i++) {
        $csv_header .= '"' . $class_fields [$i] . '",';
      }
      $csv_header .= "\n";
      fputs($handle, $csv_header);

      fclose($handle);
      return true;
    }

    protected function createBrandTemplate($file)
    {
      $brand_fields = array("class2","brand");
      $brandfile = $file;

      $handle = fopen($brandfile , "w");
      if ($handle == false) {
        return false;
      } 

      $num_column = count($brand_fields);

      $csv_header = '';
      for ($i = 0; $i < $num_column; $i++) {
        $csv_header .= '"' . $brand_fields[$i] . '",';
      }
      $csv_header .= "\n";
      fputs($handle, $csv_header);

      fclose($handle);
      return true;
    }

    protected function importClassAndBrands($classfile, $brandfile)
    {
      $result = true;
      //connect to database
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      if (!$dbc) {
        $result = mysqli_connect_error();
        return $result;
      }

      mysqli_set_charset($dbc, 'utf8');

      //clear all tables on material type, firstly.
      $mytables = array("boms_materials", "boms", "modules", "projects", "materials",
                   "class_brand_list","brand_list","detail_list", "class_list","type_list");
      $number = count($mytables);
      for ($i = 0; $i < $number; $i++) {
        $table = $mytables[$i];
        $query = "DELETE FROM $table";
        $result = mysqli_query($dbc, $query);
        if ($result == false) {
          $result = mysqli_error($dbc);
          mysqli_close($dbc);
          return $result;
        }
      }

      //import class csv file
      $fp = fopen($classfile, "r");
      if ($fp == false) {
        $result = print_r(error_get_last(), true);
        mysqli_close($dbc);
        return $result;
      }

      $isheader = true;
      $number = 0;
      $last_type = '';
      $last_class = '';

      $success = true;
      $my_error = '';
      mysqli_autocommit($dbc, false);
      while ($data = fgetcsv($fp, 1000, ",")) {
        if ($isheader == true) {
          $isheader = false;
          continue;
        } else {
          $number++;
        }

        $type = mysqli_real_escape_string($dbc, trim($data[0])); 
        $class = mysqli_real_escape_string($dbc, trim($data[1]));
        $name = mysqli_real_escape_string($dbc, trim($data[2]));
        $description = mysqli_real_escape_string($dbc, trim($data[3]));

        //insert new type into type_list;
        if ($type != $last_type) {
          $query = "INSERT INTO type_list (type) VALUES ('$type')";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }

          $query = "SELECT id FROM type_list where type='$type'";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }
          if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_row($result);
            $type_id = $row[0];
          } else {
            $my_error = "type_list id query is weird, please check, type is $type";
            $success = false;
            break;
          }
          $last_type = $type;
        } 

        //insert class and type_id into class_list
        if ($class != $last_class) {
          $query = "INSERT INTO class_list (class, type_id) VALUES ('$class','$type_id')";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }
          $query = "SELECT id FROM class_list where class='$class'";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }
          if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_row($result);
            $class_id = $row[0];
          } else {
            $my_error = "class_id id query is weird, please check, class is $class";
            $success = false;
            break;
          }
          $last_class = $class;
        }

        //insert name,description,class_id into detail_list
        $query = "INSERT INTO detail_list (name, description, class_id) VALUES ('$name', '$description', '$class_id')";
        $result = mysqli_query($dbc, $query);
        if ($result == false) {
          $success = false;
          break;
        }
      }
      //close the class file
      fclose($fp);

      if ($success) {
        mysqli_commit($dbc);
        $class_result = Yii::t('common', 'Importing {number} items from class file successfully.', ['number' => $number]);
      } else {
        $result = ($my_error == '') ? mysqli_error($dbc) : $my_error;
        mysqli_rollback($dbc);
        $result .= "<br>The importing class has been canceled.";

        mysqli_close($dbc);
        return $result;
      }

      //import brand csv file
      $fp = fopen($brandfile, "r");
      if ($fp == false) {
        $result = print_r(error_get_last(), true);
        $result = $class_result . '<br>' . $result;
        mysqli_close($dbc);
        return $result;
      }

      $isheader = true;
      $number = 0;
      $last_class = '';

      $success = true;
      $my_error = '';
      mysqli_autocommit($dbc, false);
      while ($data = fgetcsv($fp, 1000, ",")) {
        if ($isheader == true) {
          $isheader = false;
          continue;
        } else {
          $number++;
        }

        $class = mysqli_real_escape_string($dbc, trim($data[0]));
        $brand = mysqli_real_escape_string($dbc, trim($data[1]));

        if ($class != $last_class) {
          $query = "SELECT id FROM class_list where class='$class'";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }
          if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_row($result);
            $class_id = $row[0];
          } else {
            $my_error = "class_list id query is weird, please check, class is $class";
            $success = false;
            break;
          }
          $last_class = $class;
        }

        //insert new brand into brand_list;
        $query = "SELECT id FROM brand_list where brand='$brand'";
        $result = mysqli_query($dbc, $query);
        if ($result == false) {
          $success = false;
          break;
        }
        if (mysqli_num_rows($result) == 0) {
          $query = "INSERT INTO brand_list (brand) VALUES ('$brand')";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }

          $query = "SELECT id FROM brand_list where brand='$brand'";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }
          if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_row($result);
            $brand_id = $row[0];
          } else {
            $my_error = "brand_list id query is weird, please check, brand is $brand";
            $success = false;
            break;
          }
        } else {
          //the brand has been inserted before
          $row = mysqli_fetch_row($result);
          $brand_id = $row[0];
        }

        $query = "INSERT INTO class_brand_list (class_id, brand_id) VALUES ('$class_id', '$brand_id')";
        $result = mysqli_query($dbc, $query);
        if ($result == false) {
          $success = false;
          break;
        }
      }

      if ($success) {
        mysqli_commit($dbc);
        $result = Yii::t('common', 'Importing {number} items from brand file successfully.', ['number' => $number]);
      } else {
        $result = ($my_error == '') ? mysqli_error($dbc) : $my_error;
        mysqli_rollback($dbc);
        $result .= "<br>The item$number is abnormal.<br>The importing brand has been canceled.";
      }
      $result = $class_result . '<br>' . $result;

      //close the class file
      fclose($fp);
      
      //close database connection
      mysqli_close($dbc);

      return $result;
    }
}
