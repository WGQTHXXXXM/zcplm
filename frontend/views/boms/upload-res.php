<?php

$this->title = '导入BOM';
$this->params['breadcrumbs'][] = $this->title;
?>

    <h1><?= $this->title ?></h1>
    <br>
    <br>
    <br>
<?php
if($res == 1)
{
    if(!empty($verifyPos))
    {
        echo '<h3>提示：</h3>';
        echo '<br><br><h4>有两种类型的位置的料号：</h4>';
        echo '';
        foreach ($verifyPos as $key=>$vals)
        {
            foreach ($vals as $val)
                echo "<h5>$key--------->$val</h5>";
        }
        echo '<br><br><a href="upload-bom" class="btn btn-primary">返回</a>&emsp;&emsp;
        <a href="verify-upload?remakr='.$remark.'&status='.$status.'&id='.$id.'" class="btn btn-primary">导入</a>';
    }
}
else if($res == 0)
{
    echo '<h1>上传失败</h1>';
    if(!empty($matchQtyPos))
    {
        echo '<h4>数量和位置不匹配的智车料号：</h4>';
        foreach ($matchQtyPos as $val)
        {
            echo "<h5>$val</h5>";
        }
    }
    if(!empty($emptyZcPartNo))
    {
        echo '<br><br><h4>不存在智车料号的厂家料号：</h4>';
        foreach ($emptyZcPartNo as $val)
        {
            echo "<h5>$val</h5>";
        }
    }
    if(!empty($verifyZcPartNumber))
    {
        echo '<br><br><h4>与系统里的料号不是同一源：</h4>';
        foreach ($verifyZcPartNumber as $key=>$vals)
        {
            foreach ($vals as $val)
                echo "<h5>$key--------->$val</h5>";
        }
    }
    if(!empty($verifyPos))
    {
        echo '<br><br><h4>有两种类型的位置的料号：</h4>';
        foreach ($verifyPos as $key=>$vals)
        {
            foreach ($vals as $val)
                echo "<h5>$key--------->$val</h5>";
        }
    }

    echo '<br><br><br><a href="/boms/upload-bom" class="btn btn-success">返回</a>';

}
else if($res ==2)
{
    echo '成功';
}

?>