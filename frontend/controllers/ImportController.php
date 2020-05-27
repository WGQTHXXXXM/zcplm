<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\models\BuildTemplateForm;
use frontend\models\ImportMaterialForm;
use frontend\models\ImportBomForm;
use yii\web\UploadedFile;

require_once('connectvars.php');
class ImportController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionBuildTemplate()
    {
      $model = new BuildTemplateForm();

      if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        $this->createMaterialTemplate(Yii::getAlias('@uploads/') . $model->material);
        $this->createBomTemplate(Yii::getAlias('@uploads/') . $model->bom);
        return $this->render('download-template', ['model' => $model]);
      } else {
        return $this->render('build-template', ['model' => $model]);
      }
    }
    
    public function actionGetTemplate($file)
    {
      return \Yii::$app->response->sendFile(Yii::getAlias('@uploads/') . $file);
    }

    public function actionImportMaterial()
    {
      $model = new ImportMaterialForm();

      if (Yii::$app->request->isPost) {
        $model->materialFile = UploadedFile::getInstance($model, 'materialFile');
        if ($model->validate()) {
          $file = $model->materialFile->tempName;
          $result = $this->importMaterial($file);

          return $this->render('import-material-result', ['result' => $result]);
        }
      }
      return $this->render('import-material-file', ['model' => $model]);
    }

    public function actionImportBom()
    {
      $model = new ImportBomForm();

      if (Yii::$app->request->isPost) {
        $model->bomFile = UploadedFile::getInstance($model, 'bomFile');
        if ($model->validate()) {
          $file = $model->bomFile->tempName;
          $result = $this->ImportBom($file);

          return $this->render('import-bom-result', ['result' => $result]);
        }
      }
      return $this->render('import-bom-file', ['model' => $model]);
    }

    protected function createMaterialTemplate($file)
    {
      $material_fields = array("zc_part_no", "part_no", "part_name", "description",
          "unit", "pcb_footprint", "supplier_name", "part", "vehicle_standard", "remark");
      $materialfile = $file;

      $handle = fopen($materialfile, "w");
      if ($handle == false) {
        return false;
      } 

      $num_column = count($material_fields);

      $csv_header = '';
      for ($i = 0; $i < $num_column; $i++) {
        $csv_header .= '"' . $material_fields[$i] . '",';
      }
      $csv_header .= "\n";
      fputs($handle, $csv_header);

      fclose($handle);
      return true;
    }

    protected function createBomTemplate($file)
    {
      $bom_fields = array("project name",
          "module name", "category", "milestone", "produce qty", 
          "assy level", "purchase level", "qty", "reference No", "remark",
          "part No", "2nd source P/N");
      $bomfile = $file;

      $handle = fopen($bomfile, "w");
      if ($handle == false) {
        return false;
      } 

      $num_column = count($bom_fields);

      $csv_header = '';
      for ($i = 0; $i < $num_column; $i++) {
        $csv_header .= '"' . $bom_fields[$i] . '",';
      }
      $csv_header .= "\n";
      fputs($handle, $csv_header);

      fclose($handle);
      return true;
    }

    protected function importMaterial($file)
    {
      $result = true;
      //connect to database
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      if (!$dbc) {
        $result = mysqli_connect_error();
        return $result;
      }

      mysqli_set_charset($dbc, 'utf8');

      //clear all tables on material, firstly.
      $mytables = array("boms_materials", "boms", "modules", "projects", "materials");
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

      //import material csv file
      $fp = fopen($file, "r");
      if ($fp == false) {
        $result = print_r(error_get_last(), true);
        mysqli_close($dbc);
        return $result;
      }

      $isheader = true;
      $number = 0;

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

        $zc_part_no = mysqli_real_escape_string($dbc, trim($data[0]));
        $part_no = mysqli_real_escape_string($dbc, trim($data[1]));
        $part_name = mysqli_real_escape_string($dbc, trim($data[2]));
        $description = mysqli_real_escape_string($dbc, trim($data[3]));
        $unit = mysqli_real_escape_string($dbc, trim($data[4]));
        $pcb_footprint = mysqli_real_escape_string($dbc, trim($data[5]));
        $supplier_name = mysqli_real_escape_string($dbc, trim($data[6]));
        $part = mysqli_real_escape_string($dbc, trim($data[7]));
        $vehicle_standard = mysqli_real_escape_string($dbc, trim($data[8]));
        $remark = mysqli_real_escape_string($dbc, trim($data[9]));

        //if there are invalid fields that should not be null
        if (empty($part_no) || empty($description) || empty($supplier_name)) {
          $my_error = "item $number has invalid fields that should not be empty.";
          $success = false;
          break;
        }

        //if the supplier_name has been inserted into brand_list 
        $query = "SELECT id FROM brand_list where brand = '$supplier_name'";
        $result = mysqli_query($dbc, $query);
        if ($result == false) {
          $success = false;
          break;
        }
        if (mysqli_num_rows($result) == 0) {
          $my_error = "supplier_name $supplier_name is invalid.";
          $success = false;
          break;
        }
        $row = mysqli_fetch_row($result);
        $supplier_name_id = $row[0];

        //get part_id if part is not null
        if (!empty($part)) {
          $query = "SELECT id FROM detail_list where name = '$part'";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }
          if (mysqli_num_rows($result) == 0) {
            $my_error = "part $part is invalid.";
            $success = false;
            break;
          }
          $row = mysqli_fetch_row($result);
          $part_id = $row[0];
        }

        /*Check if the part_no has been inserted into materials table,
          if not, insert the item into table,
          otherwise, prompt the part_no is duplicated.*/
        $query = "SELECT part_no FROM materials where part_no = '$part_no'";
        $result = mysqli_query($dbc, $query);
        if ($result == false) {
          $success = false;
          break;
        }
        if (mysqli_num_rows($result) == 0) {
          if (empty($part)) {
            $query = "INSERT INTO materials (zc_part_no, part_no, part_name, description,
              unit, pcb_footprint, supplier_name_id, vehicle_standard, remark)
                VALUES ('$zc_part_no', '$part_no', '$part_name', '$description',
                    '$unit', '$pcb_footprint', '$supplier_name_id', '$vehicle_standard', '$remark')";

          } else {
            $query = "INSERT INTO materials (zc_part_no, part_no, part_name, description,
              unit, pcb_footprint, supplier_name_id, part_id, vehicle_standard, remark)
                VALUES ('$zc_part_no', '$part_no', '$part_name', '$description',
                    '$unit', '$pcb_footprint', '$supplier_name_id', '$part_id', '$vehicle_standard', '$remark')";
          }
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          } 
        } else {
          $my_error = "The part_no $part_no in item $number is duplicated, please check.";
          $success = false;
          break;
        }
      }
      if ($success) {
        mysqli_commit($dbc);
        $result = Yii::t('common', 'Importing {number} items from material file successfully.', ['number' => $number]);
      } else {
        $result = ($my_error == '') ? mysqli_error($dbc) : $my_error;
        mysqli_rollback($dbc);
        $result .= "<br>The importing has been canceled.";
      }
      //close the class file
      fclose($fp);

      //close database connection
      mysqli_close($dbc);

      return $result;
    }

    protected function ImportBom($file) {
      //connect to database
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      if (!$dbc) {
        $result = mysqli_connect_error();
        return $result;
      }

      mysqli_set_charset($dbc, 'utf8');

      //clear all tables on material, firstly.
      $mytables = array("boms_materials","boms","modules", "projects");
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

      //import bom csv file
      $fp = fopen($file, "r");
      if ($fp == false) {
        $result = print_r(error_get_last(), true);
        mysqli_close($dbc);
        return $result;
      }

      //start to import the boms file
      $isheader = true;
      $number = 0;
      $last_projectname = '';
      $last_modulename = '';
      $last_milestone = '';

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

        $project_name = mysqli_real_escape_string($dbc, trim($data[0]));
        $module_name = mysqli_real_escape_string($dbc, trim($data[1]));
        $category = mysqli_real_escape_string($dbc, trim($data[2]));
        $milestone = mysqli_real_escape_string($dbc, trim($data[3]));
        $produce_qty = mysqli_real_escape_string($dbc, trim($data[4]));
        $assy_level = mysqli_real_escape_string($dbc, trim($data[5]));
        $purchase_level = mysqli_real_escape_string($dbc, trim($data[6]));
        $qty = mysqli_real_escape_string($dbc, trim($data[7]));
        $reference_no = mysqli_real_escape_string($dbc, trim($data[8]));
        $remark = mysqli_real_escape_string($dbc, trim($data[9]));
        $partNo = mysqli_real_escape_string($dbc, trim($data[10]));
        $secondPartNo = mysqli_real_escape_string($dbc, trim($data[11]));

        //if there are invalid fields that should not be null
        if (empty($project_name) || empty($module_name) || empty($category) ||
            empty($milestone) || empty($partNo)) {
          $my_error = "item $number has invalid fields that should not be empty.";
          $success = false;
          break;
        }

        //insert new project name into projects
        if ($project_name != $last_projectname) {
          $query = "SELECT project_id, name FROM projects where name = '$project_name'";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }
          if (mysqli_num_rows($result) == 0) {
            $query = "INSERT INTO projects (name) VALUES ('$project_name')";
            $result = mysqli_query($dbc, $query);
            if ($result == false) {
              $success = false;
              break;
            }
            $query = "SELECT project_id, name FROM projects where name = '$project_name'";
            $result = mysqli_query($dbc, $query);
            if ($result == false) {
              $success = false;
              break;
            }
          }
          $row = mysqli_fetch_row($result);
          $project_id = $row[0];
        }

        //insert new modules into modules
        if (($module_name != $last_modulename) || ($milestone != $last_milestone)) { //&&改为|| by syb at 2017-03-27
          $query = "SELECT module_id, name, milestone FROM modules 
            where name = '$module_name' AND milestone = '$milestone'";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }
          if (mysqli_num_rows($result) == 0) {
            $query = "INSERT INTO modules (project_id, name, category, milestone, produce_qty)
              VALUES ('$project_id', '$module_name', '$category', '$milestone', '$produce_qty')";
            $result = mysqli_query($dbc, $query);
            if ($result == false) {
              $success = false;
              break;
            }
            $query = "SELECT module_id, name, milestone FROM modules 
              where name = '$module_name' AND milestone = '$milestone'";
            $result = mysqli_query($dbc, $query);
            if ($result == false) {
              $success = false;
              break;
            }
          }
          $row = mysqli_fetch_row($result);
          $module_id = $row[0];
          $last_modulename = $module_name;
          $last_milestone = $milestone;
        }

        //if the material part_no has been inserted into materials
        $query = "SELECT material_id FROM materials where part_no = '$partNo'";
        $result = mysqli_query($dbc, $query);
        if ($result == false) {
          $success = false;
          break;
        }
        if (mysqli_num_rows($result) == 0) {
          $my_error = "material part_no $partNo is invalid.";
          $success = false;
          break;
        }
        $row = mysqli_fetch_row($result);
        $material_id = $row[0];
        //if the first part_no has been inserted into boms_materials;
        $query = "SELECT bom_id FROM boms INNER JOIN boms_materials using (bom_id)
          where module_id = '$module_id' AND material_id = '$material_id' AND supplier_priority = 1";
        $result = mysqli_query($dbc, $query);
        if ($result == false) {
          $success = false;
          break;
        }
        if (mysqli_num_rows($result) != 0) {
          $my_error = "the material part_no $partNo has been inserted into this bom.";
          $success = false;
          break;
        }
        //if the ref_no has been inserted into this bom
        if (!empty($reference_no)) {
          $query = "SELECT bom_id FROM boms 
            where module_id = '$module_id' AND ref_no = '$reference_no'";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }
          if (mysqli_num_rows($result) != 0) {
            $my_error = "the ref_no $reference_no has been inserted into this bom.";
            $success = false;
            break;
          }
        }

        //insert the item into boms
        $query = "INSERT INTO boms (module_id, assy_level, purchase_level, qty, ref_no, remark) 
          VALUES ('$module_id', '$assy_level', '$purchase_level', '$qty', '$reference_no', '$remark')";
        $result = mysqli_query($dbc, $query);
        if ($result == false) {
          $success = false;
          break;
        }
        $bom_id = mysqli_insert_id($dbc);

        //insert the item into boms_materials
        $query = "INSERT INTO boms_materials (bom_id, material_id, supplier_priority)  
          VALUES ('$bom_id', '$material_id', 1)";
        $result = mysqli_query($dbc, $query);
        if ($result == false) {
          $success = false;
          break;
        }

        //if the second part_no is available
        if (!empty($secondPartNo)) {
          $query = "SELECT material_id FROM materials where part_no = '$secondPartNo'";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }
          if (mysqli_num_rows($result) == 0) {
            $my_error = "material second part_no $secondPartNo is invalid.";
            $success = false;
            break;
          }
          $row = mysqli_fetch_row($result);
          $second_material_id = $row[0];
          //insert the item into boms_materials
          $query = "INSERT INTO boms_materials (bom_id, material_id, supplier_priority)  
            VALUES ('$bom_id', '$second_material_id ', 2)";
          $result = mysqli_query($dbc, $query);
          if ($result == false) {
            $success = false;
            break;
          }
        }
      }
      if ($success) {
        mysqli_commit($dbc);
        $result = Yii::t('common', 'Importing {number} items from bom file successfully.', ['number' => $number]);
      } else {
        $result = ($my_error == '') ? mysqli_error($dbc) : $my_error;
        mysqli_rollback($dbc);
        $result .= "<br>The importing has been canceled.";
      }

      //close the class file
      fclose($fp);

      //close database connection
      mysqli_close($dbc);
      
      return $result;
    }
}
