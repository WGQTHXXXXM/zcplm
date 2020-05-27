<?php

namespace frontend\controllers;

use frontend\models\Aaa;
use frontend\models\ExtBomsChild;
use frontend\models\ExtBomsParent;
use frontend\models\ImportBomForm;
use frontend\models\MaterialEncodeRule;
use frontend\models\Tasks;
use frontend\models\UserTask;
use Yii;
use frontend\models\BomsParent;
use frontend\models\BomsChild;
use frontend\models\Materials;
use yii\caching\MemCache;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use common\components\CommonFunc;
use yii\base\Model;
use yii\web\UploadedFile;



/**
 * BomsController implements the CRUD actions for BomsChild model.
 */
class BomsController extends Controller
{
    const BOM_UPLOAD = '上传BOM';

    public function actionAaa()
    {
        for($i=1;$i<100;$i++)
        {
            $b=time()-1586547247+$i*10000;
            $this->bb($b);
        }
        var_dump("ok");
    }
    public function bb($i)
    {
        $str="INSERT INTO `aaa` (boms_parent_id,child_id,bom_expire_date,qty,ref_no,
zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,created_at,updated_at) VALUES ";
        $ii = $i+10000;
        while($i<$ii) {
            $b = $i + 1;
            $c = $i + 2;
            $d = $i + 3;
            $e = "NULL";
            $k = $i + 4;
            $j = $i + 5;
            $str .= "($i,$b,$c,$d,$e,$e,$e,$e,$k,$j),";
            $i++;
        }
        $str.="($i,$b,$c,$d,$e,$e,$e,$e,$k,$j);";

        if(!Yii::$app->db->createCommand(trim($str,','))->execute())
        {
            var_dump("n");die;
        }


        var_dump($i);
    }

    /*
     * bom的模板下载
     */
    public function actionDownloadTemplate($id)
    {
        if($id==1)//电子模板
            Yii::$app->response->sendFile('../uploads/bom_template/electron.xls');
        else if($id = 2)//结构模板
            Yii::$app->response->sendFile('../uploads/bom_template/struct.xlsx');
    }

    public function actionVerifyUpload($remakr,$status,$id)
    {
        $transaction = Yii::$app->db->beginTransaction();//开启事务
        $_POST['ImportBomForm']['merId'] = $id;
        $_POST['taskCommit'] = $status; //是否立即提交
        $_POST['taskRemark'] = $remakr;//备注

        $pid = ExtBomsParent::saveBomForUpload();//返回父ID

        $arrData = $_SESSION['upload'];
        foreach ($arrData as $key=>$val)//父id重新赋值
            $arrData[$key][0]=$pid;
        $arrAttr = ['boms_parent_id','child_id','bom_expire_date','qty','ref_no','zc_part_number2_id',
            'zc_part_number3_id','zc_part_number4_id','created_at','updated_at'];
        $res = Yii::$app->db->createCommand()->batchInsert('boms_child', $arrAttr,$arrData)->execute();//返回值为影响行数
        if($res&&Tasks::generateTask(Tasks::TASK_TYPE_BOM_UPLOAD,$pid,$this::BOM_UPLOAD))//如果返回成功并保存任务成功就提交事务
        {
            $transaction->commit();
            return $this->render('upload-res',['res'=>2]);
        }
        else
        {
            $transaction->rollBack();
            return $this->render('upload-res',['res'=>0]);
        }
    }

    /**
     * bom的上传功能
     */
    public function actionUploadBom()
    {
        $model = new ImportBomForm();
        if (Yii::$app->request->isPost)
        {
            $model->bomFile = UploadedFile::getInstance($model, 'bomFile');

            $tmp_file = $model->bomFile->tempName;
            $file_type = $model->bomFile->extension;
            //包含这个文件后，这个文件里有自动加载文件autoload
            require_once (Yii::getAlias("@common")."/components/phpexcel/PHPExcel/Reader/Excel2007.php");

            $reader = new \PHPExcel_Reader_Excel2007();
            if ($file_type == 'xls')
                $reader = \PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)

            //读excel文件
            $PHPExcel = $reader->load($tmp_file,'utf-8'); // 载入excel文件
            $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            //$highestColumm = $sheet->getHighestColumn(); // 取得总列数
            //把Excel数据保存数组中
            $transaction = Yii::$app->db->beginTransaction();//开启事务
            $pid = ExtBomsParent::saveBomForUpload();//返回父ID
            if(!$pid)//如果保存父bom不成功，退出
                return $this->render('upload-res',['res'=>false]);
            //0老版本：老模板的电子BOM。1电子：新模板的电子BOM。2结构：新模板的结构BOM。
            if($_POST['isElecBom'] == 1)//老版本
                return self::uploadOldBom($sheet,$highestRow,$pid,$transaction);
            else if($_POST['isElecBom'] == 0)
                return self::uploadElecBom($sheet,$highestRow,$pid,$transaction);
            else if($_POST['isElecBom'] == 2)
                return self::uploadStructBom($sheet,$highestRow,$pid,$transaction);
        }
        return $this->render('upload-bom',['model'=>$model]);
    }

    /**
     * 上传电子BOM
     */
    public function uploadElecBom($sheet,$highestRow,$pid,$transaction)
    {
        $arrData = [];//要存的数据
        $emptyZcPartNo = [];//不存在的智车料号放这个数组里
        $matchQtyPos =[];//不匹配的数量和位置
        $verifyPos =[];//验证位置
        $verifyZcPartNumber =[];//验证是不是出自同一个源
        $curTime = time();
        for ($rowIndex = 3; $rowIndex <= $highestRow; $rowIndex++) {        //循环读取每个单元格的内容。注意行从1开始，列从A开始
            //boms_child的每一行的数据
            //一供智车料号
            $addr = "D" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            if(empty($cell))
                break;
            $zcPartNo = Materials::findOne(['zc_part_number'=>$cell]);
            if(empty($zcPartNo))//如果没有这个智车料号，要存起来。不上传并打印出来
            {
                $addr = "E" . $rowIndex;
                $cell = $sheet->getCell($addr)->getValue();
                if ($cell instanceof \PHPExcel_RichText)
                    $cell = $cell->__toString();
                $emptyZcPartNo[] = $rowIndex.'---'.$cell;
                continue;
            }
            $arrData[$rowIndex][0] = $pid;//parent_id
            $arrData[$rowIndex][1] = $zcPartNo->material_id;//child_id
            $arrData[$rowIndex][2] = BomsParent::EXPIRE_DATE_MAX;//bom_expire_date
            //数量
            $addr = "H" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            $arrData[$rowIndex][3] = $cell;//qty
            //位置
            $addr = "I" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            if(empty(trim($cell)))//如果为空说明是结构的料，验证一下是不是结构的料
            {
                $mdlRule = MaterialEncodeRule::findOne($zcPartNo->part_type);
                $mdlRule = $mdlRule->getFather()->getFather()->getFather();
                if($mdlRule->id!=612&&$mdlRule->id!=442)//如果不是结构的认为位置与数量 不对
                    $matchQtyPos[] = $rowIndex.'行：'.$zcPartNo->zc_part_number;
            }
            else //否则是电子料
            {
                if(substr_count($cell,',')!=$arrData[$rowIndex][3]-1)//如果数量不准报错
                    $matchQtyPos[] = $rowIndex.'行：'.$zcPartNo->zc_part_number;
                //验证位置的格式是否正确
                $arrPos = explode($cell,',');
                $strPos = $arrPos[0][0];
                foreach ($arrPos as $val)
                {
                    if($strPos!=$val[0])
                        $verifyPos[] = $zcPartNo->zc_part_number;
                    $strPos = $val[0];
                }
            }
            $arrData[$rowIndex][5] = null;//zc_part_number2_id
            $arrData[$rowIndex][6] = null;//zc_part_number3_id
            $arrData[$rowIndex][7] = null;//zc_part_number4_id
            //二供
            $addr = "K" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            if(!empty(trim($cell)))
            {
                $mdlMtr2Id = Materials::findOne(['zc_part_number'=>$cell])->material_id;
                $arrData[$rowIndex][5] = $mdlMtr2Id;
            }
            //三供
            $addr = "N" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            if(!empty(trim($cell)))
            {
                $mdlMtr3Id = Materials::findOne(['zc_part_number'=>$cell])->material_id;
                $arrData[$rowIndex][6] = $mdlMtr3Id;
            }
            $arrData[$rowIndex][4] = $cell;//ref_no
            $arrData[$rowIndex][8] = $curTime;//created_at
            $arrData[$rowIndex][9] = $curTime;//updated_at
        }
        //如果有错误就不上传
        if(!empty($matchQtyPos)||!empty($emptyZcPartNo))
        {
            $transaction->rollBack();
            return $this->render('upload-res',['res'=>0,'matchQtyPos'=>$matchQtyPos,'emptyZcPartNo'=>$emptyZcPartNo]);
        }

        //把excel的数据批量的导入数据库(返回值是改变行数)
        $arrAttr = ['boms_parent_id','child_id','bom_expire_date','qty','ref_no','zc_part_number2_id',
            'zc_part_number3_id','zc_part_number4_id','created_at','updated_at'];
        $res = Yii::$app->db->createCommand()->batchInsert('boms_child', $arrAttr,$arrData)->execute();//返回值为影响行数
        if($res&&Tasks::generateTask(Tasks::TASK_TYPE_BOM_UPLOAD,$pid,$this::BOM_UPLOAD))//如果返回成功并保存任务成功就提交事务
        {
            $transaction->commit();
            return $this->render('upload-res',['res'=>1]);
        }
        else
        {
            $transaction->rollBack();
            return $this->render('upload-res',['res'=>0]);
        }

    }

    /**
     * 上传结构BOM
     */
    public function uploadStructBom($sheet,$highestRow,$pid,$transaction)
    {
        $arrData = [];//要存的数据
        $emptyZcPartNo = [];//不存在的智车料号放这个数组里
        $matchQtyPos =[];//不匹配的数量和位置
        $verifyZcPartNumber =[];//验证是不是出自同一个源
        $arrManufacturer = [];//存临时的厂家对应的厂家号

        $curTime = time();
        for ($rowIndex = 4; $rowIndex <= $highestRow; $rowIndex++) {        //循环读取每个单元格的内容。注意行从1开始，列从A开始
            //boms_child的每一行的数据
            //一供智车料号
            $addr = "I" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            if(empty($cell))
                break;
            $zcPartNo = Materials::findOne(['zc_part_number'=>$cell]);
            if(empty($zcPartNo))//如果没有这个智车料号，要存起来。不上传并打印出来
            {
                $emptyZcPartNo[] = $rowIndex.'行：'.$cell;
                continue;
            }
            $arrData[$rowIndex][0] = $pid;//parent_id
            $arrData[$rowIndex][1] = $zcPartNo->material_id;//child_id
            $arrData[$rowIndex][2] = BomsParent::EXPIRE_DATE_MAX;//bom_expire_date
            //数量
            $addr = "L" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            $arrData[$rowIndex][3] = $cell;//qty
            $arrData[$rowIndex][4] = '';//ref_no
            //验证是否出自同一个源
            $arrField = ['manufacturer'=>'N','part_name'=>'J','description'=>'K','unit'=>'M'];
            foreach ($arrField as $key=>$val)
            {
                $addr = $val . $rowIndex;
                $cell = $sheet->getCell($addr)->getValue();
                if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                    $cell = $cell->__toString();
                }
                if($val == 'N')//如果是厂家，要看厂家号对不对
                {
                    if(trim($cell) == 'ZHICHE')//认为是PCBA或PCB
                        break;
                    if(trim($cell) == '')//如果excel表的厂为空
                    {
                        $verifyZcPartNumber[$rowIndex.'行：'.$zcPartNo->zc_part_number][]='manufacturer';
                        continue;
                    }
                    if(in_array($cell,$arrManufacturer))//如果临时的厂家数组里有就不去查
                    {
                        if(!in_array($zcPartNo->manufacturer,$arrManufacturer[$cell]))
                            $verifyZcPartNumber[$rowIndex.'行：'.$zcPartNo->zc_part_number][]='manufacturer';
                    }
                    else//如果临时的厂家数组里没有就去查并放到厂家数组里
                    {
                        $arrManufacturer[$cell] = MaterialEncodeRule::find()->where(['name'=>$cell])
                            ->select('id')->column();
                        if(!in_array($zcPartNo->manufacturer,$arrManufacturer[$cell]))
                            $verifyZcPartNumber[$rowIndex.'行：'.$zcPartNo->zc_part_number][]='manufacturer';
                    }
                    continue;
                }
                if(trim($zcPartNo->$key)!=trim($cell))
                {
//                    var_dump($zcPartNo->$key);
//                    var_dump($cell);die;
                    $verifyZcPartNumber[$rowIndex.'行：'.$zcPartNo->zc_part_number][]=$key;
                }
            }


            ////二三四供
            $arrData[$rowIndex][5] = null;//zc_part_number2_id
            $arrData[$rowIndex][6] = null;//zc_part_number3_id
            $arrData[$rowIndex][7] = null;//zc_part_number4_id
            //二供
            $addr = "O" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            if(!empty(trim($cell)))
            {
                $mdlMtr2Id = Materials::findOne(['zc_part_number'=>$cell]);
                if(empty($mdlMtr2Id))
                    $emptyZcPartNo[] = $rowIndex.'行：'.$cell.'--二供料没有';
                else{
                    //二供厂家检查
                    $addr = "P" . $rowIndex;
                    $cell = $sheet->getCell($addr)->getValue();
                    if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                        $cell = $cell->__toString();
                    }
                    $isSucc = true;
                    if(trim($cell) == 'ZHICHE')//认为是PCBA或PCB
                    {
                        $isSucc = false;
                    }
                    if($isSucc==true&&trim($cell) == '')//如果excel表的厂为空
                    {
                        $verifyZcPartNumber[$rowIndex.'行：'.$zcPartNo->zc_part_number][]='manufacturer';
                        $isSucc = false;
                    }
                    if($isSucc == true){
                        if(in_array($cell,$arrManufacturer))//如果临时的厂家数组里有就不去查
                        {
                            if(!in_array($zcPartNo->manufacturer,$arrManufacturer[$cell])){
                                $verifyZcPartNumber[$rowIndex.'行：'.$zcPartNo->zc_part_number][]='manufacturer';
                                $isSucc = false;
                            }
                        }
                        else//如果临时的厂家数组里没有就去查并放到厂家数组里
                        {
                            $arrManufacturer[$cell] = MaterialEncodeRule::find()->where(['name'=>$cell])
                                ->select('id')->column();
                            if(!in_array($zcPartNo->manufacturer,$arrManufacturer[$cell])){
                                $verifyZcPartNumber[$rowIndex.'行：'.$zcPartNo->zc_part_number][]='manufacturer';
                                $isSucc = false;
                            }
                        }
                    }
                    if ($isSucc == true)
                        $arrData[$rowIndex][5] = $mdlMtr2Id->material_id;
                }
            }
            $arrData[$rowIndex][8] = $curTime;//created_at
            $arrData[$rowIndex][9] = $curTime;//updated_at
        }
        //如果有错误就不上传
        if(!empty($matchQtyPos)||!empty($emptyZcPartNo||!empty($verifyZcPartNumber)))
        {
            $transaction->rollBack();
            return $this->render('upload-res',
                [
                    'res'=>0,
                    'matchQtyPos'=>$matchQtyPos,
                    'emptyZcPartNo'=>$emptyZcPartNo,
                    'verifyZcPartNumber'=>$verifyZcPartNumber,
                ]);
        }

        //把excel的数据批量的导入数据库(返回值是改变行数)
        $arrAttr = ['boms_parent_id','child_id','bom_expire_date','qty','ref_no','zc_part_number2_id',
            'zc_part_number3_id','zc_part_number4_id','created_at','updated_at'];
        $res = Yii::$app->db->createCommand()->batchInsert('boms_child', $arrAttr,$arrData)->execute();//返回值为影响行数
        if($res&&Tasks::generateTask(Tasks::TASK_TYPE_BOM_UPLOAD,$pid,$this::BOM_UPLOAD))//如果返回成功并保存任务成功就提交事务
        {
            $transaction->commit();
            return $this->render('upload-res',['res'=>2]);
        }
        else
        {
            $transaction->rollBack();
            return $this->render('upload-res',['res'=>0]);
        }
    }

    /**
     * 上传老版本的BOM
     */
    public function uploadOldBom($sheet,$highestRow,$pid,$transaction)
    {
        $arrData = [];//要存的数据
        $emptyZcPartNo = [];//不存在的智车料号放这个数组里
        $matchQtyPos =[];//不匹配的数量和位置
        $verifyPos =[];//验证位置
        $verifyZcPartNumber =[];//验证是不是出自同一个源
        $arrManufacturer = [];//存临时的厂家对应的厂家号

        $curTime = time();
        for ($rowIndex = 5; $rowIndex <= $highestRow; $rowIndex++) {        //循环读取每个单元格的内容。注意行从1开始，列从A开始
            //boms_child的每一行的数据
            //一供智车料号
            $addr = "I" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            if(empty($cell))
                break;
            $zcPartNo = Materials::findOne(['zc_part_number'=>$cell]);
            if(empty($zcPartNo))//如果没有这个智车料号，要存起来。不上传并打印出来
            {
                $addr = "J" . $rowIndex;
                $cell = $sheet->getCell($addr)->getValue();
                if ($cell instanceof \PHPExcel_RichText)
                    $cell = $cell->__toString();
                $emptyZcPartNo[] = $rowIndex.'行：'.$cell;
                continue;
            }
            $arrData[$rowIndex][0] = $pid;//parent_id
            $arrData[$rowIndex][1] = $zcPartNo->material_id;//child_id
            $arrData[$rowIndex][2] = BomsParent::EXPIRE_DATE_MAX;//bom_expire_date
            //数量
            $addr = "M" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            $arrData[$rowIndex][3] = $cell;//qty
            //位置
            $addr = "N" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            $arrData[$rowIndex][4] = $cell;//ref_no
            if(empty(trim($cell)))//如果为空说明是结构的料，验证一下是不是结构的料
            {
                $mdlRule = MaterialEncodeRule::findOne($zcPartNo->part_type);
                $mdlRule = $mdlRule->getFather()->getFather()->getFather();
                if($mdlRule->id!=612&&$mdlRule->id!=442)//如果不是结构的认为位置与数量 不对
                    $matchQtyPos[] = $rowIndex.'行：'.$zcPartNo->zc_part_number;

            }
            else //否则是电子料
            {//             厂家号,描述,封装,厂家
                $arrField = ['mfr_part_number'=>'J','description'=>'K','pcb_footprint'=>'L','manufacturer'=>'O'];
                if(substr_count($cell,',')!=$arrData[$rowIndex][3]-1)//如果数量不准报错
                    $matchQtyPos[] = $rowIndex.'行：'.$zcPartNo->zc_part_number;
                //验证位置的格式是否正确
                $arrPos = explode(',',$cell);
                $strPos = $arrPos[0][0];
                foreach ($arrPos as $val)
                {
                    $val = trim($val);
                    if($strPos!=$val[0])
                    {
                        $verifyPos[$rowIndex.'行：'.$zcPartNo->zc_part_number][] = $val;
                    }
                    $strPos = $val[0];
                }
                //验证是否出自同一个源
                foreach ($arrField as $key=>$val)
                {
                    $addr = $val . $rowIndex;
                    $cell = $sheet->getCell($addr)->getValue();
                    if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                        $cell = $cell->__toString();
                    }
                    if($val == 'O')//如果是厂家，要看厂家号对不对
                    {
                        if(empty(trim($cell))||$cell == 'ZHICHE')//如果是板套板不考虑厂家
                        {
                            $mdlRule = MaterialEncodeRule::findOne($zcPartNo->part_type);
                            $mdlRule = $mdlRule->getFather()->getFather()->getFather();
                            if($mdlRule->id!=442)//如果不是结构的认为位置与数量 不对
                                $verifyZcPartNumber[$rowIndex.'行：'.$zcPartNo->zc_part_number][]='manufacturer';
                            continue;
                        }
                        if(in_array($cell,$arrManufacturer))//如果临时的厂家数组里有就不去查
                        {
                            if(!in_array($zcPartNo->manufacturer,$arrManufacturer[$cell]))
                                $verifyZcPartNumber[$rowIndex.'行：'.$zcPartNo->zc_part_number][]='manufacturer';
                        }
                        else//如果临时的厂家数组里没有就去查并放到厂家数组里
                        {
                            $arrManufacturer[$cell] = MaterialEncodeRule::find()->where(['name'=>$cell])
                                ->select('id')->column();
                            if(!in_array($zcPartNo->manufacturer,$arrManufacturer[$cell]))
                                $verifyZcPartNumber[$rowIndex.'行：'.$zcPartNo->zc_part_number][]='manufacturer';
                        }
                        continue;
                    }
                    if($zcPartNo->$key!=$cell)
                    {
                        $verifyZcPartNumber[$rowIndex.'行：'.$zcPartNo->zc_part_number][]=$key;
                    }
                }


            }
            ////二三四供
            $arrData[$rowIndex][5] = null;//zc_part_number2_id
            $arrData[$rowIndex][6] = null;//zc_part_number3_id
            $arrData[$rowIndex][7] = null;//zc_part_number4_id
            //二供
            $addr = "P" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            $addr = "R" . $rowIndex;
            $cell2 = $sheet->getCell($addr)->getValue();
            if ($cell2 instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell2 = $cell2->__toString();
            }
            $addr = "Q" . $rowIndex;
            $cell3 = $sheet->getCell($addr)->getValue();
            if ($cell3 instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell3 = $cell3->__toString();
            }

            if(!empty(trim($cell))||!empty(trim($cell2))||!empty(trim($cell3)))
            {
                $mdlMtr2Id = Materials::findOne(['zc_part_number'=>$cell]);
                if(empty($mdlMtr2Id))
                {
                    var_dump($mdlMtr2Id);
                    var_dump($cell);die;
                    $emptyZcPartNo[] = $rowIndex.'行：'.$cell.'--二供料没有';
                }
                else{
                    //二供厂家检查
                    $addr = "R" . $rowIndex;
                    $cell = $sheet->getCell($addr)->getValue();
                    if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                        $cell = $cell->__toString();
                    }
                    $isSucc = true;
                    if(empty(trim($cell))||$addr == 'ZHICHE')//如果是板套板不考虑厂家
                    {
                        $mdlRule = MaterialEncodeRule::findOne($mdlMtr2Id->part_type);
                        $mdlRule = $mdlRule->getFather()->getFather()->getFather();
                        if($mdlRule->id!=442)//如果不是结构的认为位置与数量 不对
                        {
                            $isSucc = false;
                            $verifyZcPartNumber[$rowIndex.'行：'.$mdlMtr2Id->zc_part_number][]='manufacturer';
                        }
                    }
                    if ($isSucc == true){
                        if(in_array($cell,$arrManufacturer))//如果临时的厂家数组里有就不去查
                        {
                            if(!in_array($mdlMtr2Id->manufacturer,$arrManufacturer[$cell]))
                            {
                                $isSucc = false;
                                $verifyZcPartNumber[$rowIndex.'行：'.$mdlMtr2Id->zc_part_number][]='manufacturer';
                            }
                        }
                        else//如果临时的厂家数组里没有就去查并放到厂家数组里
                        {
                            $arrManufacturer[$cell] = MaterialEncodeRule::find()->where(['name'=>$cell])
                                ->select('id')->column();
                            if(!in_array($mdlMtr2Id->manufacturer,$arrManufacturer[$cell]))
                            {
                                $isSucc = false;
                                $verifyZcPartNumber[$rowIndex.'行：'.$mdlMtr2Id->zc_part_number][]='manufacturer';
                            }
                        }
                    }
                    //二供厂家料号检查
                    $addr = "Q" . $rowIndex;
                    $cell = $sheet->getCell($addr)->getValue();
                    if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                        $cell = $cell->__toString();
                    }
                    if ($isSucc == true&&$mdlMtr2Id->mfr_part_number!=$cell){
                        $verifyZcPartNumber[$rowIndex.'行：'.$mdlMtr2Id->zc_part_number][]='2mfr_part_number';
                    }
                    if ($isSucc == true)
                        $arrData[$rowIndex][5] = $mdlMtr2Id->material_id;
                }
            }
            //三供
            $addr = "S" . $rowIndex;
            $cell = $sheet->getCell($addr)->getValue();
            if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            if(!empty(trim($cell)))
            {
                $mdlMtr3Id = Materials::findOne(['zc_part_number'=>$cell]);
                if(empty($mdlMtr3Id))
                    $emptyZcPartNo[] = $rowIndex.'行：'.$cell.'--三供料没有';
                else
                {                    //三供厂家检查
                    $addr = "U" . $rowIndex;
                    $cell = $sheet->getCell($addr)->getValue();
                    if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                        $cell = $cell->__toString();
                    }
                    $isSucc = true;
                    if(empty(trim($cell))||$addr == 'ZHICHE')//如果是板套板不考虑厂家
                    {
                        $mdlRule = MaterialEncodeRule::findOne($mdlMtr3Id->part_type);
                        $mdlRule = $mdlRule->getFather()->getFather()->getFather();
                        if($mdlRule->id!=442)//如果不是结构的认为位置与数量 不对
                        {
                            $isSucc = false;
                            $verifyZcPartNumber[$rowIndex.'行：'.$mdlMtr3Id->zc_part_number][]='manufacturer';
                        }
                    }
                    if ($isSucc == true){
                        if(in_array($cell,$arrManufacturer))//如果临时的厂家数组里有就不去查
                        {
                            if(!in_array($mdlMtr3Id->manufacturer,$arrManufacturer[$cell]))
                            {
                                $isSucc = false;
                                $verifyZcPartNumber[$rowIndex.'行：'.$mdlMtr3Id->zc_part_number][]='manufacturer';
                            }
                        }
                        else//如果临时的厂家数组里没有就去查并放到厂家数组里
                        {
                            $arrManufacturer[$cell] = MaterialEncodeRule::find()->where(['name'=>$cell])
                                ->select('id')->column();
                            if(!in_array($mdlMtr3Id->manufacturer,$arrManufacturer[$cell]))
                            {
                                $isSucc = false;
                                $verifyZcPartNumber[$rowIndex.'行：'.$mdlMtr3Id->zc_part_number][]='manufacturer';
                            }
                        }
                    }
                    //三供厂家料号检查
                    $addr = "T" . $rowIndex;
                    $cell = $sheet->getCell($addr)->getValue();
                    if ($cell instanceof \PHPExcel_RichText) { //富文本转换字符串
                        $cell = $cell->__toString();
                    }
                    if ($isSucc == true&&$mdlMtr3Id->mfr_part_number!=$cell){
                        $verifyZcPartNumber[$rowIndex.'行：'.$mdlMtr3Id->zc_part_number][]='3mfr_part_number';
                    }
                    if ($isSucc == true)
                        $arrData[$rowIndex][6] = $mdlMtr3Id->material_id;
                }
            }
            $arrData[$rowIndex][8] = $curTime;//created_at
            $arrData[$rowIndex][9] = $curTime;//updated_at
        }
        //如果有错误就不上传
        if(!empty($matchQtyPos)||!empty($emptyZcPartNo||!empty($verifyZcPartNumber)))
        {
            $transaction->rollBack();
            return $this->render('upload-res',
                [
                    'res'=>0,
                    'matchQtyPos'=>$matchQtyPos,
                    'emptyZcPartNo'=>$emptyZcPartNo,
                    'verifyZcPartNumber'=>$verifyZcPartNumber,
                    'verifyPos'=>$verifyPos
                ]);
        }
        if (!empty($verifyPos))
        {
            $transaction->rollBack();
            $_SESSION['upload'] = $arrData;

            return $this->render('upload-res', ['res'=>1,
                'verifyPos'=>$verifyPos,
                'id'=>$_POST['ImportBomForm']['merId'],
                'status' => $_POST['taskCommit'],//是否立即提交
                'remark' => $_POST['taskRemark'],//备注

            ]);
        }



        //把excel的数据批量的导入数据库(返回值是改变行数)
        $arrAttr = ['boms_parent_id','child_id','bom_expire_date','qty','ref_no','zc_part_number2_id',
            'zc_part_number3_id','zc_part_number4_id','created_at','updated_at'];
        $res = Yii::$app->db->createCommand()->batchInsert('boms_child', $arrAttr,$arrData)->execute();//返回值为影响行数
        if($res&&Tasks::generateTask(Tasks::TASK_TYPE_BOM_UPLOAD,$pid,$this::BOM_UPLOAD))//如果返回成功并保存任务成功就提交事务
        {
            $transaction->commit();
            return $this->render('upload-res',['res'=>2]);
        }
        else
        {
            $transaction->rollBack();
            return $this->render('upload-res',['res'=>0]);
        }
    }

    /**
     * 查看boms_parent的弹框
     */
    public function actionBomParentView($id)
    {
        $model = ExtBomsParent::findOne($id);
        return $this->renderAjax('bom-parent-view',['model'=>$model]);
    }

    /**
     * 查看boms_child的弹框
     */
    public function actionBomChildView($id)
    {
        $model = ExtBomsChild::findOne($id);
        return $this->renderAjax('bom-child-view',['model'=>$model]);
    }

    /**
     * bom上传后的查看
     */
    public function actionUploadView($idUserTask=-1)
    {

        $mdlUserTask = null;
        if($idUserTask != -1)
        {
            $mdlUserTask = UserTask::find()->leftJoin('tasks','user_task.task_id=tasks.id')
                ->select('*,user_task.status as userTaskStatus,tasks.status as taskStatus')
                ->where(['user_task.id'=>$idUserTask])->one();
        }

        return $this->render('upload-view',['mdlUserTask'=>$mdlUserTask]);
    }

    /**
     * 数据
     */
    public function actionUploadDate($pBomId)
    {
        $data = ExtBomsParent::getBomsPart($pBomId);
        //var_dump($data);die;
        return json_encode($data);
    }

    /**
     * bom上传后的更新
     */
    public function actionUploadUpdate()
    {
        return $this->render('upload-update');
    }

    /**
     * 更新上传后的零件
     */
    public function actionUpdatePart()
    {
        $res = BomsChild::updateAll(['child_id'=>$_POST['mtrId'],'qty'=>$_POST['qty'],'ref_no'=>$_POST['pos']],
            ['id'=>$_POST['cRowId']]);
        return json_encode(['status'=>$res,'message'=>'','data'=>'']);

    }

    /**
     * 创建上传后的零件
     */
    public function actionCreatePart()
    {
        $model = new BomsChild();
        $model->boms_parent_id = $_POST['pRowId'];
        $model->child_id = $_POST['mtrId'];
        $model->qty = $_POST['qty'];
        $model->ref_no = $_POST['pos'];
        $model->bom_expire_date = BomsParent::EXPIRE_DATE_MAX;
        if($model->save())
            return json_encode(['status'=>$model->id,'message'=>'','data'=>'']);
        return json_encode(['status'=>0,'message'=>'','data'=>'']);
    }

    /**
     * 删除上传后的零件
     */
    public function actionDeletePart()
    {
        $pid = $_POST['isLvl0'];
        if ($pid != 0)//删除所有上传
        {
            $res = BomsParent::findOne($pid)->delete();
            if($res)
                $res = BomsChild::deleteAll(['boms_parent_id'=>$pid]);
            if ($res)
            {
                $mdlTask = Tasks::findOne(['type'=>Tasks::TASK_TYPE_BOM_UPLOAD,'type_id'=>$pid]);
                $mdlUserTask = UserTask::findOne(['task_id'=>$mdlTask->id]);
                $mdlTask->delete();
                $mdlUserTask->delete();
            }

            return json_encode(['status'=>$res,'message'=>'','data'=>'']);
        }
        if($res = BomsChild::deleteAll(['in','id',$_POST['arrSelect']]))
            return json_encode(['status'=>$res,'message'=>'','data'=>'']);
        else
            return json_encode(['status'=>$res,'message'=>'','data'=>'']);
    }


    /**
     * 获得要上专的bom信息
     */
    public function actionGetBomData()
    {
        $part = $_POST['part'];
        $mdlMtr = Materials::findOne(['zc_part_number'=>$part]);
        if(!empty($mdlMtr))
        {
            $mdlPBom = BomsParent::findOne(['parent_id'=>$mdlMtr->material_id]);
            if(empty($mdlPBom))
                return json_encode(['status'=>1,'message'=>'','data'=>$mdlMtr->toArray()]);
        }
        return json_encode(['status'=>0,'message'=>'','data'=>'']);

    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all BomsChild models.
     * @return mixed
     */
    public function actionIndex($material_id, $forward = true, $multiLevel = true)
    {
        if (Yii::$app->request->isAjax) {
            $data = Array();
            //分层查找法
            if ($forward) {//正向查询
                $model = Materials::findOne($material_id);
                if(empty(BomsParent::findOne(['real_material'=>$material_id]))){
                    $data = array();
                    $data[0]['parent_id'] = null;
                    $data[0]['child_id'] = $material_id;
                    $data[0]['zc_part_number'] = $model->zc_part_number;
                    $data[0]['purchase_level'] = $model->purchase_level;
                    $data[0]['part_name'] = $model->part_name;
                    $data[0]['description'] = $model->description;
                    $data[0]['pcb_footprint'] = $model->pcb_footprint;
                    $data[0]['mfr_part_number'] = $model->mfr_part_number;
                }else{
                    $cache = Yii::$app->cache;
                    $bomDate = $cache->get('bom_'.$model->zc_part_number);
                    if($bomDate){
                        $data = $bomDate;
                    }else{
                        $data = BomsChild::forwardQuery($material_id, true);
                     //遍历多维数组，并在数组项之间建立父子关系，生成树型结构数组
                        $data = BomsChild::generateTreeArray($data,$data[0]['parent_id'],$data[0]['level']);
                        $cache->set('bom_'.$model->zc_part_number,$data,86400);
                    }
                }
            } else {//逆向查询
                $data = BomsChild::reverseQuery($material_id, $multiLevel);

                //遍历多维数组，并在数组项之间建立父子关系，生成树型结构数组
                $data = BomsChild::generateTreeArrayRev($data, $data[0]['parent_id']);
            }
            return json_encode($data);
        }

        $model = BomsParent::find()->where(['real_material' => $material_id])->one();
        if (!$model) {
            $model = new BomsParent();
            $model->real_material = $material_id;
            $model->parent_id = $material_id;
        }

        //获得选定料号的版本列表
        $versionList = BomsParent::find()->leftJoin('materials','materials.material_id=boms_parent.real_material')
            ->select('boms_parent.parent_version as parent_version,boms_parent.real_material as real_material,materials.zc_part_number as zc_part_number')
            ->distinct()->where(['boms_parent.parent_id' => $model->parent_id])->orderBy('parent_version')->asArray()->all();
        return $this->render('index', [
            'model' => $model,
            'versionList' => Json::encode($versionList),
            'forward' => $forward,
            'multiLevel' => $multiLevel,
        ]);
    }

    public function actionCompare()
    {
        $models[] = new BomsParent();
        $models[] = new BomsParent();

        //查出有子级的所有物料数组
        $parent_code = BomsParent::find()->select(['boms_parent.parent_id', 'materials.zc_part_number'])
            ->distinct('parent_id')->innerJoinWith('material')->orderBy('materials.zc_part_number')->asArray()->all();


        return $this->render('compare', [
            'model' => $models,
            'parent_code' => $parent_code,
        ]);
    }

    public function actionCompareView()
    {
        $models[] = new BomsParent();
        $models[] = new BomsParent();
        if (Model::loadMultiple($models, Yii::$app->request->post()) && Model::validateMultiple($models)) {
            $data = array();
            foreach ($models as $k => $model) {
                //只查询单级Bom
                $data[] = BomsChild::forwardQuery($model->real_material, false);

                /*
                //遍历多维数组，并在数组项之间建立父子关系，生成树型结构数组
                $data[$k] = Boms::generateTreeArray($data[$k], $data[$k][0]['parent_id']);
                //遍历树型结构数组，生成列表结构数组
                $data[$k] = Boms::generateListArray($data[$k], 'children');
                */
                //删除不用字段

                foreach ($data[$k] as $key=>$val) {
                    unset($data[$k][$key]['level']);
                    unset($data[$k][$key]['id']);
                    unset($data[$k][$key]['parent_id']);
                    unset($data[$k][$key]['parent_version']);
                    unset($data[$k][$key]['child_version']);
                    unset($data[$k][$key]['status']);
                    unset($data[$k][$key]['release_time']);
                    unset($data[$k][$key]['effect_date']);
                    unset($data[$k][$key]['expire_date']);
                 //   unset($data[$k][$key]['mfr_part_number']);
                    unset($data[$k][$key]['manufacturer']);
                    unset($data[$k][$key]['mfr_part_number2']);
                    unset($data[$k][$key]['manufacturer2']);
                    unset($data[$k][$key]['mfr_part_number3']);
                    unset($data[$k][$key]['manufacturer3']);
                    unset($data[$k][$key]['mfr_part_number4']);
                    unset($data[$k][$key]['manufacturer4']);
                    unset($data[$k][$key]['purchase_level']);
                    unset($data[$k][$key]['part_name']);
                    unset($data[$k][$key]['description']);
                    unset($data[$k][$key]['unit']);
                    unset($data[$k][$key]['pcb_footprint']);
                    unset($data[$k][$key]['pv_release_time']);
                    unset($data[$k][$key]['pv_effect_date']);
                    unset($data[$k][$key]['pv_expire_date']);
                    unset($data[$k][$key]['bom_expire_date']);
                }
                $models[$k]->zc_part_number = $data[$k][0]['zc_part_number'];
                //数组中的第一个元素是比较bom的料号及版本信息，删除
                array_shift($data[$k]);
            }
            //比较bom1相对于bom2的差集
            $arr_bom1diffbom2 = array_udiff($data[0], $data[1], function ($a, $b) {
                if ($a == $b) {
                    return 0;
                }
                return ($a > $b) ? 1 : -1;
            });
            //比较bom2相对于bom1的差集
            $arr_bom2diffbom1 = array_udiff($data[1], $data[0], function ($a, $b) {
                if ($a == $b) {
                    return 0;
                }
                return ($a > $b) ? 1 : -1;
            });
            //
            //            var_dump($arr_bom1diffbom2);
            //             var_dump($arr_bom2diffbom1);
            foreach ($arr_bom1diffbom2 as $k1 => $v1) {
                foreach ($arr_bom2diffbom1 as $k2 => $v2) {
                    if ($v1['zc_part_number'] == $v2['zc_part_number']) {//zc_part_number相同时，如其它项也相同，不显示
                        if ($v1['qty'] == $v2['qty']) {
                            $arr_bom1diffbom2[$k1]['qty'] = null;
                            $arr_bom2diffbom1[$k2]['qty'] = null;
                        }
                        if ($v1['ref_no'] == $v2['ref_no']) {
                            $arr_bom1diffbom2[$k1]['ref_no'] = null;
                            $arr_bom2diffbom1[$k2]['ref_no'] = null;
                        }
                        if ($v1['zc_part_number2'] == $v2['zc_part_number2']) {
                            $arr_bom1diffbom2[$k1]['zc_part_number2_id'] = null;
                            $arr_bom1diffbom2[$k1]['zc_part_number2'] = null;
                            $arr_bom2diffbom1[$k2]['zc_part_number2_id'] = null;
                            $arr_bom2diffbom1[$k2]['zc_part_number2'] = null;
                        }
                        if ($v1['zc_part_number3'] == $v2['zc_part_number3']) {
                            $arr_bom1diffbom2[$k1]['zc_part_number3_id'] = null;
                            $arr_bom1diffbom2[$k1]['zc_part_number3'] = null;
                            $arr_bom2diffbom1[$k2]['zc_part_number3_id'] = null;
                            $arr_bom2diffbom1[$k2]['zc_part_number3'] = null;
                        }
                        if ($v1['zc_part_number4'] == $v2['zc_part_number4']) {
                            $arr_bom1diffbom2[$k1]['zc_part_number4_id'] = null;
                            $arr_bom1diffbom2[$k1]['zc_part_number4'] = null;
                            $arr_bom2diffbom1[$k2]['zc_part_number4_id'] = null;
                            $arr_bom2diffbom1[$k2]['zc_part_number4'] = null;
                        }
                        // 将第二个BOM版本的差异列信息放到对应的$arr_bom1diffbom2[$k1]里，用_1标识，以便同行显示
                        $arr_bom1diffbom2[$k1]['zc_part_number_1_id'] = $arr_bom2diffbom1[$k2]['child_id'];
                        $arr_bom1diffbom2[$k1]['zc_part_number_1'] = $arr_bom2diffbom1[$k2]['zc_part_number'];
                        $arr_bom1diffbom2[$k1]['mfr_part_number_1'] = $arr_bom2diffbom1[$k2]['mfr_part_number'];
                        $arr_bom1diffbom2[$k1]['qty_1'] = $arr_bom2diffbom1[$k2]['qty'];
                        $arr_bom1diffbom2[$k1]['ref_no_1'] = $arr_bom2diffbom1[$k2]['ref_no'];
                        $arr_bom1diffbom2[$k1]['zc_part_number2_1_id'] = $arr_bom2diffbom1[$k2]['zc_part_number2_id'];
                        $arr_bom1diffbom2[$k1]['zc_part_number2_1'] = $arr_bom2diffbom1[$k2]['zc_part_number2'];
                        $arr_bom1diffbom2[$k1]['zc_part_number3_1_id'] = $arr_bom2diffbom1[$k2]['zc_part_number3_id'];
                        $arr_bom1diffbom2[$k1]['zc_part_number3_1'] = $arr_bom2diffbom1[$k2]['zc_part_number3'];
                        $arr_bom1diffbom2[$k1]['zc_part_number4_1_id'] = $arr_bom2diffbom1[$k2]['zc_part_number4_id'];
                        $arr_bom1diffbom2[$k1]['zc_part_number4_1'] = $arr_bom2diffbom1[$k2]['zc_part_number4'];
                        // 从$arr_bom2diffbom1中去除$arr_bom2diffbom1[$k2]
                        unset($arr_bom2diffbom1[$k2]);
                        break;
                    }
                }
            }

            foreach ($arr_bom1diffbom2 as $k1 => $v1) {
                foreach ($arr_bom2diffbom1 as $k2 => $v2) {
                    if ($v1['ref_no'] != null && $v1['ref_no'] == $v2['ref_no']) { //如果位号不空，且相同，则视为替换关系
                        $arr_tmp1 = $arr_bom1diffbom2[$k1];
                        $arr_tmp2 = $arr_bom2diffbom1[$k2];
                        unset($arr_bom1diffbom2[$k1]);
                        unset($arr_bom2diffbom1[$k2]);
                        $arr_bom1diffbom2[$k1]['child_id'] = $arr_tmp1['child_id'];
                        $arr_bom1diffbom2[$k1]['zc_part_number'] = $arr_tmp1['zc_part_number'];
                        $arr_bom1diffbom2[$k1]['mfr_part_number'] = $arr_tmp1['mfr_part_number'];
                        $arr_bom1diffbom2[$k1]['zc_part_number_1_id'] = $arr_bom2diffbom1[$k2]['child_id'] = $arr_tmp2['child_id'];
                        $arr_bom1diffbom2[$k1]['zc_part_number_1'] = $arr_bom2diffbom1[$k2]['zc_part_number'] = $arr_tmp2['zc_part_number'];
                        $arr_bom1diffbom2[$k1]['mfr_part_number_1'] = $arr_bom2diffbom1[$k2]['mfr_part_number'] = $arr_tmp2['mfr_part_number'];
                        if ($v1['zc_part_number2'] != $v2['zc_part_number2']) {
                            $arr_bom1diffbom2[$k1]['zc_part_number2_id'] = $arr_tmp1['zc_part_number2_id'];
                            $arr_bom1diffbom2[$k1]['zc_part_number2'] = $arr_tmp1['zc_part_number2'];
                            $arr_bom1diffbom2[$k1]['zc_part_number2_1_id'] = $arr_bom2diffbom1[$k2]['zc_part_number2_id'] = $arr_tmp2['zc_part_number2_id'];
                            $arr_bom1diffbom2[$k1]['zc_part_number2_1'] = $arr_bom2diffbom1[$k2]['zc_part_number2'] = $arr_tmp2['zc_part_number2'];
                        }
                        if ($v1['zc_part_number3'] != $v2['zc_part_number3']) {
                            $arr_bom1diffbom2[$k1]['zc_part_number3_id'] = $arr_tmp1['zc_part_number3_id'];
                            $arr_bom1diffbom2[$k1]['zc_part_number3'] = $arr_tmp1['zc_part_number3'];
                            $arr_bom1diffbom2[$k1]['zc_part_number3_1_id'] = $arr_bom2diffbom1[$k2]['zc_part_number3_id'] = $arr_tmp2['zc_part_number3_id'];
                            $arr_bom1diffbom2[$k1]['zc_part_number3_1'] = $arr_bom2diffbom1[$k2]['zc_part_number3'] = $arr_tmp2['zc_part_number3'];
                        }
                        if ($v1['zc_part_number4'] != $v2['zc_part_number4']) {
                            $arr_bom1diffbom2[$k1]['zc_part_number4_id'] = $arr_tmp1['zc_part_number4_id'];
                            $arr_bom1diffbom2[$k1]['zc_part_number4'] = $arr_tmp1['zc_part_number4'];
                            $arr_bom1diffbom2[$k1]['zc_part_number4_1_id'] = $arr_bom2diffbom1[$k2]['zc_part_number4_id'] = $arr_tmp2['zc_part_number4_id'];
                            $arr_bom1diffbom2[$k1]['zc_part_number4_1'] = $arr_bom2diffbom1[$k2]['zc_part_number4'] = $arr_tmp2['zc_part_number4'];
                        }
                        // 从$arr_bom2diffbom1中去除$arr_bom2diffbom1[$k2]
                        unset($arr_bom2diffbom1[$k2]);
                        break;
                    }
                }
            }

            // 将第二个BOM版本中去除zc_part_number相同及替换关系之后剩余的差异列信息放到$arr_bom1diffbom2数组的末尾，用_1标识
            foreach ($arr_bom2diffbom1 as $k => $v) {
                $data = array();
                $data['zc_part_number_1_id'] = $arr_bom2diffbom1[$k]['child_id'];
                $data['zc_part_number_1'] = $arr_bom2diffbom1[$k]['zc_part_number'];
                $data['mfr_part_number_1'] = $arr_bom2diffbom1[$k]['mfr_part_number'];
                $data['qty_1'] = $arr_bom2diffbom1[$k]['qty'];
                $data['ref_no_1'] = $arr_bom2diffbom1[$k]['ref_no'];
                $data['zc_part_number2_1_id'] = $arr_bom2diffbom1[$k]['zc_part_number2_id'];
                $data['zc_part_number2_1'] = $arr_bom2diffbom1[$k]['zc_part_number2'];
                $data['zc_part_number3_1_id'] = $arr_bom2diffbom1[$k]['zc_part_number3_id'];
                $data['zc_part_number3_1'] = $arr_bom2diffbom1[$k]['zc_part_number3'];
                $data['zc_part_number4_1_id'] = $arr_bom2diffbom1[$k]['zc_part_number4_id'];
                $data['zc_part_number4_1'] = $arr_bom2diffbom1[$k]['zc_part_number4'];

                array_push($arr_bom1diffbom2, $data);
            }
            // 给$arr_bom1diffbom2中的第二个BOM版本列开始处加一列行序号
            $count = 1;
            foreach ($arr_bom1diffbom2 as $k => $v) {
                $arr_bom1diffbom2[$k]['#'] = $count;
                $count++;
                //位置比较，去重
                if(!empty($arr_bom1diffbom2[$k]['ref_no'])&&!empty($arr_bom1diffbom2[$k]['ref_no_1'])){
                    $arrTemp1 = explode(',',$arr_bom1diffbom2[$k]['ref_no']);
                    $arrTemp2 = explode(',',$arr_bom1diffbom2[$k]['ref_no_1']);
                    $arr_bom1diffbom2[$k]['ref_no'] = implode(',',array_diff($arrTemp1,$arrTemp2));
                    $arr_bom1diffbom2[$k]['ref_no_1'] = implode(',',array_diff($arrTemp2,$arrTemp1));
                }
            }
             //  var_dump($arr_bom1diffbom2);die;
             //  var_dump($arr_bom2diffbom1);
            $provider_bom1diffbom2 = new ArrayDataProvider([
                'allModels' => $arr_bom1diffbom2,
                'pagination' => [
                    'pageSize' => 10000,
                ],
            ]);
         /*$provider_bom2diffbom1 = new ArrayDataProvider([
                'allModels' => $arr_bom2diffbom1,
                'pagination' => [
                    'pageSize' => 10000,
                ],
            ]);*/

            //var_dump($provider_bom1diffbom2);die;
            return $this->render('compare-view', [
                'models' => $models,
                'provider_bom1diffbom2' => $provider_bom1diffbom2,
             //   'provider_bom2diffbom1' => $provider_bom2diffbom1,
            ]);
        }
    }



    public function actionDownloadCsv()
    {
        $models[] = new BomsParent();
        $models[] = new BomsParent();
        $models[0]->real_material = $_GET['pid1'];
        $models[1]->real_material = $_GET['pid2'];
        $data = array();
        foreach ($models as $k => $model) {
            //只查询单级Bom
            $data[] = BomsChild::forwardQuery($model->real_material, false);

            /*
            //遍历多维数组，并在数组项之间建立父子关系，生成树型结构数组
            $data[$k] = Boms::generateTreeArray($data[$k], $data[$k][0]['parent_id']);
            //遍历树型结构数组，生成列表结构数组
            $data[$k] = Boms::generateListArray($data[$k], 'children');
            */
            //删除不用字段

            foreach ($data[$k] as $key=>$val) {
                unset($data[$k][$key]['level']);
                unset($data[$k][$key]['id']);
                unset($data[$k][$key]['parent_id']);
                unset($data[$k][$key]['parent_version']);
                unset($data[$k][$key]['child_version']);
                unset($data[$k][$key]['status']);
                unset($data[$k][$key]['release_time']);
                unset($data[$k][$key]['effect_date']);
                unset($data[$k][$key]['expire_date']);
                //   unset($data[$k][$key]['mfr_part_number']);
                unset($data[$k][$key]['manufacturer']);
                unset($data[$k][$key]['mfr_part_number2']);
                unset($data[$k][$key]['manufacturer2']);
                unset($data[$k][$key]['mfr_part_number3']);
                unset($data[$k][$key]['manufacturer3']);
                unset($data[$k][$key]['mfr_part_number4']);
                unset($data[$k][$key]['manufacturer4']);
                unset($data[$k][$key]['purchase_level']);
                unset($data[$k][$key]['part_name']);
                unset($data[$k][$key]['description']);
                unset($data[$k][$key]['unit']);
                unset($data[$k][$key]['pcb_footprint']);
                unset($data[$k][$key]['pv_release_time']);
                unset($data[$k][$key]['pv_effect_date']);
                unset($data[$k][$key]['pv_expire_date']);
                unset($data[$k][$key]['bom_expire_date']);
            }
            $models[$k]->zc_part_number = $data[$k][0]['zc_part_number'];
            //数组中的第一个元素是比较bom的料号及版本信息，删除
            array_shift($data[$k]);
        }
        //比较bom1相对于bom2的差集
        $arr_bom1diffbom2 = array_udiff($data[0], $data[1], function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a > $b) ? 1 : -1;
        });
        //比较bom2相对于bom1的差集
        $arr_bom2diffbom1 = array_udiff($data[1], $data[0], function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a > $b) ? 1 : -1;
        });
        //
        //            var_dump($arr_bom1diffbom2);
        //             var_dump($arr_bom2diffbom1);
        foreach ($arr_bom1diffbom2 as $k1 => $v1) {
            foreach ($arr_bom2diffbom1 as $k2 => $v2) {
                if ($v1['zc_part_number'] == $v2['zc_part_number']) {//zc_part_number相同时，如其它项也相同，不显示
                    if ($v1['qty'] == $v2['qty']) {
                        $arr_bom1diffbom2[$k1]['qty'] = null;
                        $arr_bom2diffbom1[$k2]['qty'] = null;
                    }
                    if ($v1['ref_no'] == $v2['ref_no']) {
                        $arr_bom1diffbom2[$k1]['ref_no'] = null;
                        $arr_bom2diffbom1[$k2]['ref_no'] = null;
                    }
                    if ($v1['zc_part_number2'] == $v2['zc_part_number2']) {
                        $arr_bom1diffbom2[$k1]['zc_part_number2_id'] = null;
                        $arr_bom1diffbom2[$k1]['zc_part_number2'] = null;
                        $arr_bom2diffbom1[$k2]['zc_part_number2_id'] = null;
                        $arr_bom2diffbom1[$k2]['zc_part_number2'] = null;
                    }
                    if ($v1['zc_part_number3'] == $v2['zc_part_number3']) {
                        $arr_bom1diffbom2[$k1]['zc_part_number3_id'] = null;
                        $arr_bom1diffbom2[$k1]['zc_part_number3'] = null;
                        $arr_bom2diffbom1[$k2]['zc_part_number3_id'] = null;
                        $arr_bom2diffbom1[$k2]['zc_part_number3'] = null;
                    }
                    if ($v1['zc_part_number4'] == $v2['zc_part_number4']) {
                        $arr_bom1diffbom2[$k1]['zc_part_number4_id'] = null;
                        $arr_bom1diffbom2[$k1]['zc_part_number4'] = null;
                        $arr_bom2diffbom1[$k2]['zc_part_number4_id'] = null;
                        $arr_bom2diffbom1[$k2]['zc_part_number4'] = null;
                    }
                    // 将第二个BOM版本的差异列信息放到对应的$arr_bom1diffbom2[$k1]里，用_1标识，以便同行显示
                    $arr_bom1diffbom2[$k1]['zc_part_number_1_id'] = $arr_bom2diffbom1[$k2]['child_id'];
                    $arr_bom1diffbom2[$k1]['zc_part_number_1'] = $arr_bom2diffbom1[$k2]['zc_part_number'];
                    $arr_bom1diffbom2[$k1]['mfr_part_number_1'] = $arr_bom2diffbom1[$k2]['mfr_part_number'];
                    $arr_bom1diffbom2[$k1]['qty_1'] = $arr_bom2diffbom1[$k2]['qty'];
                    $arr_bom1diffbom2[$k1]['ref_no_1'] = $arr_bom2diffbom1[$k2]['ref_no'];
                    $arr_bom1diffbom2[$k1]['zc_part_number2_1_id'] = $arr_bom2diffbom1[$k2]['zc_part_number2_id'];
                    $arr_bom1diffbom2[$k1]['zc_part_number2_1'] = $arr_bom2diffbom1[$k2]['zc_part_number2'];
                    $arr_bom1diffbom2[$k1]['zc_part_number3_1_id'] = $arr_bom2diffbom1[$k2]['zc_part_number3_id'];
                    $arr_bom1diffbom2[$k1]['zc_part_number3_1'] = $arr_bom2diffbom1[$k2]['zc_part_number3'];
                    $arr_bom1diffbom2[$k1]['zc_part_number4_1_id'] = $arr_bom2diffbom1[$k2]['zc_part_number4_id'];
                    $arr_bom1diffbom2[$k1]['zc_part_number4_1'] = $arr_bom2diffbom1[$k2]['zc_part_number4'];
                    // 从$arr_bom2diffbom1中去除$arr_bom2diffbom1[$k2]
                    unset($arr_bom2diffbom1[$k2]);
                    break;
                }
            }
        }

        foreach ($arr_bom1diffbom2 as $k1 => $v1) {
            foreach ($arr_bom2diffbom1 as $k2 => $v2) {
                if ($v1['ref_no'] != null && $v1['ref_no'] == $v2['ref_no']) { //如果位号不空，且相同，则视为替换关系
                    $arr_tmp1 = $arr_bom1diffbom2[$k1];
                    $arr_tmp2 = $arr_bom2diffbom1[$k2];
                    unset($arr_bom1diffbom2[$k1]);
                    unset($arr_bom2diffbom1[$k2]);
                    $arr_bom1diffbom2[$k1]['child_id'] = $arr_tmp1['child_id'];
                    $arr_bom1diffbom2[$k1]['zc_part_number'] = $arr_tmp1['zc_part_number'];
                    $arr_bom1diffbom2[$k1]['mfr_part_number'] = $arr_tmp1['mfr_part_number'];
                    $arr_bom1diffbom2[$k1]['zc_part_number_1_id'] = $arr_bom2diffbom1[$k2]['child_id'] = $arr_tmp2['child_id'];
                    $arr_bom1diffbom2[$k1]['zc_part_number_1'] = $arr_bom2diffbom1[$k2]['zc_part_number'] = $arr_tmp2['zc_part_number'];
                    $arr_bom1diffbom2[$k1]['mfr_part_number_1'] = $arr_bom2diffbom1[$k2]['mfr_part_number'] = $arr_tmp2['mfr_part_number'];
                    if ($v1['zc_part_number2'] != $v2['zc_part_number2']) {
                        $arr_bom1diffbom2[$k1]['zc_part_number2_id'] = $arr_tmp1['zc_part_number2_id'];
                        $arr_bom1diffbom2[$k1]['zc_part_number2'] = $arr_tmp1['zc_part_number2'];
                        $arr_bom1diffbom2[$k1]['zc_part_number2_1_id'] = $arr_bom2diffbom1[$k2]['zc_part_number2_id'] = $arr_tmp2['zc_part_number2_id'];
                        $arr_bom1diffbom2[$k1]['zc_part_number2_1'] = $arr_bom2diffbom1[$k2]['zc_part_number2'] = $arr_tmp2['zc_part_number2'];
                    }
                    if ($v1['zc_part_number3'] != $v2['zc_part_number3']) {
                        $arr_bom1diffbom2[$k1]['zc_part_number3_id'] = $arr_tmp1['zc_part_number3_id'];
                        $arr_bom1diffbom2[$k1]['zc_part_number3'] = $arr_tmp1['zc_part_number3'];
                        $arr_bom1diffbom2[$k1]['zc_part_number3_1_id'] = $arr_bom2diffbom1[$k2]['zc_part_number3_id'] = $arr_tmp2['zc_part_number3_id'];
                        $arr_bom1diffbom2[$k1]['zc_part_number3_1'] = $arr_bom2diffbom1[$k2]['zc_part_number3'] = $arr_tmp2['zc_part_number3'];
                    }
                    if ($v1['zc_part_number4'] != $v2['zc_part_number4']) {
                        $arr_bom1diffbom2[$k1]['zc_part_number4_id'] = $arr_tmp1['zc_part_number4_id'];
                        $arr_bom1diffbom2[$k1]['zc_part_number4'] = $arr_tmp1['zc_part_number4'];
                        $arr_bom1diffbom2[$k1]['zc_part_number4_1_id'] = $arr_bom2diffbom1[$k2]['zc_part_number4_id'] = $arr_tmp2['zc_part_number4_id'];
                        $arr_bom1diffbom2[$k1]['zc_part_number4_1'] = $arr_bom2diffbom1[$k2]['zc_part_number4'] = $arr_tmp2['zc_part_number4'];
                    }
                    // 从$arr_bom2diffbom1中去除$arr_bom2diffbom1[$k2]
                    unset($arr_bom2diffbom1[$k2]);
                    break;
                }
            }
        }

        // 将第二个BOM版本中去除zc_part_number相同及替换关系之后剩余的差异列信息放到$arr_bom1diffbom2数组的末尾，用_1标识
        foreach ($arr_bom2diffbom1 as $k => $v) {
            $data = array();
            $data['zc_part_number_1_id'] = $arr_bom2diffbom1[$k]['child_id'];
            $data['zc_part_number_1'] = $arr_bom2diffbom1[$k]['zc_part_number'];
            $data['mfr_part_number_1'] = $arr_bom2diffbom1[$k]['mfr_part_number'];
            $data['qty_1'] = $arr_bom2diffbom1[$k]['qty'];
            $data['ref_no_1'] = $arr_bom2diffbom1[$k]['ref_no'];
            $data['zc_part_number2_1_id'] = $arr_bom2diffbom1[$k]['zc_part_number2_id'];
            $data['zc_part_number2_1'] = $arr_bom2diffbom1[$k]['zc_part_number2'];
            $data['zc_part_number3_1_id'] = $arr_bom2diffbom1[$k]['zc_part_number3_id'];
            $data['zc_part_number3_1'] = $arr_bom2diffbom1[$k]['zc_part_number3'];
            $data['zc_part_number4_1_id'] = $arr_bom2diffbom1[$k]['zc_part_number4_id'];
            $data['zc_part_number4_1'] = $arr_bom2diffbom1[$k]['zc_part_number4'];

            array_push($arr_bom1diffbom2, $data);
        }
        // 给$arr_bom1diffbom2中的第二个BOM版本列开始处加一列行序号
        $count = 1;
        foreach ($arr_bom1diffbom2 as $k => $v) {
            $arr_bom1diffbom2[$k]['#'] = $count;
            $count++;
            //位置比较，去重
            if(!empty($arr_bom1diffbom2[$k]['ref_no'])&&!empty($arr_bom1diffbom2[$k]['ref_no_1'])){
                $arrTemp1 = explode(',',$arr_bom1diffbom2[$k]['ref_no']);
                $arrTemp2 = explode(',',$arr_bom1diffbom2[$k]['ref_no_1']);
                $arr_bom1diffbom2[$k]['ref_no'] = implode(',',array_diff($arrTemp1,$arrTemp2));
                $arr_bom1diffbom2[$k]['ref_no_1'] = implode(',',array_diff($arrTemp2,$arrTemp1));
            }
        }

        $str = "#,一供智车料号,一供厂家料号,数量,位号,二供智车料号,三供智车料号,四供智车料号,华丽的分隔线,".
        "#,一供智车料号,一供厂家料号,数量,位号,二供智车料号,三供智车料号,四供智车料号\n";
        $str = iconv('utf-8','gb2312',$str);
        foreach ($arr_bom1diffbom2 as $value)
        {
            ///////////第一个料号//////////////////
            $str .= $value['#'].',';
            if(!empty($value['zc_part_number']))//一供智车料号和厂家
                $str .= $value['zc_part_number'].','.$value['mfr_part_number'].',';
            else
                $str .= ' , ,';
            //数量和位置
            if(!empty($value['qty']))
                $str .= $value['qty'].',';
            else
                $str .= ' ,';
            if(!empty($value['ref_no']))//位置中的逗号，内容有逗号的得用引号引起来！
                $str .= "\"".$value['ref_no']."\",";//
            else
                $str .= ' ,';
            //二三四供
            if(empty($value['zc_part_number2']))
                $str .= ' ,';
            else
                $str .=$value['zc_part_number2'].',';
            if(empty($value['zc_part_number3']))
                $str .= ' ,';
            else
                $str .=$value['zc_part_number3'].',';
            if(empty($value['zc_part_number4']))
                $str .= ' ,<------>,'.$value['#'].',';
            else
                $str .=$value['zc_part_number4'].',<------>,'.$value['#'].',';
            //////////第二个料/////////////
            if(!empty($value['zc_part_number_1']))//一供智车料号和厂家
                $str .= $value['zc_part_number_1'].','.$value['mfr_part_number_1'].',';
            else
                $str .= ' , ,';
            //数量和位置
            if(!empty($value['qty_1']))
                $str .= $value['qty_1'].',';
            else
                $str .= ' ,';
            if(!empty($value['ref_no_1']))
                $str .= "\"".$value['ref_no_1']."\",";//
            else
                $str .= ' ,';
            //二三四供
            if(empty($value['zc_part_number2_1']))
                $str .= ' ,';
            else
                $str .=$value['zc_part_number2_1'].',';
            if(empty($value['zc_part_number3_1']))
                $str .= ' ,';
            else
                $str .=$value['zc_part_number3_1'].',';
            if(empty($value['zc_part_number4_1']))
                $str .= " ,\n";
            else
                $str .=$value['zc_part_number4_1'].",\n";
        }

        $filename = date('Ymd').'.csv'; //设置文件名

        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $str;


    }


    /**
     * Finds the Boms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Boms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BomsParent::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @DESC 数据导出
     * @param integer $id
     * @param bool $multiLevel
     */
    public function actionExportData($material_id, $multiLevel = true)
    {
        if (!ExtBomsParent::findOne(['real_material'=>$material_id])) { //如果在boms_parent表中未查到版本，说明该物料没有子级，不进行导出操作
            return $this->redirect(['/boms/index', 'material_id' => $material_id, 'forward' => 1]);
        }
            //分层查找法
        BomsChild::forwardQuery($material_id, $multiLevel, false);
        $sql = "SELECT m1.zc_part_number, b.level, m1.purchase_level, b.parent_id, b.child_id, m1.part_name, m1.description, m1.pcb_footprint, b.qty, m1.unit, b.ref_no, 
                 m1.mfr_part_number, mer1.name AS manufacturer, 
                 m2.zc_part_number AS zc_part_number2, m2.mfr_part_number AS mfr_part_number2, mer2.name AS manufacturer2, 
                 m3.zc_part_number AS zc_part_number3, m3.mfr_part_number AS mfr_part_number3, mer3.name AS manufacturer3, 
                 m4.zc_part_number AS zc_part_number4, m4.mfr_part_number AS mfr_part_number4, mer4.name AS manufacturer4 
                 FROM tmp_boms1 AS b 
                 LEFT JOIN materials AS m1 ON b.child_id=m1.material_id LEFT JOIN material_encode_rule AS mer1 ON mer1.id=m1.manufacturer 
                 LEFT JOIN materials AS m2 ON b.zc_part_number2_id=m2.material_id LEFT JOIN material_encode_rule AS mer2 ON mer2.id=m2.manufacturer 
                 LEFT JOIN materials AS m3 ON b.zc_part_number3_id=m3.material_id LEFT JOIN material_encode_rule AS mer3 ON mer3.id=m3.manufacturer 
                 LEFT JOIN materials AS m4 ON b.zc_part_number4_id=m4.material_id LEFT JOIN material_encode_rule AS mer4 ON mer4.id=m4.manufacturer";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        //遍历多维数组，并在数组项之间建立父子关系，生成树型结构数组
        $data = BomsChild::generateTreeArray($data, $data[0]['parent_id'],$data[0]['level']);
        //遍历树型结构数组，生成列表结构数组
        $data = BomsChild::generateListArray($data, 'children');
        //删除parent_id, child_id字段
        foreach ($data as $key=>$val) {
            unset($data[$key]['parent_id']);
            unset($data[$key]['child_id']);
        }

        if ($data) { // 数据处理
            // 给每一行记录添加Item序号
            for ($item=0; $item<count($data); $item++) {
                array_unshift($data[$item], $item+1);
            }
        }
        $header = [
            ['header'=>"Item", 'width'=>7],
            ['header'=>Yii::t('material', 'Zhiche Part Number'), 'width'=>20],
            ['header'=>Yii::t('bom', 'Assy Level'), 'width'=>8],
            ['header'=>Yii::t('material', 'Purchase Level'), 'width'=>8],
            ['header'=>Yii::t('material', 'Part Name'), 'width'=>20],
            ['header'=>Yii::t('material', 'Description'), 'width'=>35],
            ['header'=>Yii::t('material', 'Pcb Footprint'), 'width'=>15],
            ['header'=>Yii::t('bom', 'Qty'), 'width'=>7],
            ['header'=>Yii::t('material', 'Unit'), 'width'=>7],
            ['header'=>Yii::t('bom', 'Reference No.'), 'width'=>25],
            ['header'=>Yii::t('material', 'Manufacturer Part Number'), 'width'=>20],
            ['header'=>Yii::t('material', 'Manufacturer'), 'width'=>15],
            ['header'=>Yii::t('material', 'Second Zhiche Part Number'), 'width'=>20],
            ['header'=>Yii::t('material', 'Second Manufacturer Part Number'), 'width'=>20],
            ['header'=>Yii::t('bom', 'Second Manufacturer'), 'width'=>15],
            ['header'=>Yii::t('material', 'third Zhiche Part Number'), 'width'=>20],
            ['header'=>Yii::t('material', 'third Manufacturer Part Number'), 'width'=>20],
            ['header'=>Yii::t('bom', 'Third Manufacturer'), 'width'=>15],
            ['header'=>Yii::t('material', 'fourth Zhiche Part Number'), 'width'=>20],
            ['header'=>Yii::t('material', 'fourth Manufacturer Part Number'), 'width'=>20],
            ['header'=>Yii::t('bom', 'Fourth Manufacturer'), 'width'=>15],
        ]; //导出excel的表头
        $model = Materials::findOne($material_id);
        CommonFunc::exportData($data, $header, "Sheet1", $model->zc_part_number);
    }

    public function actionGetVersionListByParentId($parent_id)
    {
        //获得选定料号的版本列表
        $versionList = BomsParent::find()->leftJoin('materials','boms_parent.real_material = materials.material_id')
            ->select(['parent_version','materials.zc_part_number as real_material_part','real_material'])
            ->distinct()->where(['boms_parent.parent_id' => $parent_id])->orderBy('parent_version')->asArray()->all();

        return json_encode(['status'=>1,'message'=>'','data'=>$versionList]);
    }

    public function actionGetVersionByChildId($child_id,$bom_expire_date)
    {
        //获得选定料号在该bom中的有效版本
        $sql = "SELECT max(parent_version) AS parent_version,real_material FROM boms_parent WHERE real_material={$child_id} AND pv_effect_date<{$bom_expire_date}";
        $row = Yii::$app->db->createCommand($sql)->queryOne();
        return json_encode(['status'=>1,'message'=>'','data'=>['real_material'=>$row['real_material']]]);
    }




}
