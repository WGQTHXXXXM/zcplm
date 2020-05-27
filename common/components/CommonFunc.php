<?php

namespace common\components;

use Yii;


class CommonFunc
{
    /**
     *  @DESC 数据导
     *  @notice 解决了上面导出列数过多的问题
     *  @example
     *  $data = [1, "小明", "25"];
     *  $header = ["id", "姓名", "年龄"];
     *  Myhelpers::exportData($data, $header);
     *  @return void, Browser direct output
     */
    public static function exportData($data, $header, $title = "simple", $filename = "data")
    {
        //require relation class files
        require(Yii::getAlias("@common")."/components/phpexcel/PHPExcel.php");
        require(Yii::getAlias("@common")."/components/phpexcel/PHPExcel/Writer/Excel2007.php");
        if (!is_array ($data) || !is_array ($header)) return false;
        $objPHPExcel = new \PHPExcel();
        // Set properties
        $objPHPExcel->getProperties()->setCreator("ZhicheAuto PLM");
        $objPHPExcel->getProperties()->setLastModifiedBy("ZhicheAuto PLM");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Document");
        $objPHPExcel->getProperties()->setDescription("Document for Office 2007 XLSX, generated using PHP classes.");
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        //添加头部
        $hk = 0;
        foreach ($header as $key => $row)
        {
            if ($row['header']) { //如$v为空，舍弃  -- syb add --
                $colum = \PHPExcel_Cell::stringFromColumnIndex($hk);
                $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum."1", $row['header']);

                $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setWidth($row['width']);
                $objPHPExcel->getActiveSheet()->getStyle($colum."1")->getFill()
                    ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF66CCCC');

                $hk += 1;
            }
        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach($data as $key => $rows)  //行写入
        {
            $span = 0;
            foreach($rows as $keyName => $value) // 列写入
            {
                $j = \PHPExcel_Cell::stringFromColumnIndex($span);
                $objActSheet->setCellValue($j.$column, $value);
                $span++;
            }
            $column++;
        }
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle($title);
        // Save Excel 2007 file
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        header("Pragma:public");
        header("Content-Type:application/vnd.ms-excel;name=\"{$filename}.xlsx\"");
        header("Content-Disposition: attachment;filename=\"{$filename}.xlsx\"");
        $objWriter->save("php://output");
    }

    /**
     * 生成随机字符串
     * @param int $len：要生成的字符串长度
     * @return string：返回生成的字符串
     */
    public static function genRandomString($len) {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count ( $chars ) - 1;
        shuffle ( $chars ); // 将数组打乱
        $output = "";
        for($i = 0; $i < $len; $i ++) {
            $output .= $chars [mt_rand ( 0, $charsLen )];
        }
        return $output;
    }

    //发的是哪种邮件//任务通过，任务退回，      任务审批通知，      plm升级群发。       任务指派
    const APPROVE_PASS=0,APPROVE_REJECT=1,APPROVE_NOTICE=3,PLM_UPGRADE_NOTICE=4,TASK_CHANGE_USER=5;

    /**
     * @param $typeMail:邮件类型（是通过的，还是通知的，还是拒绝的）
     * @param $arrMailAddr：群发的邮箱地址
     * @param $taskType：任务类型（新建物料啊，还是更新ECR啥的）
     * @param $taskName:任务的名称或编号（物料名啊，ECR编号啊。。。）
     * @param null $link：跳转的链接
     */
    public static function sendMail($typeMail,$arrMailAddr,$taskType,$taskName,$link=null,$taskUser=null)
    {
        return;
        $objCompose = Yii::$app->mailer->compose();
        if($typeMail == self::APPROVE_PASS) {//通过的
            $objCompose = Yii::$app->mailer->compose(['html' => 'approvePass-html'],
                ['taskType'=>$taskType,'taskName'=>$taskName]);
        } else if($typeMail == self::APPROVE_REJECT) {//拒绝的
            //通知作者被退
            Yii::$app->mailer->compose(['html' => 'approveReject-html'],
                ['taskType'=>$taskType,'taskName'=>$taskName,'author'=>1,'link'=>$link])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                ->setTo($arrMailAddr['author'])
                ->setSubject('通知——来自' . Yii::$app->name)
                ->send();
            //通知其它人不用再审批了
            unset($arrMailAddr['author']);
            $objCompose = Yii::$app->mailer->compose(['html' => 'approveReject-html'],
                ['taskType'=>$taskType,'taskName'=>$taskName,'author'=>0,'link'=>$link]);
        } else if($typeMail == self::APPROVE_NOTICE) {//通知审批的
            $objCompose = Yii::$app->mailer->compose(['html' => 'noticeApprove-html'],
                ['taskType'=>$taskType,'taskName'=>$taskName,'link'=>$link,'taskUser'=>$taskUser]);
        } else if($typeMail == self::PLM_UPGRADE_NOTICE){//系统升级通知
            $objCompose = Yii::$app->mailer->compose(['html' => 'noticePlmUpgrade'],['content'=>$taskType]);
        }else if($typeMail == self::TASK_CHANGE_USER){
            $objCompose = Yii::$app->mailer->compose(['html' => 'taskChangeUser'],['taskType'=>$taskType,'taskName'=>$taskName]);
        }

        //发送
        $objCompose->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($arrMailAddr)->setSubject('通知——来自' . Yii::$app->name)->send();
    }
}