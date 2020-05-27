<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=substr(strrchr($attachmentSubPath,'/'),1);?></title>
    <script type="text/javascript" src="<?php echo Url::base(true)?>/statics/js/jquery.js" ></script>
    <script type="text/javascript" src="<?php echo Url::base(true)?>/statics/js/jqueryui.js"></script>
    <script type="text/javascript" src="<?php echo Url::base(true)?>/statics/js/jquery.mousewheel.min.js" ></script>
    <script type="text/javascript" src="<?php echo Url::base(true)?>/statics/js/jquery.iviewer.js" ></script>
    <script type="text/javascript">
        var $ = jQuery;
        $(document).ready(function(){
            var iviewer = {};
            $("#viewer2").iviewer(
                {
                    src: "<?=Url::base(true).$attachmentSubPath;?>",
                    initCallback: function()
                    {
                        iviewer = this;
                    }
                });
            $("#opt1").click(function()
            {
                iviewer.loadImage("IMG_8755.jpg");
                return false;
            });
        });
        function option(num)
        {
            $("#viewer2").html("");
            var iviewer = {};
            $("#viewer2").iviewer(
                {
                    src: "test_image.jpg",
                    initCallback: function()
                    {
                        iviewer = this;
                    }
                });
        }
        function option2(num)
        {
            iviewer.loadImage("IMG_8755.JPG");
            return false;
        }
    </script>
    <link rel="stylesheet" href="<?php echo Url::base(true)?>/css/jquery.iviewer.css" />
    <style>
        .viewer
        {
            width: 95%;
            height: auto;
            border: 1px solid black;
            position: relative;
            margin:auto;
        }

        .wrapper
        {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div id="viewer2" class="viewer iviewer_cursor"></div>
    <br />
</div>
<script>
    //让高度为客户端的95%;
    var oDiv = document.getElementsByClassName('viewer')[0];
    oDiv.style.height = document.documentElement.clientHeight*0.95 + 'px';

    window.addEventListener("beforeunload", function(event) {
        $.ajax({
            url: "delfile?path=<?=$attachmentSubPath;?>"
        });
        event.returnValue = "";
    });

</script>

</body>
</html>
