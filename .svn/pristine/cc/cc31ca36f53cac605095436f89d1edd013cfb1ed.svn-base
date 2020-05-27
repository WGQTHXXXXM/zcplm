<?php
use yii\helpers\Url;
?>
<html><head>
    <meta http-equiv="CONTENT-TYPE" content="text/html; charset=utf-8">
    <title><?=$title;?></title>
</head>
<body>
<center>
    <button id="first">首页</button>
    <button id="pre">上一页</button>
    <span>第</span><span>1</span><span>页</span>
    <button id="next">下一页</button>
    <button id="last">末页</button>
</center><br>
<center><img id="img" src="<?php echo Url::base(true)?>/cache/<?=$attachmentId?>/img0.jpg" alt=""></center>

<script>
    var count = <?=$count; ?>-1;
    var index = 0;
    var oFirst = document.getElementById('first');
    var oPre = document.getElementById('pre');
    var oNext = document.getElementById('next');
    var oLast = document.getElementById('last');
    var oImg = document.getElementById('img');
    var oSpan = document.getElementsByTagName('span')[1];

    oFirst.onclick = function () {
        oImg.src = '<?php echo Url::base(true)?>/cache/<?=$attachmentId?>/img0.jpg';
        index = 0;
        oSpan.innerHTML = index+1;
    }

    oPre.onclick = function () {
        index--;
        if(index<0)
        {
            index = 0;
            alert('已经是首页了');
        }
        oImg.src = '<?php echo Url::base(true)?>/cache/<?=$attachmentId?>/img'+index+'.jpg';
        oSpan.innerHTML = index+1;
    }

    oNext.onclick = function () {
        index++;
        if(index>count)
        {
            alert('已经是尾页了');
            index = count;
        }
        oImg.src = '<?php echo Url::base(true)?>/cache/<?=$attachmentId?>/img'+index+'.jpg';
        oSpan.innerHTML = index+1;
    }

    oLast.onclick = function () {
        oImg.src = '<?php echo Url::base(true)?>/cache/<?=$attachmentId?>/img'+count+'.jpg';
        index = count;
        oSpan.innerHTML = index+1;
    }


</script>

</body></html>