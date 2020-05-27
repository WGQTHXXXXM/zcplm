<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use mdm\admin\models\User;

/**
 * This is the model class for table "boms".
 *
 * @property integer $id
 * @property string $boms_parent_id
 * @property string $child_id
 * @property string $bom_expire_date
 * @property integer $qty
 * @property string $ref_no
 * @property string $unit
 * @property string $zc_part_number2_id
 * @property string $zc_part_number3_id
 * @property string $zc_part_number4_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class BomsChild extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'boms_child';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['boms_parent_id', 'child_id', 'bom_expire_date', 'zc_part_number2_id', 'zc_part_number3_id', 'zc_part_number4_id', 'created_at', 'updated_at'], 'integer'],
            [['ref_no'], 'string', 'max' => 4000],
            [['qty'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('bom', 'ID'),
            'boms_parent_id' => Yii::t('bom', 'Part ID'),
            'child_id' => Yii::t('bom', 'Child ID'),
            'bom_expire_date' => Yii::t('bom', 'Expire Date'),
            'qty' => Yii::t('bom', 'Qty'),
            'ref_no' => Yii::t('bom', 'Reference No.'),
            'zc_part_number2_id' => Yii::t('material', 'Second Zhiche Part Number ID'),
            'zc_part_number3_id' => Yii::t('material', 'third Zhiche Part Number ID'),
            'zc_part_number4_id' => Yii::t('material', 'fourth Zhiche Part Number ID'),
            'created_at' => Yii::t('bom', 'Created At'),
            'updated_at' => Yii::t('bom', 'Updated At'),
            'serial_number' => Yii::t('bom', 'Serial Number'),
            'name' => Yii::t('material', 'Description'),
            'creater' => Yii::t('bom', 'Creater'),
            'zc_part_number' => Yii::t('material', 'Zhiche Part Number'),
        ];
    }

    public function getMaterial()
    {
        return $this->hasOne(Materials::className(), ['material_id' => 'parent_id']);
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'creater_id']);
    }


    //正向查询
    public static function forwardQuery($material_id, $multiLevel = true, $returnData = true)
    {
        //分层查找法
        //1.创建临时表tmp_boms1和临时表tmp_boms2
        $sql = "DROP TABLE IF EXISTS `tmp_boms1`;
                CREATE TEMPORARY TABLE `tmp_boms1` ( 
                  `rownum` int(11) NOT NULL AUTO_INCREMENT COMMENT '当前行号', 
                  `id` int(11) NOT NULL, 
                  `level` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当前层次', 
                  `parent_id` int(11) unsigned DEFAULT NULL COMMENT '父项编码id', 
                  `parent_version` int(10) unsigned DEFAULT NULL COMMENT '父版本', 
                  `child_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '子项编码id', 
                  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态', 
                  `pv_release_time` int(11) DEFAULT NULL COMMENT '发布时间', 
                  `pv_effect_date` bigint(11) DEFAULT NULL COMMENT '生效日期', 
                  `pv_expire_date` bigint(11) DEFAULT NULL COMMENT '失效日期', 
                  `bom_expire_date` bigint(11) DEFAULT NULL COMMENT 'bom信息（料号，数量，位号，二三四供）归属上级料号的失效日期', 
                  `qty` float(7,3) DEFAULT NULL COMMENT '数量', 
                  `ref_no` varchar(4000) DEFAULT NULL COMMENT '位号', 
                  `zc_part_number2_id` int(11) unsigned DEFAULT NULL COMMENT '智车料号（2）id', 
                  `zc_part_number3_id` int(11) unsigned DEFAULT NULL COMMENT '智车料号（3）id', 
                  `zc_part_number4_id` int(11) unsigned DEFAULT NULL COMMENT '智车料号（4）id', 
                  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'BOM分类', 
                  `creater_id` int(11) unsigned DEFAULT NULL COMMENT '创建者id', 
                  `created_at` int(11) DEFAULT NULL COMMENT '创建时间', 
                  `updated_at` int(11) DEFAULT NULL COMMENT '更新时间', 
                  `real_material` int(11) DEFAULT NULL COMMENT '真实物料', 
                  PRIMARY KEY (`rownum`) 
                ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
                
                DROP TABLE IF EXISTS `tmp_boms2`;
                CREATE TEMPORARY TABLE `tmp_boms2` ( 
                  `id` int(11) NOT NULL AUTO_INCREMENT, 
                  `level` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当前层次', 
                  `parent_id` int(11) unsigned DEFAULT NULL COMMENT '父项编码id', 
                  `parent_version` int(10) unsigned DEFAULT NULL COMMENT '父版本', 
                  `child_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '子项编码id', 
                  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态', 
                  `pv_release_time` int(11) DEFAULT NULL COMMENT '发布时间', 
                  `pv_effect_date` bigint(11) DEFAULT NULL COMMENT '生效日期', 
                  `pv_expire_date` bigint(11) DEFAULT NULL COMMENT '失效日期', 
                  `bom_expire_date` bigint(11) DEFAULT NULL COMMENT 'bom信息（料号，数量，位号，二三四供）归属上级料号的失效日期', 
                  `qty` float(7,3) DEFAULT NULL COMMENT '数量', 
                  `ref_no` varchar(4000) DEFAULT NULL COMMENT '位号', 
                  `zc_part_number2_id` int(11) unsigned DEFAULT NULL COMMENT '智车料号（2）id', 
                  `zc_part_number3_id` int(11) unsigned DEFAULT NULL COMMENT '智车料号（3）id', 
                  `zc_part_number4_id` int(11) unsigned DEFAULT NULL COMMENT '智车料号（4）id', 
                  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'BOM分类', 
                  `creater_id` int(11) unsigned DEFAULT NULL COMMENT '创建者id', 
                  `created_at` int(11) DEFAULT NULL COMMENT '创建时间', 
                  `updated_at` int(11) DEFAULT NULL COMMENT '更新时间', 
                  `real_material` int(11) DEFAULT NULL COMMENT '真实物料', 
                 PRIMARY KEY (`id`) 
                ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
        Yii::$app->db->createCommand($sql)->execute();
        //2.将要查找的物料放到表tmp_boms1中
        $level = 0;
        $sql = "INSERT INTO tmp_boms1 (level,parent_id,parent_version,child_id,status,pv_release_time,pv_effect_date,pv_expire_date,bom_expire_date, 
                    zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,type,creater_id,created_at,updated_at,real_material) 
                    SELECT {$level},null,null,real_material,status,pv_release_time,pv_effect_date,pv_expire_date,pv_expire_date, 
                    null,null,null,type,creater_id,created_at,updated_at,real_material 
                    FROM boms_parent WHERE real_material={$material_id}";
        Yii::$app->db->createCommand($sql)->execute();
        $RowCount1 = 0;
        $RowCount2 = Yii::$app->db->createCommand('SELECT COUNT(*) FROM tmp_boms1')->queryScalar(); //表tmp_boms1的总行数

        $tempArr = [];//防止一个bom在不同的bom下出现

        while ($RowCount2 > $RowCount1) {
            $level = $level + 1;
            if ($level > 1 && !$multiLevel) break; //如果只查找最近一层子级，当$level>1时退出查找
            //3.将表tmp_boms1中所有行号大于RowCount1物料的下一层子项放到表tmp_boms2中
            for ($i=$RowCount1+1; $i<=$RowCount2; $i++) {
                /*   $sql = "SELECT * FROM tmp_boms1";
                   $data = Yii::$app->db->createCommand($sql)->queryAll();*/
                $sql = "SELECT * FROM tmp_boms1 WHERE rownum={$i}";
                $row = Yii::$app->db->createCommand($sql)->queryOne();
                //根据child_id和bom_expire_date查boms_parent表获得符合条件的最新版本数据，然后根据版本数据在boms_parent联合boms_child中查出其下一子级的bom
                $sql = "SELECT * FROM boms_parent WHERE real_material={$row['real_material']} AND pv_effect_date<{$row['bom_expire_date']} ORDER BY parent_version DESC";
                $row = Yii::$app->db->createCommand($sql)->queryAll();
                //bug:防止一个bom在多个bom下出现
                if ($row&&array_search($row[0]['real_material'],$tempArr)===false) {
                    $tempArr[] = $row[0]['real_material'];
                    $row = $row[0];
                    $sql = "INSERT INTO tmp_boms2 (level,parent_id,parent_version,child_id,status,pv_release_time,pv_effect_date,pv_expire_date,bom_expire_date,qty,
                    ref_no,zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,type,creater_id,created_at,updated_at,real_material) 
                    SELECT {$level},".$row['real_material'].",parent_version,child_id,status,pv_release_time,pv_effect_date,pv_expire_date,bom_expire_date,qty,
                    ref_no,zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,type,creater_id,boms_child.created_at,boms_child.updated_at,child_id 
                    FROM boms_parent INNER JOIN boms_child ON boms_parent.id=boms_child.boms_parent_id AND boms_parent.parent_id={$row['parent_id']} 
                    AND bom_expire_date>={$row['pv_expire_date']} AND parent_version<={$row['parent_version']}";
                    Yii::$app->db->createCommand($sql)->execute();
//                    var_dump($row['real_material']);
//                    if($row['real_material'] == 3234)
//                    {
//                        var_dump($row);
//                        var_dump(Yii::$app->db->createCommand('select * from tmp_boms2 where parent_id=3234')->queryAll());die;
//                    }
                }

            }

            //4.从表tmp_boms2中将所有物料移到表tmp_boms1末尾
            $sql = "INSERT INTO tmp_boms1 (level,parent_id,parent_version,child_id,status,pv_release_time,pv_effect_date,pv_expire_date,bom_expire_date,qty,
                    ref_no,zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,type,creater_id,created_at,updated_at,real_material) 
                    SELECT level,parent_id,parent_version,child_id,status,pv_release_time,pv_effect_date,pv_expire_date,bom_expire_date,qty,
                    ref_no,zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,type,creater_id,created_at,updated_at,real_material 
                    FROM tmp_boms2";
            Yii::$app->db->createCommand($sql)->execute();
            //5.清空临时表tmp_boms2
            Yii::$app->db->createCommand('DELETE FROM tmp_boms2')->execute();

            $RowCount1 = $RowCount2;
            $RowCount2 = Yii::$app->db->createCommand('SELECT COUNT(*) FROM tmp_boms1')->queryScalar(); //表tmp_boms1的总行数
        }

        //6.表tmp_boms1中存在的是原物料和查找结果
        if ($returnData) {
            $sql = "SELECT b.level, b.parent_id, b.parent_version, b.child_id, b.status, b.pv_release_time, b.pv_effect_date, b.pv_expire_date, b.bom_expire_date, b.qty, b.ref_no, 
                 m1.zc_part_number, m1.purchase_level, m1.part_name, m1.description, m1.unit, m1.pcb_footprint, m1.mfr_part_number, mer1.name AS manufacturer, real_material,
                 b.zc_part_number2_id, m2.zc_part_number AS zc_part_number2, m2.mfr_part_number AS mfr_part_number2, mer2.name AS manufacturer2, 
                 b.zc_part_number3_id, m3.zc_part_number AS zc_part_number3, m3.mfr_part_number AS mfr_part_number3, mer3.name AS manufacturer3, 
                 b.zc_part_number4_id, m4.zc_part_number AS zc_part_number4, m4.mfr_part_number AS mfr_part_number4, mer4.name AS manufacturer4 
                 FROM tmp_boms1 AS b 
                 LEFT JOIN materials AS m1 ON b.real_material=m1.material_id LEFT JOIN material_encode_rule AS mer1 ON mer1.id=m1.manufacturer 
                 LEFT JOIN materials AS m2 ON b.zc_part_number2_id=m2.material_id LEFT JOIN material_encode_rule AS mer2 ON mer2.id=m2.manufacturer 
                 LEFT JOIN materials AS m3 ON b.zc_part_number3_id=m3.material_id LEFT JOIN material_encode_rule AS mer3 ON mer3.id=m3.manufacturer 
                 LEFT JOIN materials AS m4 ON b.zc_part_number4_id=m4.material_id LEFT JOIN material_encode_rule AS mer4 ON mer4.id=m4.manufacturer";
            $data = Yii::$app->db->createCommand($sql)->queryAll();
            return $data;
        }
    }



    //遍历多维数组，生成树型结构数组
    public static function generateTreeArrayRev($arr, $parent = null)
    {
        $pages = Array();
        foreach ($arr as $page)
        {
            if ($page['parent_id'] == $parent)
            {
                $page['children'] = isset($page['children']) ? $page['children'] : self::generateTreeArrayRev($arr, $page['child_id']);
                $pages[] = $page;
            }
        }
        return $pages;
    }


    //遍历多维数组，生成树型结构数组
    public static function generateTreeArray($arr, $parent,$level)
    {
        $pages = Array();

        foreach ($arr as $page)
        {
            if ($page['parent_id'] == $parent&&$level==$page['level'])
            {
                $page['children'] = isset($page['children']) ? $page['children'] : self::generateTreeArray($arr, $page['child_id'], $page['level']+1);
                $pages[] = $page;
            }
        }
        return $pages;
    }

    //遍历树型结构数组，生成列表结构数组
    public static function generateListArray($tree, $children = 'children')
    {
        $listArr = array();
        foreach ($tree as $w) {
            if (isset($w[$children])) {
                $t = $w[$children];
                unset($w[$children]);
                $listArr[] = $w;
                if (is_array($t)) $listArr = array_merge($listArr, self::generateListArray($t, $children));
            } else {
                $listArr[] = $w;
            }
        }
        return $listArr;
    }

    //逆向查询
    public static function reverseQuery($material_id, $multiLevel = true, $returnData = true)
    {
        //分层查找法
        //1.创建临时表tmp_boms1和临时表tmp_boms2
        $sql = "DROP TABLE IF EXISTS `tmp_boms1`;
                CREATE TEMPORARY TABLE `tmp_boms1` ( 
                  `rownum` int(11) NOT NULL AUTO_INCREMENT COMMENT '当前行号', 
                  `id` int(11) NOT NULL, 
                  `level` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当前层次', 
                  `parent_id` int(11) unsigned DEFAULT NULL COMMENT '父项编码id', 
                  `parent_version` int(10) unsigned DEFAULT NULL COMMENT '父版本', 
                  `child_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '子项编码id', 
                  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态', 
                  `pv_release_time` int(11) DEFAULT NULL COMMENT '发布时间', 
                  `pv_effect_date` bigint(11) DEFAULT NULL COMMENT '生效日期', 
                  `pv_expire_date` bigint(11) DEFAULT NULL COMMENT '失效日期', 
                  `bom_expire_date` bigint(11) DEFAULT NULL COMMENT 'bom信息（料号，数量，位号，二三四供）归属上级料号的失效日期', 
                  `qty` float(7,3) DEFAULT NULL COMMENT '数量', 
                  `ref_no` varchar(4000) DEFAULT NULL COMMENT '位号', 
                  `zc_part_number2_id` int(11) unsigned DEFAULT NULL COMMENT '智车料号（2）id', 
                  `zc_part_number3_id` int(11) unsigned DEFAULT NULL COMMENT '智车料号（3）id', 
                  `zc_part_number4_id` int(11) unsigned DEFAULT NULL COMMENT '智车料号（4）id', 
                  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'BOM分类', 
                  `creater_id` int(11) unsigned DEFAULT NULL COMMENT '创建者id', 
                  `created_at` int(11) DEFAULT NULL COMMENT '创建时间', 
                  `updated_at` int(11) DEFAULT NULL COMMENT '更新时间', 
                  `real_material` int(11) DEFAULT NULL COMMENT '真实物料', 
                  PRIMARY KEY (`rownum`) 
                ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
                
                DROP TABLE IF EXISTS `tmp_boms2`;
                CREATE TEMPORARY TABLE `tmp_boms2` ( 
                  `rownum` int(11) NOT NULL AUTO_INCREMENT COMMENT '当前行号', 
                  `id` int(11) NOT NULL, 
                  `level` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当前层次', 
                  `parent_id` int(11) unsigned DEFAULT NULL COMMENT '父项编码id', 
                  `parent_version` int(10) unsigned DEFAULT NULL COMMENT '父版本', 
                  `child_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '子项编码id', 
                  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态', 
                  `pv_release_time` int(11) DEFAULT NULL COMMENT '发布时间', 
                  `pv_effect_date` bigint(11) DEFAULT NULL COMMENT '生效日期', 
                  `pv_expire_date` bigint(11) DEFAULT NULL COMMENT '失效日期', 
                  `bom_expire_date` bigint(11) DEFAULT NULL COMMENT 'bom信息（料号，数量，位号，二三四供）归属上级料号的失效日期', 
                  `qty` float(7,3) DEFAULT NULL COMMENT '数量', 
                  `ref_no` varchar(4000) DEFAULT NULL COMMENT '位号', 
                  `zc_part_number2_id` int(11) unsigned DEFAULT NULL COMMENT '智车料号（2）id', 
                  `zc_part_number3_id` int(11) unsigned DEFAULT NULL COMMENT '智车料号（3）id', 
                  `zc_part_number4_id` int(11) unsigned DEFAULT NULL COMMENT '智车料号（4）id', 
                  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'BOM分类', 
                  `creater_id` int(11) unsigned DEFAULT NULL COMMENT '创建者id', 
                  `created_at` int(11) DEFAULT NULL COMMENT '创建时间', 
                  `updated_at` int(11) DEFAULT NULL COMMENT '更新时间', 
                  `real_material` int(11) DEFAULT NULL COMMENT '真实物料', 
                  PRIMARY KEY (`rownum`) 
                ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
        Yii::$app->db->createCommand($sql)->execute();

        //2.将要查找的物料放到表tmp_boms1中
        $level = 0;
        $expire_date = BomsParent::EXPIRE_DATE_MAX;

        $sql = "INSERT INTO tmp_boms1 (level,parent_id,parent_version,child_id,status,pv_release_time,pv_effect_date,pv_expire_date,bom_expire_date,qty,
                    ref_no,zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,type,creater_id,created_at,updated_at,real_material) 
                    SELECT {$level},pbom1.real_material,pbom1.parent_version,child_id,pbom1.status,pbom1.pv_release_time,pbom1.pv_effect_date,pbom1.pv_expire_date,bom_expire_date,qty,
                    ref_no,zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,pbom1.type,pbom1.creater_id,boms_child.created_at,boms_child.updated_at,child_id
                    FROM boms_parent INNER JOIN boms_child ON boms_parent.id=boms_child.boms_parent_id AND boms_child.child_id={$material_id} 
                    AND boms_child.bom_expire_date={$expire_date} inner join boms_parent as pbom1 on pbom1.parent_id=boms_parent.parent_id and pbom1.status<>-1";

        Yii::$app->db->createCommand($sql)->execute();

        //var_dump(Yii::$app->db->createCommand("SELECT * FROM tmp_boms1")->queryAll());die;

        $RowCount1 = 0;
        $RowCount2 = Yii::$app->db->createCommand('SELECT COUNT(*) FROM tmp_boms1')->queryScalar(); //表tmp_boms1的总行数
        while ($RowCount2 > $RowCount1) {
            $level = $level + 1;
            if ($level > 1 && !$multiLevel) break; //如果只查找直接上级，当$level>1时退出查找
            //3.将表tmp_boms1中所有行号大于RowCount1物料的直接上级放到表tmp_boms2中
            for ($i=$RowCount1+1; $i<=$RowCount2; $i++) {
                $sql = "SELECT * FROM tmp_boms1 WHERE rownum={$i}";
                $row = Yii::$app->db->createCommand($sql)->queryOne();
                //var_dump($row);die;
                if ($row['parent_id']) {
                    $sql = "SELECT COUNT(*) FROM boms_child WHERE child_id={$row['parent_id']} AND bom_expire_date={$expire_date}";

                    $RowCount = Yii::$app->db->createCommand($sql)->queryScalar(); //表boms_child中符合查询条件的总行数
                    if ($RowCount) {
                        //先找到这个料父bom最新版本的bom表，然后再联合查询
                        $sql = "INSERT INTO tmp_boms2 (level,parent_id,parent_version,child_id,status,pv_release_time,pv_effect_date,pv_expire_date,bom_expire_date,qty,
                        ref_no,zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,type,creater_id,created_at,updated_at,real_material) 
                        SELECT {$level},real_material,parent_version,child_id,status,pv_release_time,pv_effect_date,pv_expire_date,bom_expire_date,qty,ref_no,
                        zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,type,creater_id,tbla.created_at,tbla.updated_at,child_id 
                        FROM `boms_parent` `pbom3` 
                        RIGHT JOIN (SELECT `pbom2`.`parent_id` AS `pid`, max(pbom2.parent_version) as maxVersion,cbom.created_at,cbom.updated_at
                            ,child_id,bom_expire_date,qty,ref_no,zc_part_number2_id,zc_part_number3_id,zc_part_number4_id FROM `boms_child` `cbom` 
                            LEFT JOIN `boms_parent` `pbom1` ON pbom1.id=cbom.boms_parent_id 
                            LEFT JOIN `boms_parent` `pbom2` ON pbom1.parent_id=pbom2.parent_id 
                            WHERE (`cbom`.`child_id`={$row['parent_id']}) AND (`bom_expire_date`={$expire_date}) GROUP BY `pid`) `tbla` 
                        ON pbom3.parent_id=tbla.pid and pbom3.parent_version=tbla.maxVersion";
                    } else {
                        $sql = "INSERT INTO tmp_boms2 (level,parent_id,parent_version,child_id,status,pv_release_time,pv_effect_date,pv_expire_date,bom_expire_date, 
                        zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,type,creater_id,created_at,updated_at,real_material) 
                        SELECT {$level},null,null,real_material,status,pv_release_time,pv_effect_date,pv_expire_date,pv_expire_date, 
                        null,null,null,type,creater_id,created_at,updated_at,real_material 
                        FROM boms_parent WHERE real_material={$row['parent_id']} AND pv_expire_date={$expire_date}";
                    }
                    Yii::$app->db->createCommand($sql)->execute();
                }
            }


            //4.从表tmp_boms2中将所有物料移到表tmp_boms1末尾
            if ($multiLevel) {//全阶逆展
                $parent_id_version = "parent_id,parent_version";
            } else {//单阶逆展，需要将直接上级的parent_id置为null，以便后续处理时不会导致有效信息丢失
                $parent_id_version = "null,null";
            }
            $sql = "INSERT INTO tmp_boms1 (level,parent_id,parent_version,child_id,status,pv_release_time,pv_effect_date,pv_expire_date,bom_expire_date,qty,
                    ref_no,zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,type,creater_id,created_at,updated_at,real_material) 
                    SELECT {$level},". $parent_id_version .",child_id,status,pv_release_time,pv_effect_date,pv_expire_date,bom_expire_date,qty,
                    ref_no,zc_part_number2_id,zc_part_number3_id,zc_part_number4_id,type,creater_id,created_at,updated_at,real_material 
                    FROM tmp_boms2 ORDER BY id ASC";
            Yii::$app->db->createCommand($sql)->execute();
            //5.清空临时表tmp_boms2
            Yii::$app->db->createCommand('DELETE FROM tmp_boms2')->execute();

            $RowCount1 = $RowCount2;
            $RowCount2 = Yii::$app->db->createCommand('SELECT COUNT(*) FROM tmp_boms1')->queryScalar(); //表tmp_boms1的总行数
        }

        //var_dump(Yii::$app->db->createCommand("SELECT * FROM tmp_boms1")->queryAll());die;
        //6.表tmp_boms1中存在的是原物料和查找结果
        if ($returnData) {
            $sql = "SELECT b.level, b.parent_id, b.parent_version, b.child_id, b.status, b.pv_release_time, b.pv_effect_date, b.pv_expire_date, b.bom_expire_date, b.qty, b.ref_no, 
                 m1.zc_part_number, m1.purchase_level, m1.part_name, m1.description, m1.unit, m1.pcb_footprint, m1.mfr_part_number, mer1.name AS manufacturer, real_material,
                 m2.zc_part_number AS zc_part_number2, m2.mfr_part_number AS mfr_part_number2, mer2.name AS manufacturer2, 
                 m3.zc_part_number AS zc_part_number3, m3.mfr_part_number AS mfr_part_number3, mer3.name AS manufacturer3, 
                 m4.zc_part_number AS zc_part_number4, m4.mfr_part_number AS mfr_part_number4, mer4.name AS manufacturer4, CONCAT_WS(',', b.parent_id, b.child_id) AS once 
                 FROM tmp_boms1 AS b 
                 LEFT JOIN materials AS m1 ON b.real_material=m1.material_id LEFT JOIN material_encode_rule AS mer1 ON mer1.id=m1.manufacturer 
                 LEFT JOIN materials AS m2 ON b.zc_part_number2_id=m2.material_id LEFT JOIN material_encode_rule AS mer2 ON mer2.id=m2.manufacturer 
                 LEFT JOIN materials AS m3 ON b.zc_part_number3_id=m3.material_id LEFT JOIN material_encode_rule AS mer3 ON mer3.id=m3.manufacturer 
                 LEFT JOIN materials AS m4 ON b.zc_part_number4_id=m4.material_id LEFT JOIN material_encode_rule AS mer4 ON mer4.id=m4.manufacturer
                 GROUP BY once ORDER BY parent_id";
            $data = Yii::$app->db->createCommand($sql)->queryAll();

            return $data;
        }
    }
}
