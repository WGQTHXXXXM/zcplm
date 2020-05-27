<?php

$this->title = '导入物料';
$this->params['breadcrumbs'][] = $this->title;
?>

    <h1><?= $this->title ?></h1>
    <br>
    <br>
    <br>
<?php

    if(empty($errorMsg)) {
        echo '<h3>成功</h3>';
    }else{
        foreach ($errorMsg as $key=>$errors){
            echo '<h4>'.$key.'</h4>';
            foreach ($errors as $err){
                echo "<h5>$err</h5>";
            }
        }
    }

?>