<?php
//子朝版用的
    if(!empty($arrTemp))
    {
        echo '<h2>有文件没有审批通过，如下文件有问题：</h2></br>';

        echo '<table>';
        if(!empty($arrTemp['noFile']))
        {
            echo "<tr><th>没上传的文件:</th><th></th></tr>";
            foreach ($arrTemp['noFile'] as $key=>$val)
            {
                $key++;
                echo "<tr><td>&emsp;&emsp;&emsp;$key</td><td>$val</td></tr>";
            }
        }
        if(!empty($arrTemp['noApproval']))
        {
            echo "<tr><th>未审批的文件:</th><th></th></tr>";
            foreach ($arrTemp['noApproval'] as $key=>$val)
            {
                $key++;
                echo "<tr><td>&emsp;&emsp;&emsp;$key</td><td>$val</td></tr>";
            }
        }
        if(!empty($arrTemp['approving']))
        {
            echo "<tr><th>审批中的文件:</th><th></th></tr>";
            foreach ($arrTemp['approving'] as $key=>$val)
            {
                $key++;
                echo "<tr><td>&emsp;&emsp;&emsp;$key</td><td>$val</td></tr>";
            }
        }
        if(!empty($arrTemp['approvalNoPass']))
        {
            echo "<tr><th>审批拒绝的文件:</th><th></th></tr>";
            foreach ($arrTemp['approvalNoPass'] as $key=>$val)
            {
                $key++;
                echo "<tr><td>&emsp;&emsp;&emsp;$key</td><td>$val</td></tr>";
            }
        }
        echo '</table>';
    }
    else
    {
        echo '<h2>文件审批都通过</h2></br>';
    }
?>

